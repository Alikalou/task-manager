<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\V1\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TaskController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $this->authorize('view', $project);
        $tasks = $project->tasks()->latest()->paginate(15);

        return TaskResource::collection($tasks);

    }

    public function updateStatus(Request $request, Project $project, Task $task)
    {

        $this->authorize('update', $project);

        $request->validate([
            'status' => ['required', new Enum(TaskStatus::class)],
        ]);

        $oldStatus = $task->status;
        $task['status'] = $request->status;
        $task->save();

        return response()->json([
            'message' => 'Task status updated successfully',
            'old_status' => $oldStatus,
            'new status' => $task->status,
        ]);

    }

    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('createTask', $project);

        $task = $project->tasks()->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Task Created Successfully',
            'status' => $task->status,
        ]);
    }

    public function destroy(Request $request, Project $project, Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'message' => 'Task Deleted Successfully',
        ]);

    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        logger()->info('title type', [
            'title' => $request->input('title'),
            'type' => gettype($request->input('title')),
        ]);

        $this->authorize('update', $task);
        $data = $request->validated();
        $task->update($data);

        return response()->json(['message' => 'Task Updated Successfully',
            'updated data' => [
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority, ],
        ]);
    }
}
