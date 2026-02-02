<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaskService
{
    public function bulkDestroyWithLog(Project $project, array $taskIds): int
    {

        return DB::transaction(function () use ($project, $taskIds) {
            $tasks = $project->tasks()
                ->with('project')
                ->whereIn('id', $taskIds)
                ->get();

            foreach ($tasks as $task) {
                Gate::authorize('delete', $task);
            }

            $deleted = $project->tasks()
                ->whereIn('id', $taskIds)
                ->delete();

            ActivityLog::create([
                'project_id' => $project->id,
                'user_id' => $project->user_id,
                'action' => 'task collection.deleted',
                'subject_type' => Task::class,
                'subject_id' => null,
                'description' => 'Task Bulk Delete',
                'meta' => [
                    'task_ids' => array_values($taskIds),
                    'count' => $deleted,
                ],
            ]);

            return $deleted;
        });

    }

    public function createTaskWithLog(Project $project, array $data): null
    {

        return DB::transaction(function () use ($project, $data) {

            $task = Task::withoutEvents(function () use ($project, $data) {
                return $project->tasks()->create([
                    'title' => $data['title'],
                    'status' => $data['status'],
                    'due_date' => $data['due_date'] ?? null,
                    'priority' => $data['priority'],
                ]);
            });

            ActivityLog::create([
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'action' => 'task.created',
                'subject_type' => Task::class,
                'subject_id' => $task->id,
                'description' => "Task created: {$task->title}",
                'meta' => null,
            ]);

        }); // the end of the transaction process

    }// The end of the first transaction method.

    public function createTaskWithTagsAndLog(Project $project, array $data, User $user): null
    {

        return DB::transaction(function () use ($project, $data, $user) {

            $tagIds = $data['tag_ids'] ?? [];
            unset($data['tag_ids']);

            $task = Task::withoutEvents(function () use ($project, $data) {
                return $project->tasks()->create([
                    'title' => $data['title'],
                    'status' => $data['status'],
                    'due_date' => $data['due_date'] ?? null,
                    'priority' => $data['priority'],
                ]);
            });

            $allowedTagIds = $user->tags()
                ->whereIn('id', $tagIds)
                ->pluck('id')
                ->all();

            if (count($allowedTagIds) !== count(array_unique($tagIds))) {
                throw new AuthorizationException('Invalid tag IDs.');
            }

            $task->tags()->sync($allowedTagIds);

            ActivityLog::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'action' => 'task.created',
                'subject_type' => Task::class,
                'subject_id' => $task->id,
                'description' => "Task created: {$task->title}",
                'meta' => null,
            ]);

        });
    }
}
