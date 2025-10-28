<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Epic;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $project;
    protected $epic;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['owner_id' => $this->user->id]);
        $this->epic = Epic::factory()->create(['project_id' => $this->project->id]);
    }

    /** @test */
    public function authenticated_user_can_view_tasks_index()
    {
        $task = Task::factory()->create(['epic_id' => $this->epic->id]);

        $response = $this->actingAs($this->user)
            ->get(route('projects.epics.tasks.index', [$this->project, $this->epic]));

        $response->assertStatus(200)
            ->assertSee($task->title)
            ->assertViewIs('tasks.index');
    }

    /** @test */
    public function authenticated_user_can_create_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'todo',
            'priority' => 'medium',
            'story_points' => 5,
            'assigned_to' => $this->user->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)
            ->post(route('projects.epics.tasks.store', [$this->project, $this->epic]), $taskData);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'epic_id' => $this->epic->id,
            'status' => 'todo',
            'priority' => 'medium',
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_task_details()
    {
        $task = Task::factory()->create(['epic_id' => $this->epic->id]);

        $response = $this->actingAs($this->user)
            ->get(route('projects.epics.tasks.show', [$this->project, $this->epic, $task]));

        $response->assertStatus(200)
            ->assertSee($task->title)
            ->assertSee($task->description)
            ->assertViewIs('tasks.show');
    }

    /** @test */
    public function authenticated_user_can_update_task()
    {
        $task = Task::factory()->create(['epic_id' => $this->epic->id]);

        $updateData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated Description',
            'status' => 'in_progress',
            'priority' => 'high',
            'story_points' => 8,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('projects.epics.tasks.update', [$this->project, $this->epic, $task]), $updateData);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'status' => 'in_progress',
            'priority' => 'high',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_task()
    {
        $task = Task::factory()->create(['epic_id' => $this->epic->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('projects.epics.tasks.destroy', [$this->project, $this->epic, $task]));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function task_validation_works_correctly()
    {
        $response = $this->actingAs($this->user)
            ->post(route('projects.epics.tasks.store', [$this->project, $this->epic]), []);

        $response->assertSessionHasErrors(['title']);
    }

    /** @test */
    public function unauthorized_user_cannot_access_tasks()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['epic_id' => $this->epic->id]);

        $response = $this->actingAs($otherUser)
            ->get(route('projects.epics.tasks.index', [$this->project, $this->epic]));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_user_is_redirected_to_login()
    {
        $response = $this->get(route('projects.epics.tasks.index', [$this->project, $this->epic]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function task_can_be_moved_via_api()
    {
        $task = Task::factory()->create([
            'epic_id' => $this->epic->id,
            'status' => 'todo',
            'position' => 1
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson(route('tasks.move', $task), [
                'status' => 'in_progress',
                'position' => 2
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
            'position' => 2
        ]);
    }

    /** @test */
    public function task_move_requires_valid_status()
    {
        $task = Task::factory()->create(['epic_id' => $this->epic->id]);

        $response = $this->actingAs($this->user)
            ->patchJson(route('tasks.move', $task), [
                'status' => 'invalid_status',
                'position' => 1
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function task_with_comments_can_be_deleted()
    {
        $task = Task::factory()->create(['epic_id' => $this->epic->id]);
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('projects.epics.tasks.destroy', [$this->project, $this->epic, $task]));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
