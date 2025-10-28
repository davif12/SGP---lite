<?php

namespace Database\Seeders;

use App\Models\Epic;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $epics = Epic::all();

        if ($epics->isEmpty()) {
            $this->command->info('No epics found. Please run EpicSeeder first.');
            return;
        }

        foreach ($epics as $epic) {
            // Create 3-8 tasks per epic
            $taskCount = rand(3, 8);
            
            for ($i = 0; $i < $taskCount; $i++) {
                Task::create([
                    'title' => $this->getTaskTitle($epic->name, $i + 1),
                    'description' => $this->getTaskDescription(),
                    'status' => $this->getRandomStatus(),
                    'priority' => $this->getRandomPriority(),
                    'story_points' => rand(1, 8),
                    'position' => $i,
                    'due_date' => rand(0, 1) ? now()->addDays(rand(1, 30)) : null,
                    'epic_id' => $epic->id,
                    'assigned_to' => $users->random()->id,
                ]);
            }
        }

        $this->command->info('Tasks seeded successfully!');
    }

    private function getTaskTitle($epicName, $taskNumber)
    {
        $taskTitles = [
            'Implementar autenticação OAuth',
            'Criar testes unitários',
            'Configurar CI/CD pipeline',
            'Desenvolver API endpoints',
            'Criar documentação técnica',
            'Implementar validações',
            'Configurar banco de dados',
            'Desenvolver interface de usuário',
            'Implementar sistema de logs',
            'Criar backup automático',
            'Configurar monitoramento',
            'Implementar cache Redis',
            'Desenvolver sistema de notificações',
            'Criar relatórios automáticos',
            'Implementar sistema de busca',
        ];

        return $taskTitles[array_rand($taskTitles)] . " - {$epicName} #{$taskNumber}";
    }

    private function getTaskDescription()
    {
        $descriptions = [
            'Implementar funcionalidade seguindo as melhores práticas de desenvolvimento.',
            'Desenvolver solução robusta e escalável para o sistema.',
            'Criar implementação eficiente com foco na performance.',
            'Desenvolver funcionalidade com testes automatizados.',
            'Implementar solução seguindo padrões de arquitetura.',
            null, // Some tasks may not have description
        ];

        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomStatus()
    {
        $statuses = ['todo', 'in_progress', 'review', 'done'];
        $weights = [40, 30, 20, 10]; // More tasks in todo/in_progress
        
        return $this->weightedRandom($statuses, $weights);
    }

    private function getRandomPriority()
    {
        $priorities = ['low', 'medium', 'high', 'critical'];
        $weights = [20, 50, 25, 5]; // Most tasks are medium priority
        
        return $this->weightedRandom($priorities, $weights);
    }

    private function weightedRandom($values, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        for ($i = 0; $i < count($values); $i++) {
            $random -= $weights[$i];
            if ($random <= 0) {
                return $values[$i];
            }
        }
        
        return $values[0];
    }
}
