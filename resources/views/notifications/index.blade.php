<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gradient">Notificações</h1>
                <p class="text-muted mb-0">Acompanhe todas as atividades dos seus projetos</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-modern btn-secondary btn-sm" onclick="markAllAsRead()">
                    <i class="bi bi-check2-all me-1"></i>Marcar todas como lidas
                </button>
                <button class="btn btn-modern btn-danger btn-sm" onclick="clearAllNotifications()">
                    <i class="bi bi-trash me-1"></i>Limpar todas
                </button>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-12">
            @if($notifications->count() > 0)
                <div class="card-modern">
                    <div class="card-body p-0">
                        @foreach($notifications as $notification)
                            <div class="notification-item {{ !$notification->read_at ? 'unread' : '' }}" 
                                 data-notification-id="{{ $notification->id }}">
                                <div class="d-flex p-3 border-bottom">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0 me-3">
                                        <div class="notification-icon bg-{{ $notification->data['color'] ?? 'primary' }}">
                                            <i class="{{ $notification->data['icon'] ?? 'bi-bell' }} text-white"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 fw-semibold">{{ $notification->data['title'] ?? 'Notificação' }}</h6>
                                            <div class="d-flex align-items-center gap-2">
                                                @if(!$notification->read_at)
                                                    <span class="badge bg-primary rounded-pill" style="width: 8px; height: 8px;"></span>
                                                @endif
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        
                                        <p class="text-muted mb-2">{{ $notification->data['message'] ?? '' }}</p>
                                        
                                        <!-- Metadata -->
                                        @if(isset($notification->data['project_name']))
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-folder me-1"></i>{{ $notification->data['project_name'] }}
                                                </small>
                                                @if(isset($notification->data['task_title']))
                                                    <small class="text-muted">
                                                        <i class="bi bi-check-square me-1"></i>{{ $notification->data['task_title'] }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <!-- Actions -->
                                        <div class="d-flex gap-2">
                                            @if(isset($notification->data['url']) && $notification->data['url'] !== '#')
                                                <a href="{{ $notification->data['url'] }}" 
                                                   class="btn btn-modern btn-primary btn-sm"
                                                   onclick="markAsRead('{{ $notification->id }}')">
                                                    <i class="bi bi-arrow-right me-1"></i>Ver Detalhes
                                                </a>
                                            @endif
                                            
                                            @if(!$notification->read_at)
                                                <button class="btn btn-modern btn-secondary btn-sm" 
                                                        onclick="markAsRead('{{ $notification->id }}')">
                                                    <i class="bi bi-check me-1"></i>Marcar como lida
                                                </button>
                                            @endif
                                            
                                            <button class="btn btn-modern btn-outline-danger btn-sm" 
                                                    onclick="deleteNotification('{{ $notification->id }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-bell display-1 text-muted"></i>
                    </div>
                    <h5 class="text-muted">Nenhuma notificação</h5>
                    <p class="text-muted">Quando houver atividades nos seus projetos, você será notificado aqui.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
function markAsRead(notificationId) {
    fetch(`/api/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (item) {
                item.classList.remove('unread');
                const badge = item.querySelector('.badge.bg-primary');
                if (badge) badge.remove();
            }
            updateNotificationBadge();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('/api/notifications/read-all', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                const badge = item.querySelector('.badge.bg-primary');
                if (badge) badge.remove();
            });
            updateNotificationBadge();
            showAlert('Todas as notificações foram marcadas como lidas!', 'success');
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteNotification(notificationId) {
    if (!confirm('Tem certeza que deseja excluir esta notificação?')) return;
    
    fetch(`/api/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (item) {
                item.remove();
            }
            updateNotificationBadge();
            showAlert('Notificação excluída!', 'success');
        }
    })
    .catch(error => console.error('Error:', error));
}

function clearAllNotifications() {
    if (!confirm('Tem certeza que deseja excluir TODAS as notificações? Esta ação não pode ser desfeita.')) return;
    
    fetch('/api/notifications', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) alert.remove();
    }, 5000);
}
</script>

<style>
.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-item {
    transition: all var(--transition-normal);
}

.notification-item.unread {
    background-color: var(--bs-blue-50);
    border-left: 4px solid var(--bs-blue);
}

.notification-item:hover {
    background-color: var(--bs-gray-50);
}

.notification-item.unread:hover {
    background-color: var(--bs-blue-100);
}

[data-theme="dark"] .notification-item {
    border-color: var(--dark-border);
}

[data-theme="dark"] .notification-item.unread {
    background-color: var(--dark-surface);
    border-left-color: var(--bs-blue);
}

[data-theme="dark"] .notification-item:hover {
    background-color: var(--dark-border);
}
</style>
