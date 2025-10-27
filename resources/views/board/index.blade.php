<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gradient">Board Kanban</h1>
                <p class="text-muted mb-0">Gerencie suas tarefas com drag & drop</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-modern btn-secondary btn-sm" onclick="refreshBoard()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Atualizar
                </button>
                <button class="btn btn-modern btn-primary btn-sm" onclick="showAddTaskModal()">
                    <i class="bi bi-plus-circle me-1"></i>Nova Tarefa
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Board Container -->
    <div class="kanban-board">
        <!-- Backlog Column -->
        <div class="kanban-column">
            <div class="kanban-header">
                <h6>📋 Backlog</h6>
                <span class="badge bg-secondary task-count">3</span>
            </div>
            <div class="kanban-cards" data-status="backlog">
                <!-- Sample Cards -->
                <div class="kanban-card" data-task-id="1">
                    <div class="card-title">Implementar autenticação OAuth</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-danger">Alta</span>
                        <small class="text-muted">João Silva</small>
                    </div>
                </div>
                
                <div class="kanban-card" data-task-id="2">
                    <div class="card-title">Criar testes unitários</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-warning">Média</span>
                        <small class="text-muted">Maria Santos</small>
                    </div>
                </div>
                
                <div class="kanban-card" data-task-id="3">
                    <div class="card-title">Documentar API endpoints</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-success">Baixa</span>
                        <small class="text-muted">Pedro Costa</small>
                    </div>
                </div>
            </div>
            <button class="btn btn-link text-muted w-100 add-task-btn" data-status="backlog">
                <i class="bi bi-plus me-1"></i>Adicionar tarefa
            </button>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column">
            <div class="kanban-header">
                <h6>🚀 Em Progresso</h6>
                <span class="badge bg-primary task-count">2</span>
            </div>
            <div class="kanban-cards" data-status="in_progress">
                <div class="kanban-card" data-task-id="4">
                    <div class="card-title">Desenvolver dashboard</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-danger">Alta</span>
                        <small class="text-muted">Ana Lima</small>
                    </div>
                </div>
                
                <div class="kanban-card" data-task-id="5">
                    <div class="card-title">Integrar sistema de pagamento</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-warning">Média</span>
                        <small class="text-muted">Carlos Oliveira</small>
                    </div>
                </div>
            </div>
            <button class="btn btn-link text-muted w-100 add-task-btn" data-status="in_progress">
                <i class="bi bi-plus me-1"></i>Adicionar tarefa
            </button>
        </div>

        <!-- Review Column -->
        <div class="kanban-column">
            <div class="kanban-header">
                <h6>👀 Em Revisão</h6>
                <span class="badge bg-warning task-count">1</span>
            </div>
            <div class="kanban-cards" data-status="review">
                <div class="kanban-card" data-task-id="6">
                    <div class="card-title">Implementar notificações push</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-warning">Média</span>
                        <small class="text-muted">Lucas Ferreira</small>
                    </div>
                </div>
            </div>
            <button class="btn btn-link text-muted w-100 add-task-btn" data-status="review">
                <i class="bi bi-plus me-1"></i>Adicionar tarefa
            </button>
        </div>

        <!-- Done Column -->
        <div class="kanban-column">
            <div class="kanban-header">
                <h6>✅ Concluído</h6>
                <span class="badge bg-success task-count">4</span>
            </div>
            <div class="kanban-cards" data-status="done">
                <div class="kanban-card" data-task-id="7">
                    <div class="card-title">Configurar CI/CD pipeline</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-success">Baixa</span>
                        <small class="text-muted">Roberto Silva</small>
                    </div>
                </div>
                
                <div class="kanban-card" data-task-id="8">
                    <div class="card-title">Criar layout responsivo</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-warning">Média</span>
                        <small class="text-muted">Fernanda Costa</small>
                    </div>
                </div>
                
                <div class="kanban-card" data-task-id="9">
                    <div class="card-title">Implementar sistema de login</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-danger">Alta</span>
                        <small class="text-muted">Gabriel Santos</small>
                    </div>
                </div>
                
                <div class="kanban-card" data-task-id="10">
                    <div class="card-title">Configurar banco de dados</div>
                    <div class="card-meta">
                        <span class="badge badge-modern badge-success">Baixa</span>
                        <small class="text-muted">Isabela Lima</small>
                    </div>
                </div>
            </div>
            <button class="btn btn-link text-muted w-100 add-task-btn" data-status="done">
                <i class="bi bi-plus me-1"></i>Adicionar tarefa
            </button>
        </div>
    </div>

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
