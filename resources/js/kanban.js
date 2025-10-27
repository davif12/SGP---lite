import Sortable from 'sortablejs';

class KanbanBoard {
    constructor() {
        this.init();
    }

    init() {
        this.setupSortable();
        this.setupEventListeners();
    }

    setupSortable() {
        const columns = document.querySelectorAll('.kanban-cards');
        
        columns.forEach(column => {
            new Sortable(column, {
                group: 'kanban',
                animation: 200,
                ghostClass: 'kanban-ghost',
                chosenClass: 'kanban-chosen',
                dragClass: 'kanban-drag',
                onStart: (evt) => {
                    evt.item.classList.add('dragging');
                },
                onEnd: (evt) => {
                    evt.item.classList.remove('dragging');
                    this.handleCardMove(evt);
                }
            });
        });
    }

    setupEventListeners() {
        // Add task button listeners
        document.querySelectorAll('.add-task-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const status = e.target.dataset.status;
                this.showAddTaskModal(status);
            });
        });

        // Card click listeners for details
        document.addEventListener('click', (e) => {
            if (e.target.closest('.kanban-card')) {
                const card = e.target.closest('.kanban-card');
                const taskId = card.dataset.taskId;
                if (taskId) {
                    this.showTaskDetails(taskId);
                }
            }
        });
    }

    async handleCardMove(evt) {
        const taskId = evt.item.dataset.taskId;
        const newStatus = evt.to.dataset.status;
        const newPosition = evt.newIndex;

        if (!taskId || !newStatus) return;

        try {
            // Show loading state
            evt.item.style.opacity = '0.7';
            
            const response = await fetch(`/api/tasks/${taskId}/move`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus,
                    position: newPosition
                })
            });

            if (!response.ok) {
                throw new Error('Failed to update task');
            }

            const data = await response.json();
            
            // Update UI with success
            evt.item.style.opacity = '1';
            this.showNotification('Task moved successfully', 'success');
            
            // Update counters
            this.updateColumnCounters();
            
        } catch (error) {
            console.error('Error moving task:', error);
            
            // Revert the move on error
            if (evt.from !== evt.to) {
                evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
            }
            
            evt.item.style.opacity = '1';
            this.showNotification('Failed to move task', 'error');
        }
    }

    updateColumnCounters() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const cards = column.querySelectorAll('.kanban-card');
            const counter = column.querySelector('.task-count');
            if (counter) {
                counter.textContent = cards.length;
            }
        });
    }

    showAddTaskModal(status) {
        // Create or show modal for adding new task
        const modal = document.getElementById('addTaskModal');
        if (modal) {
            const statusInput = modal.querySelector('#task_status');
            if (statusInput) {
                statusInput.value = status;
            }
            
            // Show modal (Bootstrap 5)
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    }

    showTaskDetails(taskId) {
        // Navigate to task details or show modal
        window.location.href = `/tasks/${taskId}`;
    }

    showNotification(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        // Add to toast container
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(container);
        }

        container.appendChild(toast);

        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove after hide
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    // Method to add new card dynamically
    addCard(columnStatus, cardData) {
        const column = document.querySelector(`[data-status="${columnStatus}"] .kanban-cards`);
        if (!column) return;

        const card = document.createElement('div');
        card.className = 'kanban-card';
        card.dataset.taskId = cardData.id;
        card.innerHTML = `
            <div class="card-title">${cardData.title}</div>
            <div class="card-meta">
                <span class="badge badge-modern badge-${this.getPriorityClass(cardData.priority)}">
                    ${cardData.priority}
                </span>
                <small class="text-muted">${cardData.assignee || 'Unassigned'}</small>
            </div>
        `;

        column.appendChild(card);
        this.updateColumnCounters();
    }

    getPriorityClass(priority) {
        switch (priority) {
            case 'high': return 'danger';
            case 'medium': return 'warning';
            case 'low': return 'success';
            default: return 'secondary';
        }
    }

    // Method to refresh board data
    async refreshBoard() {
        try {
            const response = await fetch('/api/board/data');
            const data = await response.json();
            
            // Clear existing cards
            document.querySelectorAll('.kanban-cards').forEach(column => {
                column.innerHTML = '';
            });

            // Add cards to appropriate columns
            data.tasks.forEach(task => {
                this.addCard(task.status, task);
            });

        } catch (error) {
            console.error('Error refreshing board:', error);
            this.showNotification('Failed to refresh board', 'error');
        }
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.kanban-board')) {
        window.kanbanBoard = new KanbanBoard();
    }
});

// Export for manual initialization
export default KanbanBoard;
