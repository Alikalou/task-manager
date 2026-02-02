<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\TaskCreated::class => [
            \App\Listeners\WriteActivityLog::class,
        ],
        \App\Events\TaskStatusChanged::class => [
            \App\Listeners\WriteActivityLog::class,
        ],
        \App\Events\TaskDeleted::class => [
            \App\Listeners\WriteActivityLog::class,
        ],
        \App\Events\ProjectRenamed::class => [
            \App\Listeners\WriteActivityLog::class,
        ],
    ];
}
