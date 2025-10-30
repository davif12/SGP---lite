<div class="comment-item mb-3" data-comment-id="{{ $comment->id }}">
    <div class="d-flex gap-2">
        <!-- User Avatar -->
        <div class="flex-shrink-0">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 32px; height: 32px;">
                <span class="text-white fw-bold small">
                    {{ substr($comment->user->name, 0, 1) }}
                </span>
            </div>
        </div>
        
        <!-- Comment Content -->
        <div class="flex-grow-1">
            <div class="bg-light rounded p-3">
                <!-- Comment Header -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong class="text-dark">{{ $comment->user->name }}</strong>
                        <small class="text-muted ms-2">{{ $comment->time_ago }}</small>
                    </div>
                    
                    <!-- Comment Actions -->
                    @if($comment->is_editable || $comment->is_deletable)
                        <div class="dropdown">
                            <button class="btn btn-link btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if($comment->is_editable)
                                    <li>
                                        <button class="dropdown-item" onclick="editComment({{ $comment->id }})">
                                            <i class="bi bi-pencil me-2"></i>Editar
                                        </button>
                                    </li>
                                @endif
                                @if($comment->is_deletable)
                                    <li>
                                        <button class="dropdown-item text-danger" onclick="deleteComment({{ $comment->id }})">
                                            <i class="bi bi-trash me-2"></i>Excluir
                                        </button>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
                
                <!-- Comment Text -->
                <div class="comment-content">
                    {!! nl2br(e($comment->content)) !!}
                </div>

                <!-- Comment Attachments -->
                @if($comment->attachments->count() > 0)
                    <div class="comment-attachments mt-2">
                        <div class="row g-2">
                            @foreach($comment->attachments as $attachment)
                                <div class="col-md-6">
                                    <div class="attachment-item d-flex align-items-center p-2 bg-white rounded border">
                                        <div class="flex-shrink-0 me-2">
                                            <i class="{{ $attachment->icon }} text-{{ $attachment->color }} fs-5"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-medium text-truncate" title="{{ $attachment->original_name }}">
                                                {{ $attachment->original_name }}
                                            </div>
                                            <small class="text-muted">{{ $attachment->human_size }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <a href="{{ $attachment->url }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            @if($attachment->is_image)
                                                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="previewImage('{{ $attachment->url }}', '{{ $attachment->original_name }}')" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Edit Form (Hidden by default) -->
                <form class="comment-edit-form d-none mt-2" data-comment-id="{{ $comment->id }}">
                    @csrf
                    @method('PUT')
                    <textarea class="form-control mb-2" name="content" rows="2" required>{{ $comment->content }}</textarea>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-modern btn-primary btn-sm">
                            <i class="bi bi-check me-1"></i>Salvar
                        </button>
                        <button type="button" class="btn btn-modern btn-secondary btn-sm" onclick="cancelEdit({{ $comment->id }})">
                            <i class="bi bi-x me-1"></i>Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.comment-item {
    transition: all var(--transition-normal);
}

.comment-item:hover {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 0.5rem;
    margin: -0.5rem;
    margin-bottom: 0.5rem;
}

.comment-content {
    word-wrap: break-word;
    white-space: pre-wrap;
}

[data-theme="dark"] .comment-item:hover {
    background: var(--dark-border);
}
</style>
