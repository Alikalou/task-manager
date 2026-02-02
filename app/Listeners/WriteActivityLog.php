<?php

// app/Listeners/WriteActivityLog.php

namespace App\Listeners;

use App\Events\ProjectRenamed;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskStatusChanged;
use App\Models\ActivityLog;

class WriteActivityLog
{
    public function handle($event): void
    {

        match (true) {
            $event instanceof TaskCreated => ActivityLog::create([
                'project_id' => $event->task->project_id,
                'user_id' => $event->userId,
                'action' => 'task.created',
                'subject_type' => get_class($event->task),
                'subject_id' => $event->task->id,
                'description' => "Task created: {$event->task->title}",
                'meta' => null,
            ]),

            $event instanceof TaskStatusChanged => ActivityLog::create([
                'project_id' => $event->task->project_id,
                'user_id' => $event->userId,
                'action' => 'task.status_changed',
                'subject_type' => get_class($event->task),
                'subject_id' => $event->task->id,
                'description' => "Status changed {$event->oldStatus} → {$event->newStatus}: {$event->task->title}",
                'meta' => ['from' => $event->oldStatus, 'to' => $event->newStatus],
            ]),

            $event instanceof TaskDeleted => ActivityLog::create([
                'project_id' => $event->projectId,
                'user_id' => $event->userId,
                'action' => 'task.deleted',
                'subject_type' => \App\Models\Task::class,
                'subject_id' => $event->taskId,
                'description' => "Task deleted: {$event->title}",
                'meta' => null,
            ]),

            $event instanceof ProjectRenamed => ActivityLog::create([
                'project_id' => $event->project->id,
                'user_id' => $event->userId,
                'action' => 'project.renamed',
                'subject_type' => get_class($event->project),
                'subject_id' => $event->project->id,
                'description' => "Project renamed {$event->oldName} → {$event->newName}",
                'meta' => ['from' => $event->oldName, 'to' => $event->newName],
            ]),

            default => null,
        };
    }
}
