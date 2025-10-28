<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('board.index') }}" class="text-decoration-none">Board Kanban</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $project->name }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 mt-2 text-gradient">{{ $project->name }} - Board</h1>
                <p class="text-muted mb-0">Gerencie suas tasks com drag & drop</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-modern btn-secondary btn-sm" onclick="refreshBoard()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Atualizar
                </button>
                <div class="dropdown">
                    <button class="btn btn-modern btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-plus-circle me-1"></i>Nova Task
                    </button>
                    <ul class="dropdown-menu">
                        @foreach($project->epics as $epic)
                            <li>
                                <a class="dropdown-item" href="{{ route('projects.epics.tasks.create', [$project, $epic]) }}">
                                    <i class="bi bi-journal-text me-2"></i>{{ $epic->name }}
                                </a>
                            </li>
                        @endforeach
                        @if($project->epics->isEmpty())
                            <li><span class="dropdown-item-text text-muted">Nenhum épico disponível</span></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Board Container -->
    <div class="kanban-board" id="kanbanBoard">
        <!-- Todo Column -->
        <div class="kanban-column">
            <div class="kanban-header">
                <h6><i class="bi bi-circle me-1"></i>A Fazer</h6>
                <span class="badge bg-secondary task-count">{{ $tasks->get('todo', collect())->count() }}</span>
            </div>
            <div class="kanban-cards" data-status="todo" id="todo-column">
                @foreach($tasks->get('todo', collect()) as $task)
                    @include('board.partials.task-card', ['task' => $task])
                @endforeach
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column">
            <div class="kanban-header">
                <h6><i class="bi bi-arrow-right-circle me-1"></i>Em Progresso</h6>
                <span class="badge bg-primary task-count">{{ $tasks->get('in_progress', collect())->count() }}</span>
            </div>
            <div class="kanban-cards" data-status="in_progress" id="in_progress-column">
                @foreach($tasks->get('in_progress', collect()) as $task)
                    @include('board.partials.task-card', ['task' => $task])
                @endforeach
            </div>
        </div>

        <!-- Review Column -->
        <div class="kanban-column">
            <div class="kanban-header">
                <h6><i class="bi bi-eye me-1"></i>Em Revisão</h6>
                <span class="badge bg-warning task-count">{{ $tasks->get('review', collect())->count() }}</span>
            </div>
            <div class="kanban-cards" data-status="review" id="review-column">
                @foreach($tasks->get('review', collect()) as $task)
                    @include('board.partials.task-card', ['task' => $task])
                @endforeach
            </div>
        </div>

        <!-- Done Column -->
        <div class="kanban-column">
            <div class="kanban-header">
                <h6><i class="bi bi-check-circle me-1"></i>Concluído</h6>
                <span class="badge bg-success task-count">{{ $tasks->get('done', collect())->count() }}</span>
            </div>
            <div class="kanban-cards" data-status="done" id="done-column">
                @foreach($tasks->get('done', collect()) as $task)
                    @include('board.partials.task-card', ['task' => $task])
                @endforeach
            </div>
        </div>
    </div>

    <!-- Task Detail Modal -->
    <div class="modal fade" id="taskDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalTitle">Detalhes da Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="taskModalBody">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Kanban Board
        document.addEventListener('DOMContentLoaded', function() {
            initializeKanbanBoard();
        });

        function initializeKanbanBoard() {
            const columns = ['todo', 'in_progress', 'review', 'done'];
            
            columns.forEach(status => {
                const column = document.getElementById(status + '-column');
                if (column) {
                    new Sortable(column, {
                        group: 'kanban',
                        animation: 150,
                        ghostClass: 'kanban-ghost',
                        chosenClass: 'kanban-chosen',
                        dragClass: 'kanban-drag',
                        onEnd: function(evt) {
                            const taskId = evt.item.dataset.taskId;
                            const newStatus = evt.to.dataset.status;
                            const newPosition = evt.newIndex;
                            
                            moveTask(taskId, newStatus, newPosition);
                        }
                    });
                }
            });
        }

        function moveTask(taskId, status, position) {
            fetch(`/api/tasks/${taskId}/move`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: status,
                    position: position
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    updateColumnCounters();
                } else {
                    showNotification('Erro ao mover task', 'error');
                    // Revert the move
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erro ao mover task', 'error');
                location.reload();
            });
        }

        function updateColumnCounters() {
            const columns = ['todo', 'in_progress', 'review', 'done'];
            
            columns.forEach(status => {
                const column = document.getElementById(status + '-column');
                const counter = column.parentElement.querySelector('.task-count');
                const taskCount = column.children.length;
                
                if (counter) {
                    counter.textContent = taskCount;
                }
            });
        }

        function showTaskDetails(taskId) {
            // Load task details via AJAX
            fetch(`/api/tasks/${taskId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('taskModalTitle').textContent = data.title;
                    document.getElementById('taskModalBody').innerHTML = data.html;
                    
                    const modal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error loading task details:', error);
                    showNotification('Erro ao carregar detalhes da task', 'error');
                });
        }

        function refreshBoard() {
            location.reload();
        }

        function showNotification(message, type = 'info') {
            const toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) return;

            const toastId = 'toast-' + Date.now();
            const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
            
            const toastHtml = `
                <div id="${toastId}" class="toast ${bgClass} text-white" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            const toast = new bootstrap.Toast(document.getElementById(toastId));
            toast.show();
            
            // Remove toast element after it's hidden
            document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }
    </script>

    <style>
        .kanban-board {
            display: flex;
            gap: 1.5rem;
            min-height: calc(100vh - 200px);
            padding-bottom: 2rem;
            overflow-x: auto;
        }

        .kanban-column {
            flex: 1;
            min-width: 280px;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .kanban-header {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content-between;
            align-items: center;
            background: var(--gray-50);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        .kanban-cards {
            padding: 1rem;
            min-height: 200px;
        }

        .kanban-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 0.75rem;
            cursor: grab;
            transition: all var(--transition-fast);
            box-shadow: var(--shadow-sm);
        }

        .kanban-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .kanban-card:active {
            cursor: grabbing;
        }

        .kanban-ghost {
            opacity: 0.4;
        }

        .kanban-chosen {
            transform: rotate(2deg);
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
            border-color: var(--dark-border);
        }

        [data-theme="dark"] .kanban-header {
            background: var(--dark-border);
            border-color: var(--dark-border);
        }

        [data-theme="dark"] .kanban-card {
            background: var(--dark-bg);
            border-color: var(--dark-border);
        }
    </style>
</x-app-layout>
