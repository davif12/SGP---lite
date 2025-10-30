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

                    <!-- Attachments Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted mb-0">Anexos</h6>
                            <div class="d-flex gap-2">
                                <span class="badge badge-modern badge-secondary" id="attachments-count">0</span>
                                <button class="btn btn-modern btn-outline-primary btn-sm" onclick="triggerFileUpload()">
                                    <i class="bi bi-paperclip me-1"></i>Anexar Arquivo
                                </button>
                            </div>
                        </div>
                        
                        <!-- File Upload (Hidden) -->
                        <input type="file" id="file-upload" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip" style="display: none;" onchange="uploadFiles(this.files)">
                        
                        <!-- Upload Progress -->
                        <div id="upload-progress" style="display: none;" class="mb-3">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted">Enviando arquivos...</small>
                        </div>
                        
                        <!-- Attachments List -->
                        <div id="attachments-list" class="row g-2">
                            <!-- Attachments will be loaded here -->
                        </div>
                    </div>

                    <!-- Time Tracking Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted mb-0">Time Tracking</h6>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge badge-modern badge-info" id="total-time">0h</span>
                                <button class="btn btn-modern btn-success btn-sm" id="start-timer-btn" onclick="startTimer()">
                                    <i class="bi bi-play-fill me-1"></i>Iniciar
                                </button>
                                <button class="btn btn-modern btn-danger btn-sm" id="stop-timer-btn" onclick="stopTimer()" style="display: none;">
                                    <i class="bi bi-stop-fill me-1"></i>Parar
                                </button>
                                <button class="btn btn-modern btn-outline-primary btn-sm" onclick="showAddTimeModal()">
                                    <i class="bi bi-plus me-1"></i>Adicionar Tempo
                                </button>
                            </div>
                        </div>

                        <!-- Running Timer Display -->
                        <div id="running-timer" class="alert alert-info d-flex justify-content-between align-items-center" style="display: none;">
                            <div>
                                <i class="bi bi-clock me-2"></i>
                                <strong>Timer ativo:</strong> <span id="timer-description">Trabalhando na task</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-primary fs-6" id="timer-display">00:00:00</span>
                                <button class="btn btn-sm btn-outline-danger" onclick="stopTimer()">
                                    <i class="bi bi-stop-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Time Progress Bar -->
                        <div class="mb-3" id="time-progress-container" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Progresso do tempo</small>
                                <small class="text-muted">
                                    <span id="logged-time">0h</span> / <span id="estimated-time">{{ $task->estimated_time_human }}</span>
                                </small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" id="time-progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Time Entries List -->
                        <div id="time-entries-list">
                            <!-- Time entries will be loaded here -->
                        </div>
                    </div>

                    <!-- Subtasks Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted mb-0">Subtasks</h6>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge badge-modern badge-info" id="subtasks-summary">0/0 concluídas</span>
                                <button class="btn btn-modern btn-outline-primary btn-sm" onclick="showAddSubtaskModal()">
                                    <i class="bi bi-plus me-1"></i>Nova Subtask
                                </button>
                            </div>
                        </div>

                        <!-- Subtasks Progress -->
                        <div class="mb-3" id="subtasks-progress-container" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Progresso das subtasks</small>
                                <small class="text-muted" id="subtasks-progress-text">0%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" id="subtasks-progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Subtasks List -->
                        <div id="subtasks-list">
                            <!-- Subtasks will be loaded here -->
                        </div>
                    </div>

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
                                        
                                        <!-- Comment Attachments Upload -->
                                        <div class="mt-2">
                                            <input type="file" id="comment-files" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip" style="display: none;" onchange="handleCommentFiles(this.files)">
                                            <div id="comment-attachments-preview" class="mb-2" style="display: none;">
                                                <div class="border rounded p-2 bg-light">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <small class="text-muted fw-medium">Arquivos anexados:</small>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCommentAttachments()">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    </div>
                                                    <div id="comment-files-list"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('comment-files').click()">
                                                    <i class="bi bi-paperclip me-1"></i>Anexar
                                                </button>
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Você pode editar por 15 minutos após postar
                                                </small>
                                            </div>
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

    <!-- Add Time Modal -->
    <div class="modal fade" id="addTimeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Tempo Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="add-time-form">
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control" name="description" required 
                                   placeholder="Descreva o que foi feito...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duração (horas)</label>
                            <input type="number" class="form-control" name="duration_hours" 
                                   step="0.25" min="0.25" max="24" required placeholder="1.5">
                            <div class="form-text">Use decimais para minutos (ex: 1.5 = 1h 30min)</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="addManualTime()">Adicionar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Time Modal -->
    <div class="modal fade" id="editTimeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Registro de Tempo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-time-form">
                        <input type="hidden" name="time_entry_id">
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control" name="description" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duração (horas)</label>
                            <input type="number" class="form-control" name="duration_hours" 
                                   step="0.25" min="0.25" max="24" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateTimeEntry()">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Subtask Modal -->
    <div class="modal fade" id="addSubtaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Subtask</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="add-subtask-form">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" name="title" required 
                                   placeholder="Digite o título da subtask...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="description" rows="3" 
                                      placeholder="Descreva a subtask (opcional)..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prioridade</label>
                            <select class="form-select" name="priority" required>
                                <option value="low">Baixa</option>
                                <option value="medium" selected>Média</option>
                                <option value="high">Alta</option>
                                <option value="critical">Crítica</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Atribuir para</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Não atribuído</option>
                                @foreach($task->epic->project->members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="createSubtask()">Criar Subtask</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Subtask Modal -->
    <div class="modal fade" id="editSubtaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Subtask</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-subtask-form">
                        <input type="hidden" name="subtask_id">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="todo">A Fazer</option>
                                <option value="in_progress">Em Progresso</option>
                                <option value="review">Em Revisão</option>
                                <option value="done">Concluído</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prioridade</label>
                            <select class="form-select" name="priority" required>
                                <option value="low">Baixa</option>
                                <option value="medium">Média</option>
                                <option value="high">Alta</option>
                                <option value="critical">Crítica</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Atribuir para</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Não atribuído</option>
                                @foreach($task->epic->project->members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateSubtask()">Salvar</button>
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
            if (!content && commentAttachments.length === 0) {
                showNotification('Digite um comentário ou anexe um arquivo', 'warning');
                return;
            }
            
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
                body: JSON.stringify({ content: content || 'Arquivo anexado' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const commentId = data.comment.id;
                    
                    // If there are attachments, upload them
                    if (commentAttachments.length > 0) {
                        uploadCommentAttachments(commentId, data.comment);
                    } else {
                        // No attachments, just add the comment
                        addCommentToList(data.comment);
                        document.getElementById('comment-content').value = '';
                        showNotification('Comentário adicionado com sucesso!', 'success');
                    }
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

// Comment attachments management
let commentAttachments = [];

function handleCommentFiles(files) {
    commentAttachments = Array.from(files);
    displayCommentAttachments();
}

function displayCommentAttachments() {
    const preview = document.getElementById('comment-attachments-preview');
    const filesList = document.getElementById('comment-files-list');
    
    if (commentAttachments.length === 0) {
        preview.style.display = 'none';
        return;
    }
    
    preview.style.display = 'block';
    filesList.innerHTML = commentAttachments.map((file, index) => `
        <div class="d-flex align-items-center justify-content-between py-1">
            <div class="d-flex align-items-center">
                <i class="bi bi-file-earmark me-2 text-primary"></i>
                <span class="small">${file.name}</span>
                <span class="text-muted small ms-2">(${formatFileSize(file.size)})</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCommentAttachment(${index})">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `).join('');
}

function removeCommentAttachment(index) {
    commentAttachments.splice(index, 1);
    displayCommentAttachments();
}

function clearCommentAttachments() {
    commentAttachments = [];
    document.getElementById('comment-files').value = '';
    displayCommentAttachments();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function uploadCommentAttachments(commentId, comment) {
    let uploadedCount = 0;
    const totalFiles = commentAttachments.length;
    
    commentAttachments.forEach(file => {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('attachable_type', 'App\\Models\\Comment');
        formData.append('attachable_id', commentId);
        
        fetch('/api/attachments/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            uploadedCount++;
            
            if (uploadedCount === totalFiles) {
                // All files uploaded, reload comments to show attachments
                location.reload(); // Simple reload to show attachments
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            uploadedCount++;
            
            if (uploadedCount === totalFiles) {
                addCommentToList(comment);
                showNotification('Comentário adicionado, mas alguns anexos falharam', 'warning');
            }
        });
    });
}

function addCommentToList(comment) {
    const commentsList = document.getElementById('comments-list');
    const commentsCount = document.getElementById('comments-count');
    const noComments = document.getElementById('no-comments');
    
    // Hide no comments message
    if (noComments) {
        noComments.style.display = 'none';
    }
    
    // Add new comment to the list
    const commentHtml = createCommentHtml(comment);
    commentsList.insertAdjacentHTML('beforeend', commentHtml);
    
    // Update comments count
    const currentCount = parseInt(commentsCount.textContent);
    commentsCount.textContent = currentCount + 1;
    
    // Clear form and attachments
    document.getElementById('comment-content').value = '';
    clearCommentAttachments();
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

// Attachments functionality
function triggerFileUpload() {
    document.getElementById('file-upload').click();
}

function uploadFiles(files) {
    if (files.length === 0) return;
    
    const progressContainer = document.getElementById('upload-progress');
    const progressBar = progressContainer.querySelector('.progress-bar');
    
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    
    let uploadedCount = 0;
    const totalFiles = files.length;
    
    Array.from(files).forEach(file => {
        uploadFile(file, () => {
            uploadedCount++;
            const progress = (uploadedCount / totalFiles) * 100;
            progressBar.style.width = progress + '%';
            
            if (uploadedCount === totalFiles) {
                setTimeout(() => {
                    progressContainer.style.display = 'none';
                    loadAttachments();
                }, 500);
            }
        });
    });
}

function uploadFile(file, callback) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('attachable_type', 'App\\Models\\Task');
    formData.append('attachable_id', {{ $task->id }});
    
    fetch('/api/attachments/upload', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`Arquivo "${data.attachment.original_name}" enviado com sucesso!`, 'success');
        } else {
            showNotification(`Erro ao enviar "${file.name}": ${data.message || 'Erro desconhecido'}`, 'error');
        }
        callback();
    })
    .catch(error => {
        console.error('Upload error:', error);
        showNotification(`Erro ao enviar "${file.name}"`, 'error');
        callback();
    });
}

function loadAttachments() {
    fetch(`/api/attachments?attachable_type=App\\Models\\Task&attachable_id={{ $task->id }}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayAttachments(data.attachments);
            updateAttachmentsCount(data.attachments.length);
        }
    })
    .catch(error => {
        console.error('Error loading attachments:', error);
    });
}

function displayAttachments(attachments) {
    const container = document.getElementById('attachments-list');
    
    if (attachments.length === 0) {
        container.innerHTML = '<div class="col-12"><p class="text-muted text-center py-3">Nenhum anexo encontrado</p></div>';
        return;
    }
    
    container.innerHTML = attachments.map(attachment => `
        <div class="col-md-6 col-lg-4" data-attachment-id="${attachment.id}">
            <div class="card attachment-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-2">
                            <i class="${attachment.icon} text-${attachment.color} fs-4"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="card-title mb-1 text-truncate" title="${attachment.original_name}">
                                ${attachment.original_name}
                            </h6>
                            <p class="card-text small text-muted mb-1">
                                ${attachment.size} • ${attachment.uploaded_by}
                            </p>
                            <p class="card-text small text-muted">
                                ${attachment.created_at}
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="${attachment.url}" target="_blank">
                                        <i class="bi bi-download me-2"></i>Download
                                    </a></li>
                                    ${attachment.is_image ? `
                                    <li><a class="dropdown-item" href="#" onclick="previewImage('${attachment.thumbnail_url}', '${attachment.original_name}')">
                                        <i class="bi bi-eye me-2"></i>Visualizar
                                    </a></li>
                                    ` : ''}
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteAttachment(${attachment.id})">
                                        <i class="bi bi-trash me-2"></i>Excluir
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    ${attachment.is_image ? `
                    <div class="mt-2">
                        <img src="${attachment.thumbnail_url}" class="img-fluid rounded attachment-thumbnail" 
                             alt="${attachment.original_name}" onclick="previewImage('${attachment.url}', '${attachment.original_name}')"
                             style="max-height: 100px; cursor: pointer;">
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

function updateAttachmentsCount(count) {
    document.getElementById('attachments-count').textContent = count;
}

function deleteAttachment(attachmentId) {
    if (!confirm('Tem certeza que deseja excluir este anexo?')) {
        return;
    }
    
    fetch(`/api/attachments/${attachmentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const attachmentItem = document.querySelector(`[data-attachment-id="${attachmentId}"]`);
            attachmentItem.remove();
            
            // Update count
            const currentCount = parseInt(document.getElementById('attachments-count').textContent);
            updateAttachmentsCount(currentCount - 1);
            
            showNotification('Anexo excluído com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao excluir anexo', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao excluir anexo', 'error');
    });
}

function previewImage(url, name) {
    // Create modal for image preview
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${name}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${url}" class="img-fluid" alt="${name}">
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
}

// Load attachments on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAttachments();
    loadTimeEntries();
    loadSubtasks();
    checkRunningTimer();
    
    // Set default date to today
    document.querySelector('#addTimeModal input[name="date"]').value = new Date().toISOString().split('T')[0];
});

// Time Tracking Variables
let currentTimerEntry = null;
let timerInterval = null;

// Time Tracking Functions
function startTimer() {
    const description = prompt('Descrição do que você vai trabalhar (opcional):') || 'Trabalhando na task';
    
    fetch(`/api/tasks/{{ $task->id }}/time-entries/start`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ description: description })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentTimerEntry = data.time_entry;
            showRunningTimer(data.time_entry);
            showNotification('Timer iniciado!', 'success');
        } else {
            showNotification(data.message || 'Erro ao iniciar timer', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao iniciar timer', 'error');
    });
}

function stopTimer() {
    if (!currentTimerEntry) {
        showNotification('Nenhum timer ativo', 'warning');
        return;
    }
    
    fetch(`/api/tasks/{{ $task->id }}/time-entries/${currentTimerEntry.id}/stop`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideRunningTimer();
            loadTimeEntries();
            showNotification('Timer parado!', 'success');
        } else {
            showNotification(data.message || 'Erro ao parar timer', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao parar timer', 'error');
    });
}

function showRunningTimer(timeEntry) {
    currentTimerEntry = timeEntry;
    
    document.getElementById('running-timer').style.display = 'flex';
    document.getElementById('timer-description').textContent = timeEntry.description;
    document.getElementById('start-timer-btn').style.display = 'none';
    document.getElementById('stop-timer-btn').style.display = 'inline-block';
    
    // Start timer display update
    const startTime = new Date(timeEntry.started_at.replace(' ', 'T'));
    timerInterval = setInterval(() => {
        const now = new Date();
        const elapsed = Math.floor((now - startTime) / 1000);
        document.getElementById('timer-display').textContent = formatDuration(elapsed);
    }, 1000);
}

function hideRunningTimer() {
    currentTimerEntry = null;
    
    document.getElementById('running-timer').style.display = 'none';
    document.getElementById('start-timer-btn').style.display = 'inline-block';
    document.getElementById('stop-timer-btn').style.display = 'none';
    
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
}

function checkRunningTimer() {
    fetch('/api/time-entries/running')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.running_timer) {
            const timer = data.running_timer;
            if (timer.task.id == {{ $task->id }}) {
                showRunningTimer({
                    id: timer.id,
                    description: timer.description,
                    started_at: timer.started_at
                });
            }
        }
    })
    .catch(error => console.error('Error checking running timer:', error));
}

function loadTimeEntries() {
    fetch(`/api/tasks/{{ $task->id }}/time-entries`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayTimeEntries(data.time_entries);
            updateTimeStats(data.total_time, data.total_seconds);
        }
    })
    .catch(error => {
        console.error('Error loading time entries:', error);
    });
}

function displayTimeEntries(entries) {
    const container = document.getElementById('time-entries-list');
    
    if (entries.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-3">Nenhum registro de tempo encontrado</p>';
        return;
    }
    
    container.innerHTML = entries.map(entry => `
        <div class="card mb-2 time-entry-card" data-entry-id="${entry.id}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${entry.description}</h6>
                        <div class="d-flex gap-3 text-muted small">
                            <span><i class="bi bi-person me-1"></i>${entry.user.name}</span>
                            <span><i class="bi bi-calendar me-1"></i>${entry.started_at}</span>
                            ${entry.is_running ? 
                                `<span class="text-primary"><i class="bi bi-clock me-1"></i>Em execução</span>` :
                                `<span><i class="bi bi-clock me-1"></i>${entry.duration}</span>`
                            }
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        ${entry.is_running ? 
                            `<span class="badge bg-primary">${entry.current_duration}</span>` :
                            `<span class="badge bg-secondary">${entry.duration}</span>`
                        }
                        ${entry.can_edit ? `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="editTimeEntry(${entry.id}, '${entry.description}', ${entry.duration_seconds / 3600})">
                                    <i class="bi bi-pencil me-2"></i>Editar
                                </a></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteTimeEntry(${entry.id})">
                                    <i class="bi bi-trash me-2"></i>Excluir
                                </a></li>
                            </ul>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function updateTimeStats(totalTime, totalSeconds) {
    document.getElementById('total-time').textContent = totalTime;
    
    const estimatedHours = {{ $task->estimated_hours ?? 0 }};
    if (estimatedHours > 0) {
        const loggedHours = totalSeconds / 3600;
        const progress = Math.min(100, (loggedHours / estimatedHours) * 100);
        
        document.getElementById('time-progress-container').style.display = 'block';
        document.getElementById('logged-time').textContent = totalTime;
        document.getElementById('time-progress-bar').style.width = progress + '%';
        
        if (progress > 100) {
            document.getElementById('time-progress-bar').classList.add('bg-warning');
        } else if (progress > 80) {
            document.getElementById('time-progress-bar').classList.add('bg-info');
        } else {
            document.getElementById('time-progress-bar').classList.add('bg-primary');
        }
    }
}

function showAddTimeModal() {
    const modal = new bootstrap.Modal(document.getElementById('addTimeModal'));
    modal.show();
}

function addManualTime() {
    const form = document.getElementById('add-time-form');
    const formData = new FormData(form);
    
    fetch(`/api/tasks/{{ $task->id }}/time-entries`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            description: formData.get('description'),
            duration_hours: parseFloat(formData.get('duration_hours')),
            date: formData.get('date')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addTimeModal')).hide();
            form.reset();
            loadTimeEntries();
            showNotification('Tempo adicionado com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao adicionar tempo', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao adicionar tempo', 'error');
    });
}

function editTimeEntry(id, description, durationHours) {
    const form = document.getElementById('edit-time-form');
    form.querySelector('input[name="time_entry_id"]').value = id;
    form.querySelector('input[name="description"]').value = description;
    form.querySelector('input[name="duration_hours"]').value = durationHours;
    
    const modal = new bootstrap.Modal(document.getElementById('editTimeModal'));
    modal.show();
}

function updateTimeEntry() {
    const form = document.getElementById('edit-time-form');
    const formData = new FormData(form);
    const entryId = formData.get('time_entry_id');
    
    fetch(`/api/tasks/{{ $task->id }}/time-entries/${entryId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            description: formData.get('description'),
            duration_hours: parseFloat(formData.get('duration_hours'))
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editTimeModal')).hide();
            loadTimeEntries();
            showNotification('Registro atualizado com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao atualizar registro', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao atualizar registro', 'error');
    });
}

function deleteTimeEntry(id) {
    if (!confirm('Tem certeza que deseja excluir este registro de tempo?')) {
        return;
    }
    
    fetch(`/api/tasks/{{ $task->id }}/time-entries/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTimeEntries();
            showNotification('Registro excluído com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao excluir registro', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao excluir registro', 'error');
    });
}

function formatDuration(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

// Subtasks functionality
function loadSubtasks() {
    fetch(`/api/tasks/{{ $task->id }}/subtasks`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displaySubtasks(data.subtasks);
            updateSubtasksProgress(data.progress, data.summary);
        }
    })
    .catch(error => {
        console.error('Error loading subtasks:', error);
    });
}

function displaySubtasks(subtasks) {
    const container = document.getElementById('subtasks-list');
    
    if (subtasks.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-3">Nenhuma subtask encontrada</p>';
        return;
    }
    
    container.innerHTML = subtasks.map(subtask => `
        <div class="card mb-2 subtask-card" data-subtask-id="${subtask.id}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h6 class="mb-0">${subtask.title}</h6>
                            <span class="badge bg-${getStatusColor(subtask.status)}">${subtask.status_label}</span>
                            <span class="badge bg-${getPriorityColor(subtask.priority)}">${subtask.priority_label}</span>
                        </div>
                        ${subtask.description ? `<p class="text-muted small mb-2">${subtask.description}</p>` : ''}
                        <div class="d-flex gap-3 text-muted small">
                            <span><i class="bi bi-person me-1"></i>${subtask.assigned_user ? subtask.assigned_user.name : 'Não atribuído'}</span>
                            <span><i class="bi bi-calendar me-1"></i>${subtask.created_at}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="${subtask.url}" class="btn btn-sm btn-outline-primary" title="Ver detalhes">
                            <i class="bi bi-eye"></i>
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="editSubtask(${subtask.id}, '${subtask.title}', '${subtask.description || ''}', '${subtask.status}', '${subtask.priority}', ${subtask.assigned_to || 'null'})">
                                    <i class="bi bi-pencil me-2"></i>Editar
                                </a></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteSubtask(${subtask.id})">
                                    <i class="bi bi-trash me-2"></i>Excluir
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function updateSubtasksProgress(progress, summary) {
    document.getElementById('subtasks-summary').textContent = summary;
    
    if (progress > 0) {
        document.getElementById('subtasks-progress-container').style.display = 'block';
        document.getElementById('subtasks-progress-text').textContent = progress + '%';
        document.getElementById('subtasks-progress-bar').style.width = progress + '%';
    } else {
        document.getElementById('subtasks-progress-container').style.display = 'none';
    }
}

function showAddSubtaskModal() {
    const modal = new bootstrap.Modal(document.getElementById('addSubtaskModal'));
    modal.show();
}

function createSubtask() {
    const form = document.getElementById('add-subtask-form');
    const formData = new FormData(form);
    
    fetch(`/api/tasks/{{ $task->id }}/subtasks`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            title: formData.get('title'),
            description: formData.get('description'),
            priority: formData.get('priority'),
            assigned_to: formData.get('assigned_to') || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addSubtaskModal')).hide();
            form.reset();
            loadSubtasks();
            showNotification('Subtask criada com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao criar subtask', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao criar subtask', 'error');
    });
}

function editSubtask(id, title, description, status, priority, assignedTo) {
    const form = document.getElementById('edit-subtask-form');
    form.querySelector('input[name="subtask_id"]').value = id;
    form.querySelector('input[name="title"]').value = title;
    form.querySelector('textarea[name="description"]').value = description;
    form.querySelector('select[name="status"]').value = status;
    form.querySelector('select[name="priority"]').value = priority;
    form.querySelector('select[name="assigned_to"]').value = assignedTo || '';
    
    const modal = new bootstrap.Modal(document.getElementById('editSubtaskModal'));
    modal.show();
}

function updateSubtask() {
    const form = document.getElementById('edit-subtask-form');
    const formData = new FormData(form);
    const subtaskId = formData.get('subtask_id');
    
    fetch(`/api/tasks/{{ $task->id }}/subtasks/${subtaskId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            title: formData.get('title'),
            description: formData.get('description'),
            status: formData.get('status'),
            priority: formData.get('priority'),
            assigned_to: formData.get('assigned_to') || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editSubtaskModal')).hide();
            loadSubtasks();
            showNotification('Subtask atualizada com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao atualizar subtask', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao atualizar subtask', 'error');
    });
}

function deleteSubtask(id) {
    if (!confirm('Tem certeza que deseja excluir esta subtask?')) {
        return;
    }
    
    fetch(`/api/tasks/{{ $task->id }}/subtasks/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadSubtasks();
            showNotification('Subtask excluída com sucesso!', 'success');
        } else {
            showNotification(data.message || 'Erro ao excluir subtask', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao excluir subtask', 'error');
    });
}

function getPriorityColor(priority) {
    const colors = {
        'low': 'success',
        'medium': 'warning',
        'high': 'danger',
        'critical': 'dark'
    };
    return colors[priority] || 'secondary';
}
</script>

<style>
.attachment-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e9ecef;
}

.attachment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.attachment-thumbnail {
    transition: transform 0.2s;
}

.attachment-thumbnail:hover {
    transform: scale(1.05);
}

#upload-progress .progress {
    height: 8px;
}

#file-upload {
    display: none;
}

/* Time Tracking Styles */
.time-entry-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-left: 4px solid #e9ecef;
}

.time-entry-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#running-timer {
    border-left: 4px solid #0d6efd;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { border-left-color: #0d6efd; }
    50% { border-left-color: #6610f2; }
    100% { border-left-color: #0d6efd; }
}

#timer-display {
    font-family: 'Courier New', monospace;
    font-size: 1.1rem;
    min-width: 80px;
    text-align: center;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.3s ease;
}

/* Subtasks Styles */
.subtask-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-left: 4px solid #e9ecef;
}

.subtask-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-left-color: #0d6efd;
}

.subtask-card .badge {
    font-size: 0.7rem;
}

/* Comment Attachments Styles */
.comment-attachments .attachment-item {
    transition: background-color 0.2s;
}

.comment-attachments .attachment-item:hover {
    background-color: #f8f9fa !important;
}

#comment-attachments-preview {
    border: 1px dashed #dee2e6;
    border-radius: 0.375rem;
}

#comment-files-list .btn {
    padding: 0.125rem 0.25rem;
}
</style>
