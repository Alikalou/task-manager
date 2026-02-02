<?php

namespace App\Observers;

// What events did we create that should be handled by task observer?
// creation, deletion, & status change of a task.

use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskStatusChanged;
use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */

    // From where are the model $task being passed to the methods of the observer.
    public function created(Task $task): void
    {
        event(new TaskCreated($task), auth()->id());
        // This simple statement can be articulated as follow
        // the observer runs an event, where an event wait for the object appropriate for it, in our case the object is an instance
        // of TaskCreated event. Also notice the importance of $task, without it, it would be meaningless since we don't know
        // know what task was created.
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        if ($task->wasChanged('status')) {
            $new = $task->getRawOriginal('status');
            $old = $task->getRawOriginal('old_status') ?? $task->getRawOriginal('status');

            event(new TaskStatusChanged($task, $old, $new, auth()->id()));
        }
    }

    public function updating(Task $task): void
    {
        // Check if the task status property is already filled.
        if ($task->isDirty('status')) {
            $task->old_status = (string) $task->getRawOriginal('status');
        }

    }

    /**
     * Handle the Task "deleted" event.
     */

    // what would occur if the below code was written for a deleted method? how will the observer handle it?
    public function deleting(Task $task): void
    {

        $projectId = (int) $task->project_id;
        $taskId = (int) $task->id;
        $title = $task->title;

        event(new TaskDeleted($projectId, $taskId, $title), auth()->id());
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
