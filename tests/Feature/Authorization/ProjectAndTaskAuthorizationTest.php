<?php

namespace Tests\Feature\Authorization;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectAndTaskAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper payload: adjust keys/values if your StoreTaskRequest requires more fields.
     */
    private function validTaskPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Test Task',
            'status' => 'todo',   // adjust if you use enum values like TaskStatus::TODO->value
            'priority' => 'low',    // adjust if your app expects int 1-3 etc.
            // 'due_date' => now()->addDay()->toDateString(), // uncomment if required
        ], $overrides);
    }

    public function test_user_cannot_view_another_users_project(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $project = Project::factory()->for($owner)->create();

        $response = $this->actingAs($intruder, 'web')->get("/projects/{$project->id}");

        // Choose ONE based on your app’s behavior:
        $response->assertForbidden();     // 403 if you expose “exists but forbidden”
        // $response->assertNotFound();   // 404 if you hide resource existence
    }

    public function test_user_cannot_create_task_in_another_users_project(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $project = Project::factory()->for($owner)->create();

        $response = $this->actingAs($intruder, 'web')->post(
            "/projects/{$project->id}/tasks",
            $this->validTaskPayload(['title' => 'Intruder task attempt'])
        );

        $response->assertForbidden();
    }

    public function test_user_cannot_delete_another_users_task(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $project = Project::factory()->for($owner)->create();

        // If you have Task factory state like ->for($project), use it:
        $task = Task::factory()->for($project)->create();

        $response = $this->actingAs($intruder, 'web')->delete("projects/{$project->id}/tasks/{$task->id}");

        $response->assertForbidden();
    }

    public function test_user_cannot_update_another_users_task(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $project = Project::factory()->for($owner)->create();
        $task = Task::factory()->for($project)->create([
            'title' => 'Original',
        ]);

        $response = $this->actingAs($intruder, 'web')->patch(
            "projects/{$project->id}/tasks/{$task->id}",
            $this->validTaskPayload(['title' => 'Changed by intruder'])
        );

        $response->assertForbidden();
    }

    public function test_owner_can_create_task_in_own_project(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->for($owner)->create();

        $response = $this->actingAs($owner, 'web')->post(
            "/projects/{$project->id}/tasks",
            $this->validTaskPayload(['title' => 'Owner can create'])
        );

        // Web form endpoints typically redirect back on success:
        $response->assertStatus(302);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Owner can create',
            'project_id' => $project->id,
        ]);
    }
}
