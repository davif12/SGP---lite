<?php

namespace Database\Seeders;

use App\Models\Epic;
use App\Models\Project;
use Illuminate\Database\Seeder;

class EpicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projects = Project::all();

        if ($projects->count() > 0) {
            $epicsData = [
                [
                    'name' => 'Sistema de Autenticação',
                    'description' => 'Implementar sistema completo de login, registro e recuperação de senha.',
                    'status' => 'done',
                    'priority' => 'high',
                ],
                [
                    'name' => 'Dashboard Principal',
                    'description' => 'Criar dashboard com métricas e visão geral dos projetos.',
                    'status' => 'in_progress',
                    'priority' => 'high',
                ],
                [
                    'name' => 'Sistema de Notificações',
                    'description' => 'Implementar notificações por email e in-app.',
                    'status' => 'backlog',
                    'priority' => 'medium',
                ],
                [
                    'name' => 'API REST',
                    'description' => 'Desenvolver API REST para integração com aplicativos móveis.',
                    'status' => 'backlog',
                    'priority' => 'low',
                ],
                [
                    'name' => 'Relatórios e Analytics',
                    'description' => 'Sistema de relatórios com gráficos e métricas de produtividade.',
                    'status' => 'backlog',
                    'priority' => 'medium',
                ],
            ];

            foreach ($projects as $project) {
                foreach ($epicsData as $epicData) {
                    Epic::create(array_merge($epicData, ['project_id' => $project->id]));
                }
            }
        }
    }
}
