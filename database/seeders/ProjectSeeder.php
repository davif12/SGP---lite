<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar usuários de teste
        $users = User::factory(5)->create();
        
        // Criar projetos para cada usuário
        $users->each(function ($user) use ($users) {
            // Cada usuário cria 2-3 projetos
            $projects = Project::factory(rand(2, 3))->create([
                'owner_id' => $user->id,
            ]);
            
            // Adicionar o owner como membro com role 'owner'
            $projects->each(function ($project) use ($user, $users) {
                $project->users()->attach($user->id, ['role' => 'owner']);
                
                // Adicionar alguns membros aleatórios
                $randomMembers = $users->where('id', '!=', $user->id)
                    ->random(rand(1, 3));
                
                foreach ($randomMembers as $member) {
                    $project->users()->attach($member->id, ['role' => 'member']);
                }
            });
        });
        
        // Criar um usuário de teste específico
        $testUser = User::factory()->create([
            'name' => 'Davi Teste',
            'email' => 'davi@teste.com',
        ]);
        
        // Criar alguns projetos para o usuário de teste
        $testProjects = Project::factory(3)->create([
            'owner_id' => $testUser->id,
        ]);
        
        $testProjects->each(function ($project) use ($testUser) {
            $project->users()->attach($testUser->id, ['role' => 'owner']);
        });
    }
}
