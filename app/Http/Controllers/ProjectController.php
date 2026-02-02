<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetTaskRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects = auth()->user()
            ->projects()
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));

    }

    public function show(Project $project, GetTaskRequest $request)
    {
        $this->authorize('view', $project);

        $activities = $project->activityLogs()->with('user')->latest()->take(20)->get();

        $filters = $request->validated();

        $project->load([
            'tasks' => fn ($q) => $q->notArchived()->applyFilters($filters),
            'tasks.tags',
            'tasks.subtasks',
        ]);

        $tags = $request->user()->tags()->orderBy('name')->get();

        return view('projects.show', compact('project', 'filters', 'tags', 'activities'));
    }

    public function store(StoreProjectRequest $request)
    {
        $this->authorize('create', Project::class);
        $project = $request->user()->projects()->create($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        // Why would we want to authorize the access of a user to a form input?
        // in updating data, the form would contain the old information that we wish to update, this is a data leak case.

        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));

    }

    public function create()
    {
        $this->authorize('create', Project::class);

        return view('projects.create');
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
