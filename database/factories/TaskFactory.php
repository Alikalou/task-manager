<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'project_id' => Project::factory(),
            // add required fields in your schema:
            // 'status' => 'todo',
            // 'priority' => 1,
            // 'due_date' => null,
        ];
    }
}
