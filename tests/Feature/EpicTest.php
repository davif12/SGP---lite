<?php

namespace Tests\Feature;

use App\Models\Epic;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EpicTest extends TestCase
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

    public function test_user_can_view_epics_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('epics.index', $this->project));

        $response->assertStatus(200);
        $response->assertSee($this->epic->name);
    }

    public function test_user_can_create_epic()
    {
        $epicData = [
            'name' => 'Test Epic',
            'description' => 'Test epic description',
            'status' => 'backlog',
            'priority' => 'medium',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('projects.epics.store', $this->project), $epicData);

        $response->assertRedirect(route('epics.index', $this->project));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('epics', [
            'name' => 'Test Epic',
            'project_id' => $this->project->id,
        ]);
    }

    public function test_user_can_view_epic()
    {
        $response = $this->actingAs($this->user)
            ->get(route('projects.epics.show', [$this->project, $this->epic]));

        $response->assertStatus(200);
        $response->assertSee($this->epic->name);
        $response->assertSee($this->epic->description);
    }

    public function test_user_can_update_epic()
    {
        $updateData = [
            'name' => 'Updated Epic Name',
            'description' => 'Updated description',
            'status' => 'in_progress',
            'priority' => 'high',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('projects.epics.update', [$this->project, $this->epic]), $updateData);

        $response->assertRedirect(route('epics.index', $this->project));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('epics', [
            'id' => $this->epic->id,
            'name' => 'Updated Epic Name',
            'status' => 'in_progress',
            'priority' => 'high',
        ]);
    }

    public function test_user_can_delete_epic()
    {
        $response = $this->actingAs($this->user)
            ->delete(route('projects.epics.destroy', [$this->project, $this->epic]));

        $response->assertRedirect(route('epics.index', $this->project));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('epics', [
            'id' => $this->epic->id,
        ]);
    }

    public function test_non_member_cannot_access_epics()
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('epics.index', $this->project));

        $response->assertStatus(403);
    }

    public function test_member_can_view_but_not_modify_epics()
    {
        $member = User::factory()->create();
        $this->project->users()->attach($member->id, ['role' => 'member']);

        // Can view
        $response = $this->actingAs($member)
            ->get(route('epics.index', $this->project));
        $response->assertStatus(200);

        // Cannot create
        $response = $this->actingAs($member)
            ->get(route('projects.epics.create', $this->project));
        $response->assertStatus(403);

        // Cannot update
        $response = $this->actingAs($member)
            ->put(route('projects.epics.update', [$this->project, $this->epic]), [
                'name' => 'Updated',
                'status' => 'done',
                'priority' => 'low',
            ]);
        $response->assertStatus(403);
    }

    public function test_epic_validation_rules()
    {
        // Test required fields
        $response = $this->actingAs($this->user)
            ->post(route('projects.epics.store', $this->project), []);

        $response->assertSessionHasErrors(['name', 'status', 'priority']);

        // Test invalid status
        $response = $this->actingAs($this->user)
            ->post(route('projects.epics.store', $this->project), [
                'name' => 'Test Epic',
                'status' => 'invalid_status',
                'priority' => 'medium',
            ]);

        $response->assertSessionHasErrors(['status']);

        // Test invalid priority
        $response = $this->actingAs($this->user)
            ->post(route('projects.epics.store', $this->project), [
                'name' => 'Test Epic',
                'status' => 'backlog',
                'priority' => 'invalid_priority',
            ]);

        $response->assertSessionHasErrors(['priority']);
    }
}
