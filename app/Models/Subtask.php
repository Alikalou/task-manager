<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    protected $fillable = ['title', 'is_done']; // Why do we need task id to be filled by the user form?

    protected $casts = ['is_done' => 'boolean'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Recall that in the model, we define the fillable data, and we define the relationships of the model.
    // Also, in this particular model, we need to define three helpers. one for the total number of substacks,
    // another for the total number of finished substacks, last helper is to see if a task itself is done.

}
