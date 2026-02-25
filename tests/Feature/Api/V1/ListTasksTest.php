<?php

namespace Tests\Feature\Api\V1;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListTasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication(): void
    {
        $project = Project::factory()->create();
        $this->getJson('/api/v1/projects/{project->id}/tasks')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_user_cannot_list_tasks_of_another_users_project(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $projectA = Project::factory()->create(['user_id' => $userA->id]);

        Task::factory()->count(2)->create(['project_id' => $projectA->id]);

        $this->actingAs($userB, 'sanctum')
            ->getJson("/api/v1/projects/{$projectA->id}/tasks")
            ->assertStatus(403);

    }

    public function test_it_lists_only_tasks_of_the_given_project_in_contract_shape(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $projectUserA = Project::factory()->create(['user_id' => $userA->id]);
        $projectUserB = Project::factory()->create(['user_id' => $userB->id]);

        Task::factory()->count(2)->create([
            'project_id' => $projectUserA->id,
        ]);

        Task::factory()->count(3)->create([
            'project_id' => $projectUserB->id,
        ]);

        $response = $this->actingAs($userA, 'sanctum')
            ->getJson("/api/v1/projects/{$projectUserA->id}/tasks");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'project_id',
                        'title',
                        'status',
                        'priority',
                        'due_date',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        // enforce: only tasks of this project
        $this->assertCount(2, $response->json('data'));
    }
}
