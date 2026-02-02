<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubtaskRequest;
use App\Http\Requests\UpdateSubtaskRequest;
use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;

class SubtaskController extends Controller
{
    // The routes that we registered are for storing, updating and deleting a subtask.

    public function store(Project $project, Task $task, StoreSubtaskRequest $request)
    {

        $this->authorize('createSubtask', $project);

        $data = $request->validated();

        $task->subtasks()->create(
            ['title' => $data['title'],
                'is_done' => false,
            ]);

        return back()->with('success', 'Subtask added.');

    }

    public function destroy(Project $project, Task $task, Subtask $subtask)
    {

        $this->authorize('delete', $project);
        $this->authorize('delete', $subtask);

        $subtask->delete();

        return back()->with(
            'success', 'Subtask deleted'
        );

    }

    public function update(Project $project, Task $task, Subtask $subtask, UpdateSubtaskRequest $request)
    {

        $this->authorize('update', $project);
        $this->authorize('update', $subtask);

        $data = $request->validated();

        // Reject empty PATCH (nothing to update)
        if (empty($data)) {
            return back()->withErrors(['subtask' => 'Nothing to update.']);
        }

        // Optional: if title is present but empty string, treat as invalid (instead of null)
        if (array_key_exists('title', $data) && is_string($data['title']) && trim($data['title']) === '') {
            return back()->withErrors(['title' => 'Title cannot be empty.']);
        }

        $subtask->fill($data);

        // avoid useless save if nothing changed
        if ($subtask->isDirty()) {
            $subtask->save();
        }

        return back()->with('success', 'Subtask updated.');
    }
}
