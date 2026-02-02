<?php

namespace App\Models;

use App\Model\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name'];

    //
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'tag_task')->withTimeStamps();
    }

    // you can see the question of whether should a tag belong to a user or a project
    // obviously, the tag should belong to a user, since the tag is attached to a task and not a project.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
