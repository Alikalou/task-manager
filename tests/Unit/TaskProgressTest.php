<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_is_zero_when_task_has_no_subtasks(): void
    {
        // Arrange
        $task = Task::factory()->create();

        // Act
        $progress = $task->progressPercentage();

        // Assert
        $this->assertSame(0, $progress);
    }
}
