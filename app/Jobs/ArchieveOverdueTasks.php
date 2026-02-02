<?php

namespace App\Jobs;

// This is the most important thing, who initiated the request is the whole theme of the work here.

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ArchieveOverdueTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    // apply critical thinking here.
    // The parameters try to answer the question of what data should
    // be catched? since there is no request
    public function __construct(public ?int $userId,
        public int $taskId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $task = Task::find($this->taskId);
        $userExists = $this->userId ? User::whereKey($this->userId)->exists() : false;

        if (! $task || $task->status !== 'due_date' || ! $userExists) {
            return;
        }

        ActivityLog::create([
            'action' => 'archieved.overdue.task',
            'project_id' => $task->project_id,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'user_type' => 'user',
            'user_id' => $this->userId,

            'meta' => [
                'task_title' => $task->title,
            ],

            'description' => 'Task Overdue Archieved',

        ]);

    }
}
