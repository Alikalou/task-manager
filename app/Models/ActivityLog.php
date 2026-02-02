<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Morphto;

class ActivityLog extends Model
{
    protected $fillable = [
        'meta',
        'subject_id',
        'subject_type',
        'action',
        'description',
        'project_id',
        'user_id',
        'user_type',
    ];

    protected $casts = ['meta' => 'array'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
