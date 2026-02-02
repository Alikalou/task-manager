<?php

namespace App\Policies;

use App\Models\Subtask;
use App\Models\User;

class SubtaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // No standalone resource /subtask to view the list of subtasks.
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subtask $subtask): bool
    {
        return $user->id === $subtask->task->project->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subtask $subtask): bool
    {
        return $user->id === $subtask->task->project->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subtask $subtask): bool
    {
        return $user->id === $subtask->task->project->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Subtask $subtask): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Subtask $subtask): bool
    {
        return false;
    }
}
