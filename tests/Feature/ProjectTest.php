<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_projects_index()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/projects');
        
        $response->assertStatus(200);
        $response->assertViewIs('projects.index');
    }

    public function test_user_can_create_project()
    {
        $user = User::factory()->create();
        
        $projectData = [
            'name' => 'Test Project',
            'description' => 'This is a test project',
        ];
        
        $response = $this->actingAs($user)->post('/projects', $projectData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'description' => 'This is a test project',
            'owner_id' => $user->id,
        ]);
        
        // Verificar se o owner foi adicionado como membro
        $project = Project::where('name', 'Test Project')->first();
        $this->assertTrue($project->users()->where('user_id', $user->id)->exists());
    }

    public function test_project_owner_can_view_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $project->users()->attach($user->id, ['role' => 'owner']);
        
        $response = $this->actingAs($user)->get("/projects/{$project->id}");
        
        $response->assertStatus(200);
        $response->assertViewIs('projects.show');
        $response->assertViewHas('project', $project);
    }

    public function test_project_member_can_view_project()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        $project->users()->attach($owner->id, ['role' => 'owner']);
        $project->users()->attach($member->id, ['role' => 'member']);
        
        $response = $this->actingAs($member)->get("/projects/{$project->id}");
        
        $response->assertStatus(200);
    }

    public function test_non_member_cannot_view_project()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        $project->users()->attach($owner->id, ['role' => 'owner']);
        
        $response = $this->actingAs($nonMember)->get("/projects/{$project->id}");
        
        $response->assertStatus(403);
    }

    public function test_project_owner_can_update_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $project->users()->attach($user->id, ['role' => 'owner']);
        
        $updateData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ];
        
        $response = $this->actingAs($user)->put("/projects/{$project->id}", $updateData);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ]);
    }

    public function test_project_member_cannot_update_project()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        $project->users()->attach($owner->id, ['role' => 'owner']);
        $project->users()->attach($member->id, ['role' => 'member']);
        
        $updateData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ];
        
        $response = $this->actingAs($member)->put("/projects/{$project->id}", $updateData);
        
        $response->assertStatus(403);
    }

    public function test_project_owner_can_delete_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $project->users()->attach($user->id, ['role' => 'owner']);
        
        $response = $this->actingAs($user)->delete("/projects/{$project->id}");
        
        $response->assertRedirect('/projects');
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_project_owner_can_add_member()
    {
        $owner = User::factory()->create();
        $newMember = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        $project->users()->attach($owner->id, ['role' => 'owner']);
        
        $response = $this->actingAs($owner)->post("/projects/{$project->id}/members", [
            'email' => $newMember->email,
        ]);
        
        $response->assertRedirect();
        $this->assertTrue($project->users()->where('user_id', $newMember->id)->exists());
    }

    public function test_project_owner_can_remove_member()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        $project->users()->attach($owner->id, ['role' => 'owner']);
        $project->users()->attach($member->id, ['role' => 'member']);
        
        $response = $this->actingAs($owner)->delete("/projects/{$project->id}/members/{$member->id}");
        
        $response->assertRedirect();
        $this->assertFalse($project->users()->where('user_id', $member->id)->exists());
    }

    public function test_guest_cannot_access_projects()
    {
        $response = $this->get('/projects');
        
        $response->assertRedirect('/login');
    }
}
