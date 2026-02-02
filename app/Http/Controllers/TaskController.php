<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\DeleteTaskRequest;
use App\Http\Requests\ReminderRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Jobs\SendTaskReminderJob;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function showArchivedTasks()
    {
        $this->authorize('viewAny', Task::class);

        $tasks = auth()->user()
            ->tasks()
            ->archived()
            ->latest()
            ->get();

        return view('archived-tasks', compact('tasks'));
    }

    public function uncompleteArchivedTask(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => ['required', 'in:todo,in_progress'],
        ]);

        $status = TaskStatus::from($validated['status']);

        match ($status) {
            TaskStatus::TO_DO => $task->reopenToTodo(),
            TaskStatus::IN_PROGRESS => $task->reopenToInProgress(),
            default => throw new \InvalidArgumentException("Unsupported status {$status->value}"),
        };

        return back()->with('success', 'Task is Reopened');

    }

    public function store(StoreTaskRequest $request, Project $project, TaskService $service)
    {
        $this->authorize('createTask', $project);

        $data = $request->validated();
        $user = $request->user();

        empty($data['tag_ids'] ?? [])
            ? $service->createTaskWithLog($project, $data, $user)
            : $service->createTaskWithTagsAndLog($project, $data, $user);

        return back()->with('success', 'Task created successfully.');
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        $this->authorize('update', $project);
        $this->authorize('update', $task);

        $data = $request->validated();
        $status = TaskStatus::from($data['status']);

        match ($status) {
            TaskStatus::DONE => $task->markDone(),
            TaskStatus::TO_DO => $task->reopenTodo(),
            TaskStatus::IN_PROGRESS => $task->reopenToInProgress(),
            default => throw new \InvalidArgumentException("Unsupported status: {$status->value}"),
        };

        unset($data['status']);

        if (! empty($data)) {
            $task->fill($data)->save();
        }

        return back()->with('success', 'Task updated successfully.');
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('update', $project);
        $this->authorize('delete', $task);

        $task->delete();

        return back()->with('success', 'Task deleted.');
    }

    protected function authorizeTaskBelongsToProject(Project $project, Task $task): void
    {
        abort_unless($task->project_id === $project->id, 404);
    }

    public function bulkDestroy(Project $project, DeleteTaskRequest $request, TaskService $service)
    {

        $this->authorize('update', $project);

        $taskIds = $request->validated()['task_ids'];

        $deleted = $service->bulkDestroyWithLog($project, $taskIds, $service);

        return back()->with('success', "Deleted {$deleted} tasks.");
    }

    public function sendReminder(Project $project, Task $task, ReminderRequest $request)
    {
        // Request validation is applied, what next?
        // Since I already have the send reminder method, which takes the user request and detach it for later execution.

        // If I'm correct, the question of ownership applies to both if the user ownes the project
        // and if he ownes the task.
        $this->authorize('update', $project);
        $this->authorize('update', $task);

        $data = $request->validated();

        $delay = now()->addSeconds(3600 * $data['delay_hours'] + 60 * $data['delay_minutes']);

        SendTaskReminderJob::dispatch(auth()->id(), $task->id, $data['email'])->delay($delay);

        return back()->with('success', 'Reminder Sent');

    }
}
