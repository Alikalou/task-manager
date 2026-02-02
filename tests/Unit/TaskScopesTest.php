<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TaskScopesTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_scope_returns_only_tasks_with_due_date_before_today(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-19 13:51:00'));

        $overdue = Task::factory()->create(['due_date' => '2026-01-18']); // yesterday
        $today = Task::factory()->create(['due_date' => '2026-01-19']); // today
        $future = Task::factory()->create(['due_date' => '2026-01-20']); // tomorrow
        $noDueDate = Task::factory()->create(['due_date' => null]);

        $resultIds = Task::query()->overdue()->pluck('id')->all();

        $this->assertContains($overdue->id, $resultIds);
        $this->assertNotContains($today->id, $resultIds);
        $this->assertNotContains($future->id, $resultIds);
        $this->assertNotContains($noDueDate->id, $resultIds);
    }

    public function test_today_scope_returns_only_tasks_with_due_today(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-19 09:00:00'));

        $overdue = Task::factory()->create(['due_date' => '2026-01-18']); // yesterday
        $today = Task::factory()->create(['due_date' => '2026-01-19']); // today
        $future = Task::factory()->create(['due_date' => '2026-01-20']); // tomorrow
        $noDueDate = Task::factory()->create(['due_date' => null]);

        $resultIds = Task::query()->dueToday()->pluck('id')->all();

        $this->assertNotContains($overdue->id, $resultIds);
        $this->assertContains($today->id, $resultIds);
        $this->assertNotContains($future->id, $resultIds);
        $this->assertNotContains($noDueDate->id, $resultIds);
    }

    public function test_week_scope_returns_only_tasks_due_this_week(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-19 09:00:00'));

        $overdue = Task::factory()->create(['due_date' => '2026-01-18']); // yesterday
        $today = Task::factory()->create(['due_date' => '2026-01-19']); // today
        $future = Task::factory()->create(['due_date' => '2026-01-22']); // future
        $noDueDate = Task::factory()->create(['due_date' => null]);

        $resultIds = Task::query()->dueThisWeek()->pluck('id')->all(); // 👈 use your week scope name

        $this->assertNotContains($overdue->id, $resultIds);
        $this->assertNotContains($today->id, $resultIds);
        $this->assertContains($future->id, $resultIds);
        $this->assertNotContains($noDueDate->id, $resultIds);
    }
}
