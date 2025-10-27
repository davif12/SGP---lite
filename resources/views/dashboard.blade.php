<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h3 mb-0 text-gradient">Dashboard</h1>
            <p class="text-muted mb-0">Bem-vindo de volta, {{ Auth::user()->name }}!</p>
        </div>
    </x-slot>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        @php
            $ownedProjects = Auth::user()->projects()->count();
            $memberProjects = Auth::user()->memberProjects()->count();
            $projectCount = $ownedProjects + $memberProjects;
            $epicCount = \App\Models\Epic::whereHas('project', function($query) {
                $query->where('owner_id', Auth::id())
                      ->orWhereHas('users', function($q) {
                          $q->where('user_id', Auth::id());
                      });
            })->count();
            $epicsInProgress = \App\Models\Epic::whereHas('project', function($query) {
                $query->where('owner_id', Auth::id())
                      ->orWhereHas('users', function($q) {
                          $q->where('user_id', Auth::id());
                      });
            })->where('status', 'in_progress')->count();
            $epicsCompleted = \App\Models\Epic::whereHas('project', function($query) {
                $query->where('owner_id', Auth::id())
                      ->orWhereHas('users', function($q) {
                          $q->where('user_id', Auth::id());
                      });
            })->where('status', 'done')->count();
        @endphp

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="card-icon icon-primary">
                    <i class="bi bi-folder"></i>
                </div>
                <div class="card-value">{{ $projectCount }}</div>
                <div class="card-label">Projetos Ativos</div>
                <div class="card-change positive">
                    <i class="bi bi-arrow-up me-1"></i>+2 este mês
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="card-icon icon-success">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div class="card-value">{{ $epicCount }}</div>
                <div class="card-label">Total de Épicos</div>
                <div class="card-change positive">
                    <i class="bi bi-arrow-up me-1"></i>+{{ $epicCount }} novos
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="card-icon icon-warning">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="card-value">{{ $epicsInProgress }}</div>
                <div class="card-label">Em Progresso</div>
                <div class="card-change">
                    <i class="bi bi-dash me-1"></i>Estável
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="card-icon icon-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="card-value">{{ $epicsCompleted }}</div>
                <div class="card-label">Concluídos</div>
                <div class="card-change positive">
                    <i class="bi bi-arrow-up me-1"></i>+{{ $epicsCompleted }} finalizados
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="row g-4 mb-4">
        <!-- Progress Chart -->
        <div class="col-lg-8">
            <div class="card-modern">
                <div class="card-header">
                    <h5 class="card-title">Progresso dos Épicos</h5>
                </div>
                <div class="card-body">
                    <canvas id="progressChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="col-lg-4">
            <div class="card-modern">
                <div class="card-header">
                    <h5 class="card-title">Atividade Recente</h5>
                </div>
                <div class="card-body">
                    <div class="activity-feed">
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="bi bi-check-circle text-white"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Épico concluído</div>
                                <div class="activity-description">Sistema de Autenticação finalizado</div>
                                <div class="activity-time">2 horas atrás</div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon bg-primary">
                                <i class="bi bi-person-plus text-white"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Novo membro</div>
                                <div class="activity-description">João adicionado ao projeto Alpha</div>
                                <div class="activity-time">1 dia atrás</div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon bg-warning">
                                <i class="bi bi-exclamation-triangle text-white"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Épico atualizado</div>
                                <div class="activity-description">Dashboard Principal em progresso</div>
                                <div class="activity-time">2 dias atrás</div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon bg-info">
                                <i class="bi bi-folder-plus text-white"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Projeto criado</div>
                                <div class="activity-description">Novo projeto "Beta" iniciado</div>
                                <div class="activity-time">3 dias atrás</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Projetos Recentes</h5>
                    <a href="{{ route('projects.index') }}" class="btn btn-modern btn-primary btn-sm">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $recentProjects = \App\Models\Project::where('owner_id', Auth::id())
                            ->orWhereHas('users', function($query) {
                                $query->where('user_id', Auth::id());
                            })
                            ->with(['owner', 'epics'])
                            ->orderBy('updated_at', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp

                    @if($recentProjects->count() > 0)
                        <div class="row g-4">
                            @foreach($recentProjects as $project)
                                <div class="col-md-4">
                                    <div class="project-card">
                                        <div class="project-header">
                                            <h6 class="project-title">{{ $project->name }}</h6>
                                            @if($project->isOwner(Auth::user()))
                                                <span class="badge badge-modern badge-primary">Dono</span>
                                            @else
                                                <span class="badge badge-modern badge-secondary">Membro</span>
                                            @endif
                                        </div>
                                        @if($project->description)
                                            <p class="project-description">{{ Str::limit($project->description, 80) }}</p>
                                        @endif
                                        <div class="project-stats">
                                            <div class="stat-item">
                                                <i class="bi bi-journal-text me-1"></i>
                                                {{ $project->epics->count() }} épicos
                                            </div>
                                            <div class="stat-item">
                                                <i class="bi bi-people me-1"></i>
                                                {{ $project->users->count() }} membros
                                            </div>
                                        </div>
                                        <div class="project-actions">
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-modern btn-primary btn-sm">
                                                Ver Projeto
                                            </a>
                                            <a href="{{ route('epics.index', $project) }}" class="btn btn-modern btn-secondary btn-sm">
                                                Épicos
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-folder2-open display-4 text-muted"></i>
                            <h6 class="mt-3 text-muted">Nenhum projeto encontrado</h6>
                            <p class="text-muted">Comece criando seu primeiro projeto!</p>
                            <a href="{{ route('projects.create') }}" class="btn btn-modern btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Criar Projeto
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sprint Status -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-check-circle text-white fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h5 class="mb-1">Sprint 2 - Épicos Concluída! 🎉</h5>
                            <p class="text-muted mb-2">
                                CRUD de Épicos, Interface Bootstrap, Sistema de Status e Prioridades implementados com sucesso!
                            </p>
                            <div class="d-flex gap-2">
                                <span class="badge badge-modern badge-success">✅ Bootstrap 5</span>
                                <span class="badge badge-modern badge-success">✅ CRUD Épicos</span>
                                <span class="badge badge-modern badge-success">✅ Testes</span>
                                <span class="badge badge-modern badge-warning">🚀 Próximo: Tasks</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Progress Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('progressChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Backlog', 'Em Progresso', 'Concluído'],
                        datasets: [{
                            data: [
                                {{ \App\Models\Epic::whereHas('project', function($query) {
                                    $query->where('owner_id', Auth::id())
                                          ->orWhereHas('users', function($q) {
                                              $q->where('user_id', Auth::id());
                                          });
                                })->where('status', 'backlog')->count() }},
                                {{ $epicsInProgress }},
                                {{ $epicsCompleted }}
                            ],
                            backgroundColor: [
                                'var(--secondary)',
                                'var(--primary)',
                                'var(--success)'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <style>
        .activity-feed {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .activity-item:last-child {
            margin-bottom: 0;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 0.75rem;
        }

        .activity-content {
            flex-grow: 1;
        }

        .activity-title {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--gray-800);
        }

        .activity-description {
            font-size: 0.8rem;
            color: var(--gray-600);
            margin: 0.25rem 0;
        }

        .activity-time {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .project-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            transition: all var(--transition-normal);
        }

        .project-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .project-title {
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }

        .project-description {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .project-stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            font-size: 0.8rem;
            color: var(--gray-600);
            display: flex;
            align-items: center;
        }

        .project-actions {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</x-app-layout>
