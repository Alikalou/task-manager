<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Is a logged in user allowed to view their own tags?
        // Notice that the authentication is done in the controller's index method itself.
        // The actual scoping (only returning the user's tags) is handled by the query.

        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tag $tag): bool
    {
        return $tag->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Creation is authorized at the USER level.
        // Ownership is enforced by creating the tag via $user->tags()->create().
        // There is no Tag instance yet, so we do NOT check $tag here.
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $tag->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $tag->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tag $tag): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tag $tag): bool
    {
        return false;
    }
}
