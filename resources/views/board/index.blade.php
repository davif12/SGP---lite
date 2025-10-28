<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gradient">Board Kanban</h1>
                <p class="text-muted mb-0">Selecione um projeto para visualizar o board</p>
            </div>
        </div>
    </x-slot>

    @if($projects->count() > 0)
        <!-- Project Selection -->
        <div class="row g-4">
            @foreach($projects as $project)
                <div class="col-md-6 col-xl-4">
                    <div class="card-modern h-100 project-board-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="bi bi-kanban text-white fs-3"></i>
                                </div>
                            </div>
                            
                            <h5 class="card-title">{{ $project->name }}</h5>
                            
                            @if($project->description)
                                <p class="text-muted small mb-3">{{ Str::limit($project->description, 80) }}</p>
                            @endif
                            
                            <!-- Project Stats -->
                            <div class="row g-2 mb-4">
                                <div class="col-4">
                                    <div class="text-center">
                                        <div class="fw-bold text-primary">{{ $project->epics->count() }}</div>
                                        <small class="text-muted">Épicos</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        @php
                                            $totalTasks = $project->epics->sum(function($epic) {
                                                return $epic->tasks->count();
                                            });
                                        @endphp
                                        <div class="fw-bold text-success">{{ $totalTasks }}</div>
                                        <small class="text-muted">Tasks</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <div class="fw-bold text-info">{{ $project->users->count() + 1 }}</div>
                                        <small class="text-muted">Membros</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Task Status Overview -->
                            @php
                                $allTasks = $project->epics->flatMap->tasks;
                                $todoTasks = $allTasks->where('status', 'todo')->count();
                                $inProgressTasks = $allTasks->where('status', 'in_progress')->count();
                                $reviewTasks = $allTasks->where('status', 'review')->count();
                                $doneTasks = $allTasks->where('status', 'done')->count();
                            @endphp
                            
                            @if($totalTasks > 0)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Progresso</span>
                                        <span>{{ $doneTasks }}/{{ $totalTasks }}</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" 
                                             style="width: {{ $totalTasks > 0 ? ($doneTasks / $totalTasks) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-center gap-2 mb-3">
                                    @if($todoTasks > 0)
                                        <span class="badge badge-modern badge-secondary">{{ $todoTasks }} A Fazer</span>
                                    @endif
                                    @if($inProgressTasks > 0)
                                        <span class="badge badge-modern badge-primary">{{ $inProgressTasks }} Em Progresso</span>
                                    @endif
                                    @if($reviewTasks > 0)
                                        <span class="badge badge-modern badge-warning">{{ $reviewTasks }} Revisão</span>
                                    @endif
                                    @if($doneTasks > 0)
                                        <span class="badge badge-modern badge-success">{{ $doneTasks }} Concluído</span>
                                    @endif
                                </div>
                            @endif
                            
                            <a href="{{ route('board.index', $project) }}" class="btn btn-modern btn-primary">
                                <i class="bi bi-kanban me-1"></i>Abrir Board
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-kanban display-1 text-muted"></i>
            </div>
            <h5 class="text-muted">Nenhum projeto encontrado</h5>
            <p class="text-muted">Você precisa ter projetos com épicos e tasks para usar o board Kanban.</p>
            <div class="mt-4">
                <a href="{{ route('projects.create') }}" class="btn btn-modern btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Criar Primeiro Projeto
                </a>
            </div>
        </div>
    @endif

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Tarefa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addTaskForm">
                        <div class="mb-3">
                            <label for="task_title" class="form-label">Título da Tarefa</label>
                            <input type="text" class="form-control" id="task_title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="task_description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="task_description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="task_priority" class="form-label">Prioridade</label>
                                    <select class="form-select" id="task_priority" required>
                                        <option value="low">Baixa</option>
                                        <option value="medium" selected>Média</option>
                                        <option value="high">Alta</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="task_status" class="form-label">Status</label>
                                    <select class="form-select" id="task_status" required>
                                        <option value="backlog" selected>Backlog</option>
                                        <option value="in_progress">Em Progresso</option>
                                        <option value="review">Em Revisão</option>
                                        <option value="done">Concluído</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="task_assignee" class="form-label">Responsável</label>
                            <select class="form-select" id="task_assignee">
                                <option value="">Não atribuído</option>
                                <option value="joão">João Silva</option>
                                <option value="maria">Maria Santos</option>
                                <option value="pedro">Pedro Costa</option>
                                <option value="ana">Ana Lima</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addTask()">Criar Tarefa</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Board functionality
        function showAddTaskModal(status = 'backlog') {
            const modal = new bootstrap.Modal(document.getElementById('addTaskModal'));
            document.getElementById('task_status').value = status;
            modal.show();
        }

        function addTask() {
            const form = document.getElementById('addTaskForm');
            const formData = new FormData(form);
            
            // Simulate adding task (in real implementation, this would be an API call)
            const taskData = {
                id: Date.now(),
                title: document.getElementById('task_title').value,
                priority: document.getElementById('task_priority').value,
                assignee: document.getElementById('task_assignee').value || 'Não atribuído'
            };
            
            const status = document.getElementById('task_status').value;
            
            if (window.kanbanBoard) {
                window.kanbanBoard.addCard(status, taskData);
            }
            
            // Close modal and reset form
            bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
            form.reset();
            
            // Show success message
            if (window.kanbanBoard) {
                window.kanbanBoard.showNotification('Tarefa criada com sucesso!', 'success');
            }
        }

        function refreshBoard() {
            if (window.kanbanBoard) {
                window.kanbanBoard.refreshBoard();
            } else {
                location.reload();
            }
        }

        // Initialize board on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Update counters on load
            if (window.kanbanBoard) {
                window.kanbanBoard.updateColumnCounters();
            }
        });
    </script>

    <style>
        /* Additional board-specific styles */
        .kanban-board {
            min-height: calc(100vh - 200px);
            padding-bottom: 2rem;
        }

        .kanban-column {
            flex-shrink: 0;
        }

        .add-task-btn {
            border: 2px dashed var(--gray-300);
            border-radius: var(--radius-md);
            padding: 0.75rem;
            transition: all var(--transition-fast);
        }

        .add-task-btn:hover {
            border-color: var(--primary);
            background-color: var(--primary);
            color: white !important;
        }

        /* Sortable.js styles */
        .kanban-ghost {
            opacity: 0.4;
        }

        .kanban-chosen {
            transform: rotate(5deg);
        }

        .kanban-drag {
            transform: rotate(5deg);
            box-shadow: var(--shadow-xl);
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .kanban-board {
                flex-direction: column;
                padding: 1rem;
            }
            
            .kanban-column {
                min-width: 100%;
                margin-bottom: 1rem;
            }
        }

        /* Dark theme support */
        [data-theme="dark"] .kanban-column {
            background: var(--dark-surface);
        }

        [data-theme="dark"] .kanban-card {
            background: var(--dark-bg);
            border-color: var(--dark-border);
        }

        [data-theme="dark"] .add-task-btn {
            border-color: var(--dark-border);
            color: var(--gray-400);
        }

        [data-theme="dark"] .add-task-btn:hover {
            border-color: var(--primary);
            background-color: var(--primary);
        }
    </style>
</x-app-layout>
