<?php

namespace App\Console\Commands;

use App\Jobs\ArchiveCompletedTasks;
use Illuminate\Console\Command;

class RunArchiveJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-archive-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        config(['tasks.archive_after_days' => 0]);

        ArchiveCompletedTasks::dispatchSync();

        $this->info('Archive job executed');
    }
}
