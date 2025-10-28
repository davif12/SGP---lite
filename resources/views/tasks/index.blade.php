<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.index') }}" class="text-decoration-none">Projetos</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">{{ $project->name }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('epics.index', $project) }}" class="text-decoration-none">Épicos</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.epics.show', [$project, $epic]) }}" class="text-decoration-none">{{ $epic->name }}</a>
                        </li>
                        <li class="breadcrumb-item active">Tasks</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 mt-2 text-gradient">Tasks - {{ $epic->name }}</h1>
                <p class="text-muted mb-0">Gerencie as tarefas do épico</p>
            </div>
            @can('update', $project)
                <a href="{{ route('projects.epics.tasks.create', [$project, $epic]) }}" class="btn btn-modern btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Nova Task
                </a>
            @endcan
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($tasks->count() > 0)
        <!-- Status Filter Tabs -->
        <div class="card-modern mb-4">
            <div class="card-body p-0">
                <ul class="nav nav-tabs border-0" id="statusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-0" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                            <i class="bi bi-list-ul me-1"></i>Todas 
                            <span class="badge badge-modern badge-secondary ms-1">{{ $tasks->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-0" id="todo-tab" data-bs-toggle="tab" data-bs-target="#todo" type="button" role="tab">
                            <i class="bi bi-circle me-1"></i>A Fazer
                            <span class="badge badge-modern badge-secondary ms-1">{{ $tasks->where('status', 'todo')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-0" id="in-progress-tab" data-bs-toggle="tab" data-bs-target="#in-progress" type="button" role="tab">
                            <i class="bi bi-arrow-right-circle me-1"></i>Em Progresso
                            <span class="badge badge-modern badge-primary ms-1">{{ $tasks->where('status', 'in_progress')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-0" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab">
                            <i class="bi bi-eye me-1"></i>Em Revisão
                            <span class="badge badge-modern badge-warning ms-1">{{ $tasks->where('status', 'review')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-0" id="done-tab" data-bs-toggle="tab" data-bs-target="#done" type="button" role="tab">
                            <i class="bi bi-check-circle me-1"></i>Concluído
                            <span class="badge badge-modern badge-success ms-1">{{ $tasks->where('status', 'done')->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="statusTabContent">
            <!-- All Tasks -->
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                @include('tasks.partials.task-grid', ['tasks' => $tasks])
            </div>

            <!-- Todo Tasks -->
            <div class="tab-pane fade" id="todo" role="tabpanel">
                @include('tasks.partials.task-grid', ['tasks' => $tasks->where('status', 'todo')])
            </div>

            <!-- In Progress Tasks -->
            <div class="tab-pane fade" id="in-progress" role="tabpanel">
                @include('tasks.partials.task-grid', ['tasks' => $tasks->where('status', 'in_progress')])
            </div>

            <!-- Review Tasks -->
            <div class="tab-pane fade" id="review" role="tabpanel">
                @include('tasks.partials.task-grid', ['tasks' => $tasks->where('status', 'review')])
            </div>

            <!-- Done Tasks -->
            <div class="tab-pane fade" id="done" role="tabpanel">
                @include('tasks.partials.task-grid', ['tasks' => $tasks->where('status', 'done')])
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-list-task display-1 text-muted"></i>
            </div>
            <h5 class="text-muted">Nenhuma task encontrada</h5>
            <p class="text-muted">Comece criando a primeira task para este épico.</p>
            <div class="mt-4">
                @can('update', $project)
                    <a href="{{ route('projects.epics.tasks.create', [$project, $epic]) }}" class="btn btn-modern btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Criar Primeira Task
                    </a>
                @endcan
            </div>
        </div>
    @endif
</x-app-layout>
