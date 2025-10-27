<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h3 mb-0 text-gradient">Dashboard</h1>
            <p class="text-muted mb-0">Bem-vindo de volta, {{ Auth::user()->name }}!</p>
        </div>
    </x-slot>

    <!-- Simple Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="card-icon icon-primary">
                    <i class="bi bi-folder"></i>
                </div>
                <div class="card-value">0</div>
                <div class="card-label">Projetos Ativos</div>
                <div class="card-change positive">
                    <i class="bi bi-arrow-up me-1"></i>Iniciando
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="card-icon icon-success">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div class="card-value">0</div>
                <div class="card-label">Total de Épicos</div>
                <div class="card-change positive">
                    <i class="bi bi-arrow-up me-1"></i>Novo
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="card-icon icon-warning">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="card-value">0</div>
                <div class="card-label">Em Progresso</div>
                <div class="card-change">
                    <i class="bi bi-dash me-1"></i>Aguardando
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="card-icon icon-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="card-value">0</div>
                <div class="card-label">Concluídos</div>
                <div class="card-change positive">
                    <i class="bi bi-arrow-up me-1"></i>Pronto para começar
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-rocket display-1 text-primary"></i>
                    </div>
                    <h3 class="mb-3">Bem-vindo ao SGP Lite!</h3>
                    <p class="text-muted mb-4">
                        Sistema de Gestão de Projetos moderno com interface renovada. 
                        Comece criando seu primeiro projeto para organizar suas tarefas.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('projects.create') }}" class="btn btn-modern btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Criar Primeiro Projeto
                        </a>
                        <a href="{{ route('board.index') }}" class="btn btn-modern btn-secondary">
                            <i class="bi bi-kanban me-1"></i>Ver Board Kanban
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-modern text-center h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-folder display-4 text-primary"></i>
                    </div>
                    <h5>Gestão de Projetos</h5>
                    <p class="text-muted">Organize seus projetos e gerencie equipes de forma eficiente.</p>
                    <a href="{{ route('projects.index') }}" class="btn btn-modern btn-outline-primary btn-sm">
                        Ver Projetos
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-modern text-center h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-journal-text display-4 text-success"></i>
                    </div>
                    <h5>Épicos & Funcionalidades</h5>
                    <p class="text-muted">Organize grandes funcionalidades em épicos para melhor controle.</p>
                    <button class="btn btn-modern btn-outline-success btn-sm" disabled>
                        Criar Projeto Primeiro
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-modern text-center h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-kanban display-4 text-warning"></i>
                    </div>
                    <h5>Board Kanban</h5>
                    <p class="text-muted">Visualize o progresso das tarefas com drag & drop intuitivo.</p>
                    <a href="{{ route('board.index') }}" class="btn btn-modern btn-outline-warning btn-sm">
                        Ver Board
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sprint Status -->
    <div class="row g-4 mt-2">
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
                            <h5 class="mb-1">Interface Moderna Implementada! 🎉</h5>
                            <p class="text-muted mb-2">
                                Nova interface com Bootstrap 5, design moderno e funcionalidades avançadas implementadas com sucesso!
                            </p>
                            <div class="d-flex gap-2">
                                <span class="badge badge-modern badge-success">✅ Bootstrap 5</span>
                                <span class="badge badge-modern badge-success">✅ Design Moderno</span>
                                <span class="badge badge-modern badge-success">✅ Responsivo</span>
                                <span class="badge badge-modern badge-success">✅ Kanban Board</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
