<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gradient">Meus Projetos</h1>
                <p class="text-muted mb-0">Gerencie seus projetos e equipes</p>
            </div>
            <a href="{{ route('projects.create') }}" class="btn btn-modern btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Novo Projeto
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($projects->count() > 0)
        <div class="row g-4">
            @foreach($projects as $project)
                <div class="col-md-6 col-xl-4">
                    <div class="card-modern h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title">{{ $project->name }}</h5>
                                @if($project->isOwner(auth()->user()))
                                    <span class="badge badge-modern badge-primary">Dono</span>
                                @else
                                    <span class="badge badge-modern badge-secondary">Membro</span>
                                @endif
                            </div>
                            
                            @if($project->description)
                                <p class="text-muted small mb-3">{{ Str::limit($project->description, 100) }}</p>
                            @endif
                            
                            <div class="project-stats mb-3">
                                <div class="d-flex justify-content-between small text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $project->owner->name }}
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-people me-1"></i>
                                        {{ $project->users->count() }} membros
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mt-1">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-journal-text me-1"></i>
                                        {{ $project->epics->count() }} épicos
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $project->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-modern btn-primary btn-sm flex-fill">
                                    <i class="bi bi-eye me-1"></i>Ver Projeto
                                </a>
                                <a href="{{ route('epics.index', $project) }}" class="btn btn-modern btn-secondary btn-sm">
                                    <i class="bi bi-journal-text me-1"></i>Épicos
                                </a>
                                @if($project->isOwner(auth()->user()))
                                    <div class="dropdown">
                                        <button class="btn btn-modern btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('projects.edit', $project) }}">
                                                <i class="bi bi-pencil me-2"></i>Editar
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete('{{ $project->id }}')">
                                                <i class="bi bi-trash me-2"></i>Excluir
                                            </a></li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-folder2-open display-1 text-muted"></i>
            </div>
            <h5 class="text-muted">Nenhum projeto encontrado</h5>
            <p class="text-muted">Comece criando seu primeiro projeto para organizar suas tarefas.</p>
            <div class="mt-4">
                <a href="{{ route('projects.create') }}" class="btn btn-modern btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Criar Primeiro Projeto
                </a>
            </div>
        </div>
    @endif

    <script>
        function confirmDelete(projectId) {
            if (confirm('Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita.')) {
                // Create and submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/projects/${projectId}`;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <style>
        .project-stats {
            background: var(--gray-50);
            border-radius: var(--radius-md);
            padding: 0.75rem;
        }

        [data-theme="dark"] .project-stats {
            background: var(--dark-border);
        }
    </style>
</x-app-layout>
