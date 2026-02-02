<?php

namespace App\Jobs;

use App\Mail\TaskReminderMail;
use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTaskReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ?int $userId, // who initiated the reminder
        public int $taskId,
        public string $email)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $task = Task::find($this->taskId);

        if (! $task || $task->status === 'is_done') {
            return;
        }

        $userExists = $this->userId ? User::whereKey($this->userId)->exists() : false;

        ActivityLog::create([
            'action' => 'task.reminder.sent',
            'project_id' => $task->project_id,

            'subject_type' => Task::class,
            'subject_id' => $task->id,

            'user_type' => $userExists ? 'user' : 'system',
            'user_id' => $userExists ? $this->userId : null,

            'meta' => [
                'task_title' => $task->title,
                'channel' => 'email',
                'to' => $this->email,
            ],

            'description' => 'Task Reminder Sent',

        ]);

        Mail::to($this->email)->send(new TaskReminderMail($task));

    }
}
