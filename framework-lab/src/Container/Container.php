<?php

declare(strict_types=1);

namespace App\Container;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    /** @var array<string, array{concrete: mixed, shared: bool}> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    /**
     * Bind an abstract type to a concrete implementation (not shared).
     */
    public function bind(string $abstract, mixed $concrete = null): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'shared' => false,
        ];
    }

    /**
     * Bind an abstract type to a concrete implementation (shared).
     */
    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'shared' => true,
        ];
    }

    /**
     * Register an existing instance as shared.
     */
    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve an abstract type from the container.
     */
    public function make(string $abstract): object
    {
        // If we already have a shared instance, return it.
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // If it is bound, resolve its concrete.
        if (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];
            $object = $this->resolveConcrete($binding['concrete']);

            if ($binding['shared'] === true) {
                $this->instances[$abstract] = $object;
            }

            return $object;
        }

        // Otherwise, treat it as a class name and try to auto-wire.
        return $this->build($abstract);
    }

    /**
     * Call a callable, resolving its parameters from the container.
     * Similar idea to Laravel's container->call().
     */
    public function call(callable $callable, array $parameters = []): mixed
    {
        $ref = $this->reflectCallable($callable);

        $args = [];
        foreach ($ref->getParameters() as $param) {
            $name = $param->getName();

            // Explicit parameters win
            if (array_key_exists($name, $parameters)) {
                $args[] = $parameters[$name];

                continue;
            }

            $type = $param->getType();
            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                $args[] = $this->make($type->getName());

                continue;
            }

            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();

                continue;
            }

            throw new RuntimeException("Unable to resolve parameter \${$name}.");
        }

        return $callable(...$args);
    }

    private function resolveConcrete(mixed $concrete): object
    {
        // Factory/closure binding
        if ($concrete instanceof Closure) {
            $result = $concrete($this);

            if (! is_object($result)) {
                throw new RuntimeException('Factory binding must return an object.');
            }

            return $result;
        }

        // Class string
        if (is_string($concrete)) {
            return $this->build($concrete);
        }

        // Direct object binding
        if (is_object($concrete)) {
            return $concrete;
        }

        throw new RuntimeException('Invalid binding concrete type.');
    }

    private function build(string $class): object
    {
        if (! class_exists($class)) {
            throw new RuntimeException("Class [{$class}] does not exist.");
        }

        $refClass = new ReflectionClass($class);

        if (! $refClass->isInstantiable()) {
            throw new RuntimeException("Class [{$class}] is not instantiable.");
        }

        $ctor = $refClass->getConstructor();

        // No constructor => just instantiate
        if ($ctor === null) {
            return new $class;
        }

        $deps = [];
        foreach ($ctor->getParameters() as $param) {
            $type = $param->getType();

            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                $deps[] = $this->make($type->getName());

                continue;
            }

            // Built-in types (string/int/array) can't be auto-wired safely
            if ($param->isDefaultValueAvailable()) {
                $deps[] = $param->getDefaultValue();

                continue;
            }

            $name = $param->getName();
            throw new RuntimeException("Cannot auto-wire built-in parameter \${$name} in [{$class}].");
        }

        return $refClass->newInstanceArgs($deps);
    }

    private function reflectCallable(callable $callable): ReflectionFunction|ReflectionMethod
    {
        if (is_array($callable) && count($callable) === 2) {
            return new ReflectionMethod($callable[0], $callable[1]);
        }

        if (is_string($callable) && str_contains($callable, '::')) {
            [$cls, $method] = explode('::', $callable, 2);

            return new ReflectionMethod($cls, $method);
        }

        if ($callable instanceof Closure) {
            return new ReflectionFunction($callable);
        }

        if (is_object($callable) && method_exists($callable, '__invoke')) {
            return new ReflectionMethod($callable, '__invoke');
        }

        return new ReflectionFunction($callable);
    }
}
