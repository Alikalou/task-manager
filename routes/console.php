<?php

use App\Jobs\ArchiveCompletedTasks;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
Schedule::job(new ArchiveCompletedTasks)
    ->dailyAt('02:10')
    ->onOneServer()
    ->withoutOverlapping();
    */

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
