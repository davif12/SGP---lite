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

class CommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $project;
    protected $epic;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['owner_id' => $this->user->id]);
        $this->epic = Epic::factory()->create(['project_id' => $this->project->id]);
        $this->task = Task::factory()->create(['epic_id' => $this->epic->id]);
    }

    /** @test */
    public function authenticated_user_can_create_comment()
    {
        $commentData = [
            'content' => 'This is a test comment'
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('comments.store', $this->task), $commentData);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'comment' => [
                    'id',
                    'content',
                    'user' => ['name', 'avatar'],
                    'time_ago',
                    'is_editable',
                    'is_deletable'
                ]
            ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment',
            'task_id' => $this->task->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function comment_content_is_required()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('comments.store', $this->task), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    /** @test */
    public function comment_content_cannot_exceed_1000_characters()
    {
        $longContent = str_repeat('a', 1001);

        $response = $this->actingAs($this->user)
            ->postJson(route('comments.store', $this->task), [
                'content' => $longContent
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    /** @test */
    public function user_can_edit_own_comment_within_15_minutes()
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'created_at' => now()->subMinutes(10) // 10 minutes ago
        ]);

        $updateData = [
            'content' => 'Updated comment content'
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('comments.update', $comment), $updateData);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment content'
        ]);
    }

    /** @test */
    public function user_cannot_edit_comment_after_15_minutes()
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'created_at' => now()->subMinutes(20) // 20 minutes ago
        ]);

        $updateData = [
            'content' => 'Updated comment content'
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('comments.update', $comment), $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_edit_other_users_comment()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $otherUser->id
        ]);

        $updateData = [
            'content' => 'Updated comment content'
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('comments.update', $comment), $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_own_comment()
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('comments.destroy', $comment));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function project_owner_can_delete_any_comment()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user) // Project owner
            ->deleteJson(route('comments.destroy', $comment));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function regular_user_cannot_delete_other_users_comment()
    {
        $otherUser = User::factory()->create();
        $regularUser = User::factory()->create();
        
        // Add regular user to project
        $this->project->users()->attach($regularUser->id);
        
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($regularUser)
            ->deleteJson(route('comments.destroy', $comment));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_get_comments_for_task()
    {
        $comments = Comment::factory()->count(3)->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('comments.index', $this->task));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'comments');
    }

    /** @test */
    public function unauthorized_user_cannot_access_comments()
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->getJson(route('comments.index', $this->task));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_comments()
    {
        $response = $this->getJson(route('comments.index', $this->task));

        $response->assertStatus(401);
    }
}
