<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Epic;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_belongs_to_epic()
    {
        $epic = Epic::factory()->create();
        $task = Task::factory()->create(['epic_id' => $epic->id]);

        $this->assertInstanceOf(Epic::class, $task->epic);
        $this->assertEquals($epic->id, $task->epic->id);
    }

    /** @test */
    public function task_belongs_to_assigned_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['assigned_to' => $user->id]);

        $this->assertInstanceOf(User::class, $task->assignedUser);
        $this->assertEquals($user->id, $task->assignedUser->id);
    }

    /** @test */
    public function task_has_many_comments()
    {
        $task = Task::factory()->create();
        $comments = Comment::factory()->count(3)->create(['task_id' => $task->id]);

        $this->assertCount(3, $task->comments);
        $this->assertInstanceOf(Comment::class, $task->comments->first());
    }

    /** @test */
    public function task_status_label_accessor_works()
    {
        $task = Task::factory()->create(['status' => 'todo']);
        $this->assertEquals('A Fazer', $task->status_label);

        $task = Task::factory()->create(['status' => 'in_progress']);
        $this->assertEquals('Em Progresso', $task->status_label);

        $task = Task::factory()->create(['status' => 'review']);
        $this->assertEquals('Em Revisão', $task->status_label);

        $task = Task::factory()->create(['status' => 'done']);
        $this->assertEquals('Concluído', $task->status_label);
    }

    /** @test */
    public function task_priority_label_accessor_works()
    {
        $task = Task::factory()->create(['priority' => 'low']);
        $this->assertEquals('Baixa', $task->priority_label);

        $task = Task::factory()->create(['priority' => 'medium']);
        $this->assertEquals('Média', $task->priority_label);

        $task = Task::factory()->create(['priority' => 'high']);
        $this->assertEquals('Alta', $task->priority_label);

        $task = Task::factory()->create(['priority' => 'critical']);
        $this->assertEquals('Crítica', $task->priority_label);
    }

    /** @test */
    public function task_status_color_accessor_works()
    {
        $task = Task::factory()->create(['status' => 'todo']);
        $this->assertEquals('secondary', $task->status_color);

        $task = Task::factory()->create(['status' => 'in_progress']);
        $this->assertEquals('primary', $task->status_color);

        $task = Task::factory()->create(['status' => 'review']);
        $this->assertEquals('warning', $task->status_color);

        $task = Task::factory()->create(['status' => 'done']);
        $this->assertEquals('success', $task->status_color);
    }

    /** @test */
    public function task_priority_color_accessor_works()
    {
        $task = Task::factory()->create(['priority' => 'low']);
        $this->assertEquals('success', $task->priority_color);

        $task = Task::factory()->create(['priority' => 'medium']);
        $this->assertEquals('warning', $task->priority_color);

        $task = Task::factory()->create(['priority' => 'high']);
        $this->assertEquals('danger', $task->priority_color);

        $task = Task::factory()->create(['priority' => 'critical']);
        $this->assertEquals('dark', $task->priority_color);
    }

    /** @test */
    public function task_ordered_scope_works()
    {
        $task1 = Task::factory()->create(['position' => 3]);
        $task2 = Task::factory()->create(['position' => 1]);
        $task3 = Task::factory()->create(['position' => 2]);

        $orderedTasks = Task::ordered()->get();

        $this->assertEquals($task2->id, $orderedTasks->first()->id);
        $this->assertEquals($task1->id, $orderedTasks->last()->id);
    }

    /** @test */
    public function task_by_status_scope_works()
    {
        $todoTask = Task::factory()->create(['status' => 'todo']);
        $inProgressTask = Task::factory()->create(['status' => 'in_progress']);

        $todoTasks = Task::byStatus('todo')->get();
        $inProgressTasks = Task::byStatus('in_progress')->get();

        $this->assertCount(1, $todoTasks);
        $this->assertCount(1, $inProgressTasks);
        $this->assertEquals($todoTask->id, $todoTasks->first()->id);
        $this->assertEquals($inProgressTask->id, $inProgressTasks->first()->id);
    }

    /** @test */
    public function task_by_priority_scope_works()
    {
        $highTask = Task::factory()->create(['priority' => 'high']);
        $lowTask = Task::factory()->create(['priority' => 'low']);

        $highTasks = Task::byPriority('high')->get();
        $lowTasks = Task::byPriority('low')->get();

        $this->assertCount(1, $highTasks);
        $this->assertCount(1, $lowTasks);
        $this->assertEquals($highTask->id, $highTasks->first()->id);
        $this->assertEquals($lowTask->id, $lowTasks->first()->id);
    }

    /** @test */
    public function task_assigned_to_scope_works()
    {
        $user = User::factory()->create();
        $assignedTask = Task::factory()->create(['assigned_to' => $user->id]);
        $unassignedTask = Task::factory()->create(['assigned_to' => null]);

        $assignedTasks = Task::assignedTo($user->id)->get();

        $this->assertCount(1, $assignedTasks);
        $this->assertEquals($assignedTask->id, $assignedTasks->first()->id);
    }
}
