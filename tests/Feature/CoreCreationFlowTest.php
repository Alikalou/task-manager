<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreCreationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Adjust these defaults to match your StoreTaskRequest rules / enums.
     */
    private function validTaskPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'My Task',
            'status' => 'todo',  // change if your enum values differ
            'priority' => 'low',   // change if your app expects int, e.g. 1/2/3
            // 'due_date' => now()->addDay()->toDateString(), // uncomment if required
            // 'tag_ids'  => [], // include if your StoreTaskRequest expects it
        ], $overrides);
    }

    public function test_authenticated_user_can_create_a_project(): void
    {
        // arrange

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->post('/projects', [
            'name' => 'My First Project',

        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('projects', [
            'name' => 'My First Project',
            'user_id' => $user->id,
        ]);
    }

    public function test_create_project_with_missing_data(): void
    {

        // arrange
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->post('/projects', []);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing(
            'projects', ['user_id' => $user->id]
        );
    }

    public function test_authenticated_user_can_create_a_task_inside_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->actingAs($user, 'web')->post(
            "/projects/{$project->id}/tasks",
            $this->validTaskPayload(['title' => 'Task 1'])
        );

        // Your controller returns back()->with('success'...)
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Task 1',
            'project_id' => $project->id,
        ]);
    }

    public function test_task_creation_requires_valid_data(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $response = $this->actingAs($user, 'web')->post(
            "/projects/{$project->id}/tasks",
            $this->validTaskPayload(['title' => '']) // invalid title
        );

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $project->id,
            'title' => '',
        ]);
    }

    public function test_guest_cannot_create_project_and_is_redirected_to_login(): void
    {
        $response = $this->post('/projects', [
            'name' => 'Guest project attempt',
        ]);

        $response->assertRedirectToRoute('login');
        $this->assertDatabaseMissing('projects', [
            'name' => 'Guest project attempt',
        ]);
    }

    public function test_guest_cannot_create_task_and_is_redirected_to_login(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->for($owner)->create();

        $response = $this->post("/projects/{$project->id}/tasks", $this->validTaskPayload([
            'title' => 'Guest task attempt',
        ]));

        $response->assertRedirectToRoute('login');
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Guest task attempt',
            'project_id' => $project->id,
        ]);
    }
}
