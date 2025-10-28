import Sortable from 'sortablejs';

// Initialize Kanban Board when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.kanban-board')) {
        initializeKanbanBoard();
    }
});

function initializeKanbanBoard() {
    const columns = ['todo', 'in_progress', 'review', 'done'];
    
    columns.forEach(status => {
        const column = document.querySelector(`[data-status="${status}"]`);
        if (column) {
            new Sortable(column, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'kanban-ghost',
                chosenClass: 'kanban-chosen',
                dragClass: 'kanban-drag',
                onEnd: function(evt) {
                    handleTaskMove(evt);
                }
            });
        }
    });
    
    updateColumnCounters();
}

function handleTaskMove(evt) {
    const taskId = evt.item.dataset.taskId;
    const newStatus = evt.to.dataset.status;
    const newPosition = evt.newIndex;
    
    fetch(`/api/tasks/${taskId}/move`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus,
            position: newPosition
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            updateColumnCounters();
        } else {
            showNotification('Erro ao mover task', 'error');
            revertTaskMove(evt);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao mover task', 'error');
        revertTaskMove(evt);
    });
}

function revertTaskMove(evt) {
    const item = evt.item;
    const originalParent = evt.from;
    const originalIndex = evt.oldIndex;
    
    item.remove();
    
    if (originalIndex >= originalParent.children.length) {
        originalParent.appendChild(item);
    } else {
        originalParent.insertBefore(item, originalParent.children[originalIndex]);
    }
}

function updateColumnCounters() {
    const columns = ['todo', 'in_progress', 'review', 'done'];
    
    columns.forEach(status => {
        const column = document.querySelector(`[data-status="${status}"]`);
        const counter = column?.parentElement?.querySelector('.task-count');
        
        if (column && counter) {
            counter.textContent = column.children.length;
        }
    });
}

function showNotification(message, type = 'info') {
    console.log(`${type.toUpperCase()}: ${message}`);
}

// Make functions globally available
window.initializeKanbanBoard = initializeKanbanBoard;
window.handleTaskMove = handleTaskMove;
window.updateColumnCounters = updateColumnCounters;
window.showNotification = showNotification;
