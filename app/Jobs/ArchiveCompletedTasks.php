<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ArchiveCompletedTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $days = (int) config('tasks.archive_after_days', 30);

        Task::query()
            ->eligibleForArchive($days)
            ->select('id', 'archived_at', 'project_id', 'title')
            ->chunkById(500, function ($tasks) {
                foreach ($tasks as $task) {
                    if ($task->archived_at) {
                        continue;
                    }

                    $task->archive();
                    ActivityLog::create([
                        'action' => 'task.finished.archived',
                        'project_id' => $task->project_id,

                        'subject_type' => Task::class,
                        'subject_id' => $task->id,

                        'user_type' => 'system',
                        'user_id' => null,

                        'meta' => [
                            'task_title' => $task->title,
                        ],

                        'description' => 'Auto-archive completed tasks after N days.',

                    ]);
                }
            });

    }
}
