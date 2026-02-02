<?php

// app/Observers/ProjectObserver.php

namespace App\Observers;

use App\Events\ProjectRenamed;
use App\Models\Project;

class ProjectObserver
{
    public function updating(Project $project): void
    {
        if ($project->isDirty('name')) {
            $project->old_name = (string) $project->getOriginal('name');
        }
    }

    public function updated(Project $project): void
    {
        if ($project->wasChanged('name')) {
            $old = (string) ($project->old_name ?? $project->getOriginal('name'));
            $new = (string) $project->name;

            event(new ProjectRenamed($old, $new, $project), auth()->id());
        }
    }
}
