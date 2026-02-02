<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'priority',
        'archived_by',
        'archived_at',
        'done_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'done_at' => 'date',
        'archived_at' => 'date',
        'priority' => TaskPriority::class,
        'status' => TaskStatus::class,
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_task')->withTimeStamps();
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! $status) {
            return $query;
        }

        try {
            TaskStatus::from($status);
        } catch (\ValueError) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where('title', 'like', '%'.$term.'%');
    }

    public function scopeDueFilter(Builder $query, ?string $due): Builder
    {
        if (! $due) {
            return $query;
        }

        $today = Carbon::today();

        return match ($due) {
            'overdue' => $query->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->where('status', '!=', 'done'),

            'today' => $query->whereDate('due_date', $today),

            'this_week' => $query->whereBetween('due_date', [
                $today->copy()->startOfWeek()->toDateString(),
                $today->copy()->endOfWeek()->toDateString(),
            ]),

            default => $query,
        };
    }

    public function scopePrioritySort(Builder $query, ?string $mode): Builder
    {
        if (! $mode || $mode !== 'first') {
            return $query;
        }

        if ($mode === 'first') {
            return $query->orderByRaw('
                CASE priority
                    WHEN ? THEN 1
                    WHEN ? THEN 2
                    WHEN ? THEN 3
                    ELSE 4
                END
            ', [
                TaskPriority::High->value,
                TaskPriority::Normal->value,
                TaskPriority::Low->value,
            ]);
        }

        return $query;
    }

    public function scopeSortBy(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),

            'due_asc' => $query->orderByRaw('due_date IS NULL, due_date asc'),
            'due_desc' => $query->orderByRaw('due_date IS NULL, due_date desc'),
            default => $query->orderBy('created_at', 'desc'), // newest
        };
    }

    public function scopeTags(Builder $query, ?array $tagIds)
    {
        if (! $tagIds) {
            return $query;
        }

        return $query->whereHas('tags', function (Builder $q) use ($tagIds) {
            $q->whereIn('tags.id', $tagIds);
        });
    }

    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->whereNull('archived_at')
            ->status($filters['status'] ?? null)
            ->search($filters['search'] ?? null)
            ->dueFilter($filters['due'] ?? null)
            ->sortBy($filters['sort'] ?? 'newest')
            ->prioritySort($filters['priority_sort'] ?? null)   // ✅ first grouping
            ->tags($filters['tag_ids'] ?? null);             // ✅ secondary order
    }

    public function subtasksTotalCount(): int
    {

        return (int) $this->subtasks->count();
    }

    public function subtasksDoneCount(): int
    {
        return (int) $this->subtasks->where('is_done', true)->count();
    }

    public function subtasksProgressLabel(): string
    {

        $done = $this->subtasksDoneCount();
        $total = $this->subtasksTotalCount();

        return "{$done}/{$total} done";
    }

    // below can only be used as a testing metho?
    public function progressPercentage(): int
    {

        $total = $this->subtasks()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->subtasks()->where('is_done', true)->count();

        return (int) round(($completed / $total) * 100);

    }

    public function markDone(): void
    {
        $this->forceFill([
            'done_at' => $this['done_at'] ?? now(),
            'status' => TaskStatus::DONE->value,
        ])->save();
    }

    public function reopenToTodo(): void
    {
        $this->forceFill([
            'status' => TaskStatus::TO_DO->value,
            'done_at' => null,
            'archived_at' => null,
        ])->save();
    }

    public function reopenToInProgress(): void
    {
        $this->forceFill([
            'status' => TaskStatus::IN_PROGRESS->value,
            'done_at' => null,
            'archived_at' => null,
        ])->save();
    }

    public function archive(): void
    {
        $this->forceFill([
            'archived_at' => now(),
            'archived_by' => 'system',
        ])->save();
    }

    public function isArchived(): bool
    {
        return ! is_null($this->archived_at);
    }

    public function scopeArchived(Builder $q): Builder
    {
        return $q->whereNotNull('archived_at');
    }

    public function scopeNotArchived(Builder $q): Builder
    {
        return $q->whereNull('archived_at');
    }

    public function scopeEligibleForArchive(Builder $q, int $days): Builder
    {
        return $q->whereNull('archived_at')
            ->whereNotNull('done_at')
            ->where('done_at', '<=', now()->subDays($days));
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('due_date')->whereDate('due_date', '<', now()->toDateString());

    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', '=', now()->toDateString());
    }

    public function scopeDueThisWeek(Builder $query): Builder
    {
        return $query->whereDate('due_date', '>', now()->toDateString());
    }
}
