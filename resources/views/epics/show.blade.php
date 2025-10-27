<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.index') }}">Projetos</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('epics.index', $project) }}">Épicos</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $epic->name }}</li>
                    </ol>
                </nav>
                <h2 class="h3 mb-0 mt-2">{{ $epic->name }}</h2>
            </div>
            @can('update', $project)
                <div class="d-flex gap-2">
                    <a href="{{ route('projects.epics.edit', [$project, $epic]) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <form action="{{ route('projects.epics.destroy', [$project, $epic]) }}" method="POST" class="d-inline" 
                          onsubmit="return confirm('Tem certeza que deseja excluir este épico?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Excluir
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <!-- Epic Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-journal-text me-2"></i>Detalhes do Épico
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Status:</strong>
                            </div>
                            <div class="col-sm-9">
                                <span class="badge {{ $epic->getStatusBadgeClass() }} fs-6">
                                    {{ $epic->getStatusLabel() }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Prioridade:</strong>
                            </div>
                            <div class="col-sm-9">
                                <span class="badge {{ $epic->getPriorityBadgeClass() }} fs-6">
                                    {{ $epic->getPriorityLabel() }}
                                </span>
                            </div>
                        </div>

                        @if($epic->description)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Descrição:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <p class="mb-0">{{ $epic->description }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Projeto:</strong>
                            </div>
                            <div class="col-sm-9">
                                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                    {{ $project->name }}
                                </a>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Criado em:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $epic->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        @if($epic->updated_at != $epic->created_at)
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Atualizado em:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $epic->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Future: Tasks/Features will be listed here -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-task me-2"></i>Tasks/Features
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-gear display-4"></i>
                            <p class="mt-3">Funcionalidade em desenvolvimento</p>
                            <small>As tasks/features serão implementadas na próxima sprint.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Actions Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Ações Rápidas</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('epics.index', $project) }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-1"></i>Voltar aos Épicos
                            </a>
                            @can('update', $project)
                                <a href="{{ route('projects.epics.edit', [$project, $epic]) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil me-1"></i>Editar Épico
                                </a>
                                <a href="{{ route('projects.epics.create', $project) }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>Novo Épico
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Project Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Informações do Projeto</h6>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{{ $project->name }}</h6>
                        @if($project->description)
                            <p class="card-text small text-muted">{{ $project->description }}</p>
                        @endif
                        <div class="small text-muted">
                            <div class="mb-1">
                                <strong>Dono:</strong> {{ $project->owner->name }}
                            </div>
                            <div class="mb-1">
                                <strong>Membros:</strong> {{ $project->users->count() }}
                            </div>
                            <div>
                                <strong>Épicos:</strong> {{ $project->epics->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
