<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // ✅ This makes nested bindings safe:
        // /projects/{project}/tasks/{task}
        // will only resolve {task} if it belongs to {project}
        Route::scopedBindings();
    }
}
