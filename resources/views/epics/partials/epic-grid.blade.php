@if($epics->count() > 0)
    <div class="row g-4">
        @foreach($epics as $epic)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title">{{ $epic->name }}</h5>
                            <div class="d-flex gap-1">
                                <span class="badge {{ $epic->getStatusBadgeClass() }}">{{ $epic->getStatusLabel() }}</span>
                                <span class="badge {{ $epic->getPriorityBadgeClass() }}">{{ $epic->getPriorityLabel() }}</span>
                            </div>
                        </div>
                        
                        @if($epic->description)
                            <p class="card-text text-muted small">{{ Str::limit($epic->description, 120) }}</p>
                        @endif
                        
                        <div class="small text-muted mb-3">
                            <i class="bi bi-calendar3 me-1"></i>
                            Criado em {{ $epic->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="{{ route('projects.epics.show', [$project, $epic]) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="bi bi-eye me-1"></i>Ver
                            </a>
                            @can('update', $project)
                                <a href="{{ route('projects.epics.edit', [$project, $epic]) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('projects.epics.destroy', [$project, $epic]) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir este épico?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-4">
        <i class="bi bi-journal-text display-4 text-muted"></i>
        <p class="text-muted mt-3">Nenhum épico nesta categoria.</p>
    </div>
@endif
