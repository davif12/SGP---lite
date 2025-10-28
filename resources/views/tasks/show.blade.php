<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.index') }}" class="text-decoration-none">Projetos</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">{{ $project->name }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('epics.index', $project) }}" class="text-decoration-none">Épicos</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.epics.show', [$project, $epic]) }}" class="text-decoration-none">{{ $epic->name }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.epics.tasks.index', [$project, $epic]) }}" class="text-decoration-none">Tasks</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $task->title }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0 mt-2 text-gradient">{{ $task->title }}</h1>
                <p class="text-muted mb-0">Task #{{ $task->id }}</p>
            </div>
            <div class="d-flex gap-2">
                @can('update', $project)
                    <a href="{{ route('projects.epics.tasks.edit', [$project, $epic, $task]) }}" 
                       class="btn btn-modern btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                @endcan
                <a href="{{ route('projects.epics.tasks.index', [$project, $epic]) }}" 
                   class="btn btn-modern btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Task Details -->
            <div class="card-modern">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-card-text me-2"></i>Detalhes da Task
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Title -->
                    <div class="mb-4">
                        <h4 class="mb-2">{{ $task->title }}</h4>
                        <div class="d-flex gap-2 mb-3">
                            <span class="badge badge-modern badge-{{ $task->status_color }}">
                                {{ $task->status_label }}
                            </span>
                            <span class="badge badge-modern badge-{{ $task->priority_color }}">
                                {{ $task->priority_label }}
                            </span>
                            @if($task->story_points)
                                <span class="badge badge-modern badge-info">
                                    {{ $task->story_points }} Story Points
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    @if($task->description)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Descrição</h6>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Comments Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted mb-0">Comentários</h6>
                            <span class="badge badge-modern badge-secondary" id="comments-count">
                                {{ $task->comments->count() }}
                            </span>
                        </div>
                        
                        <!-- Comments List -->
                        <div id="comments-list" class="mb-3">
                            @forelse($task->comments as $comment)
                                @include('tasks.partials.comment', ['comment' => $comment])
                            @empty
                                <div class="text-center py-3 text-muted" id="no-comments">
                                    <i class="bi bi-chat-dots display-4"></i>
                                    <p class="mb-0">Nenhum comentário ainda</p>
                                    <small>Seja o primeiro a comentar!</small>
                                </div>
                            @endforelse
                        </div>
                        
                        <!-- Add Comment Form -->
                        @can('view', $project)
                            <form id="comment-form" class="mt-3">
                                @csrf
                                <div class="d-flex gap-2">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 32px; height: 32px;">
                                            <span class="text-white fw-bold small">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <textarea class="form-control" 
                                                  id="comment-content" 
                                                  name="content" 
                                                  rows="2" 
                                                  placeholder="Escreva um comentário..."
                                                  required></textarea>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Você pode editar por 15 minutos após postar
                                            </small>
                                            <button type="submit" class="btn btn-modern btn-primary btn-sm">
                                                <i class="bi bi-send me-1"></i>Comentar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endcan
                    </div>

                    <!-- Timeline -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Timeline</h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between">
                                        <strong>Task criada</strong>
                                        <small class="text-muted">{{ $task->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <p class="text-muted mb-0">Task foi criada no épico {{ $epic->name }}</p>
                                </div>
                            </div>
                            
                            @if($task->updated_at != $task->created_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between">
                                            <strong>Última atualização</strong>
                                            <small class="text-muted">{{ $task->updated_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <p class="text-muted mb-0">Task foi modificada</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Task Info -->
            <div class="card-modern mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informações
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">Status</label>
                        <div>
                            <span class="badge badge-modern badge-{{ $task->status_color }}">
                                {{ $task->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Priority -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">Prioridade</label>
                        <div>
                            <span class="badge badge-modern badge-{{ $task->priority_color }}">
                                {{ $task->priority_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Assignee -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">Responsável</label>
                        <div class="d-flex align-items-center">
                            @if($task->assignedUser)
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 32px; height: 32px;">
                                    <span class="text-white fw-bold">
                                        {{ substr($task->assignedUser->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $task->assignedUser->name }}</div>
                                    <small class="text-muted">{{ $task->assignedUser->email }}</small>
                                </div>
                            @else
                                <div class="text-muted">
                                    <i class="bi bi-person-dash me-1"></i>Não atribuído
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Story Points -->
                    @if($task->story_points)
                        <div class="mb-3">
                            <label class="form-label small text-muted">Story Points</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-speedometer2 me-2 text-primary"></i>
                                <span class="fw-semibold">{{ $task->story_points }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Due Date -->
                    @if($task->due_date)
                        <div class="mb-3">
                            <label class="form-label small text-muted">Data de Vencimento</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 me-2 {{ $task->due_date->isPast() ? 'text-danger' : 'text-primary' }}"></i>
                                <span class="fw-semibold {{ $task->due_date->isPast() ? 'text-danger' : '' }}">
                                    {{ $task->due_date->format('d/m/Y') }}
                                    @if($task->due_date->isPast())
                                        <small class="text-danger">(Atrasado)</small>
                                    @elseif($task->due_date->isToday())
                                        <small class="text-warning">(Hoje)</small>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Dates -->
                    <div class="mb-3">
                        <label class="form-label small text-muted">Criado em</label>
                        <div class="small">{{ $task->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    @if($task->updated_at != $task->created_at)
                        <div class="mb-3">
                            <label class="form-label small text-muted">Atualizado em</label>
                            <div class="small">{{ $task->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Epic Info -->
            <div class="card-modern mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-journal-text me-2"></i>Épico
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('projects.epics.show', [$project, $epic]) }}" 
                                   class="text-decoration-none">
                                    {{ $epic->name }}
                                </a>
                            </h6>
                            <div class="d-flex gap-2">
                                <span class="badge badge-modern badge-secondary">{{ $epic->status }}</span>
                                <span class="badge badge-modern badge-warning">{{ $epic->priority }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Info -->
            <div class="card-modern">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-folder me-2"></i>Projeto
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="mb-1">
                        <a href="{{ route('projects.show', $project) }}" 
                           class="text-decoration-none">
                            {{ $project->name }}
                        </a>
                    </h6>
                    <small class="text-muted">Dono: {{ $project->owner->name }}</small>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--gray-300);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid var(--white);
}

.timeline-content {
    background: var(--gray-50);
    padding: 1rem;
    border-radius: var(--radius-md);
}

[data-theme="dark"] .timeline-content {
    background: var(--dark-border);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('comment-form');
    const commentsList = document.getElementById('comments-list');
    const commentsCount = document.getElementById('comments-count');
    const noComments = document.getElementById('no-comments');
    
    // Handle comment form submission
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const content = document.getElementById('comment-content').value.trim();
            if (!content) return;
            
            const submitBtn = commentForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Enviando...';
            submitBtn.disabled = true;
            
            fetch(`/api/tasks/{{ $task->id }}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ content: content })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide no comments message
                    if (noComments) {
                        noComments.style.display = 'none';
                    }
                    
                    // Add new comment to the list
                    const commentHtml = createCommentHtml(data.comment);
                    commentsList.insertAdjacentHTML('beforeend', commentHtml);
                    
                    // Update comments count
                    const currentCount = parseInt(commentsCount.textContent);
                    commentsCount.textContent = currentCount + 1;
                    
                    // Clear form
                    document.getElementById('comment-content').value = '';
                    
                    showNotification('Comentário adicionado com sucesso!', 'success');
                } else {
                    showNotification('Erro ao adicionar comentário', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Erro ao adicionar comentário', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

function createCommentHtml(comment) {
    return `
        <div class="comment-item mb-3" data-comment-id="${comment.id}">
            <div class="d-flex gap-2">
                <div class="flex-shrink-0">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 32px; height: 32px;">
                        <span class="text-white fw-bold small">${comment.user.avatar}</span>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="bg-light rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong class="text-dark">${comment.user.name}</strong>
                                <small class="text-muted ms-2">${comment.time_ago}</small>
                            </div>
                            ${comment.is_editable || comment.is_deletable ? `
                                <div class="dropdown">
                                    <button class="btn btn-link btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        ${comment.is_editable ? `
                                            <li>
                                                <button class="dropdown-item" onclick="editComment(${comment.id})">
                                                    <i class="bi bi-pencil me-2"></i>Editar
                                                </button>
                                            </li>
                                        ` : ''}
                                        ${comment.is_deletable ? `
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteComment(${comment.id})">
                                                    <i class="bi bi-trash me-2"></i>Excluir
                                                </button>
                                            </li>
                                        ` : ''}
                                    </ul>
                                </div>
                            ` : ''}
                        </div>
                        <div class="comment-content">${comment.content.replace(/\n/g, '<br>')}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function editComment(commentId) {
    const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
    const content = commentItem.querySelector('.comment-content');
    const editForm = commentItem.querySelector('.comment-edit-form');
    
    content.style.display = 'none';
    editForm.classList.remove('d-none');
}

function cancelEdit(commentId) {
    const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
    const content = commentItem.querySelector('.comment-content');
    const editForm = commentItem.querySelector('.comment-edit-form');
    
    content.style.display = 'block';
    editForm.classList.add('d-none');
}

function deleteComment(commentId) {
    if (!confirm('Tem certeza que deseja excluir este comentário?')) {
        return;
    }
    
    fetch(`/api/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
            commentItem.remove();
            
            // Update comments count
            const commentsCount = document.getElementById('comments-count');
            const currentCount = parseInt(commentsCount.textContent);
            commentsCount.textContent = currentCount - 1;
            
            // Show no comments message if needed
            const remainingComments = document.querySelectorAll('.comment-item');
            if (remainingComments.length === 0) {
                const noComments = document.getElementById('no-comments');
                if (noComments) {
                    noComments.style.display = 'block';
                }
            }
            
            showNotification(data.message, 'success');
        } else {
            showNotification('Erro ao excluir comentário', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao excluir comentário', 'error');
    });
}

function showNotification(message, type = 'info') {
    console.log(`${type.toUpperCase()}: ${message}`);
    // You can implement toast notifications here
}
</script>
