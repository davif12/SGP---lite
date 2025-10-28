@if($tasks->count() > 0)
    <div class="row g-4">
        @foreach($tasks as $task)
            <div class="col-md-6 col-xl-4">
                <div class="card-modern h-100 task-card" data-task-id="{{ $task->id }}">
                    <div class="card-body">
                        <!-- Task Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-modern badge-{{ $task->priority_color }} me-2">
                                    {{ $task->priority_label }}
                                </span>
                                <small class="text-muted">#{{ $task->id }}</small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('projects.epics.tasks.show', [$project, $epic, $task]) }}">
                                            <i class="bi bi-eye me-2"></i>Ver Detalhes
                                        </a>
                                    </li>
                                    @can('update', $project)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('projects.epics.tasks.edit', [$project, $epic, $task]) }}">
                                                <i class="bi bi-pencil me-2"></i>Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('projects.epics.tasks.destroy', [$project, $epic, $task]) }}" 
                                                  onsubmit="return confirm('Tem certeza que deseja excluir esta task?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i>Excluir
                                                </button>
                                            </form>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </div>

                        <!-- Task Title -->
                        <h6 class="card-title task-title">
                            <a href="{{ route('projects.epics.tasks.show', [$project, $epic, $task]) }}" 
               class="text-decoration-none text-dark">
                                {{ $task->title }}
                            </a>
                        </h6>

                        <!-- Task Description -->
                        @if($task->description)
                            <p class="text-muted small mb-3">
                                {{ Str::limit($task->description, 100) }}
                            </p>
                        @endif

                        <!-- Task Meta -->
                        <div class="task-meta mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge-modern badge-{{ $task->status_color }}">
                                    {{ $task->status_label }}
                                </span>
                                @if($task->story_points)
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        {{ $task->story_points }} pts
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Task Footer -->
                        <div class="task-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Assignee -->
                                <div class="d-flex align-items-center">
                                    @if($task->assignedUser)
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 24px; height: 24px;">
                                            <span class="text-white small fw-bold">
                                                {{ substr($task->assignedUser->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <small class="text-muted">{{ $task->assignedUser->name }}</small>
                                    @else
                                        <small class="text-muted">
                                            <i class="bi bi-person-dash me-1"></i>Não atribuído
                                        </small>
                                    @endif
                                </div>

                                <!-- Due Date -->
                                @if($task->due_date)
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <span class="{{ $task->due_date->isPast() ? 'text-danger' : '' }}">
                                            {{ $task->due_date->format('d/m') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-4">
        <i class="bi bi-inbox display-4 text-muted"></i>
        <p class="text-muted mt-2">Nenhuma task neste status</p>
    </div>
@endif

<style>
.task-card {
    transition: all var(--transition-normal);
    cursor: pointer;
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.task-title a:hover {
    color: var(--primary) !important;
}

.task-meta {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 0.5rem;
}

[data-theme="dark"] .task-meta {
    background: var(--dark-border);
}
</style>
