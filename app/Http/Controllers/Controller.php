<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function authorizeProject(Project $project): void
    {
        abort_if($project->user_id !== auth()->id(), 403);
    }

    protected function authorizeTag(Tag $tag): void
    {
        abort_if($tag->user_id !== auth()->id(), 403);
    }
}
