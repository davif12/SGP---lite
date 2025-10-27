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
                        <li class="breadcrumb-item active">Épicos</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 mt-2 text-gradient">Épicos - {{ $project->name }}</h1>
                <p class="text-muted mb-0">Organize funcionalidades em épicos</p>
            </div>
            @can('update', $project)
                <a href="{{ route('projects.epics.create', $project) }}" class="btn btn-modern btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Novo Épico
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

    @if($epics->count() > 0)
        <!-- Status Filter Tabs -->
        <div class="card-modern mb-4">
            <div class="card-body p-0">
                <ul class="nav nav-tabs border-0" id="statusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-0" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                            <i class="bi bi-list-ul me-1"></i>Todos 
                            <span class="badge badge-modern badge-secondary ms-1">{{ $epics->count() }}</span>
                        </button>
                    </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="backlog-tab" data-bs-toggle="tab" data-bs-target="#backlog" type="button" role="tab">
                                        Backlog <span class="badge bg-secondary ms-1">{{ $epics->where('status', 'backlog')->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="in-progress-tab" data-bs-toggle="tab" data-bs-target="#in-progress" type="button" role="tab">
                                        Em Progresso <span class="badge bg-primary ms-1">{{ $epics->where('status', 'in_progress')->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="done-tab" data-bs-toggle="tab" data-bs-target="#done" type="button" role="tab">
                                        Concluído <span class="badge bg-success ms-1">{{ $epics->where('status', 'done')->count() }}</span>
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="statusTabsContent">
                                <!-- All Epics -->
                                <div class="tab-pane fade show active" id="all" role="tabpanel">
                                    @include('epics.partials.epic-grid', ['epics' => $epics])
                                </div>
                                
                                <!-- Backlog Epics -->
                                <div class="tab-pane fade" id="backlog" role="tabpanel">
                                    @include('epics.partials.epic-grid', ['epics' => $epics->where('status', 'backlog')])
                                </div>
                                
                                <!-- In Progress Epics -->
                                <div class="tab-pane fade" id="in-progress" role="tabpanel">
                                    @include('epics.partials.epic-grid', ['epics' => $epics->where('status', 'in_progress')])
                                </div>
                                
                                <!-- Done Epics -->
                                <div class="tab-pane fade" id="done" role="tabpanel">
                                    @include('epics.partials.epic-grid', ['epics' => $epics->where('status', 'done')])
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-journal-text display-1 text-muted"></i>
                                </div>
                                <h5 class="text-muted">Nenhum épico encontrado</h5>
                                <p class="text-muted">Comece criando seu primeiro épico para organizar as funcionalidades do projeto.</p>
                                @can('update', $project)
                                    <div class="mt-4">
                                        <a href="{{ route('projects.epics.create', $project) }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>Criar Épico
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
