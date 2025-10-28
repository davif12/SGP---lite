<div class="kanban-card" data-task-id="{{ $task->id }}" onclick="showTaskDetails({{ $task->id }})">
    <!-- Task Header -->
    <div class="d-flex justify-content-between align-items-start mb-2">
        <span class="badge badge-modern badge-{{ $task->priority_color }} small">
            {{ $task->priority_label }}
        </span>
        <small class="text-muted">#{{ $task->id }}</small>
    </div>

    <!-- Task Title -->
    <h6 class="card-title mb-2">{{ $task->title }}</h6>

    <!-- Epic Info -->
    <div class="mb-2">
        <small class="text-muted">
            <i class="bi bi-journal-text me-1"></i>{{ $task->epic->name }}
        </small>
    </div>

    <!-- Task Description -->
    @if($task->description)
        <p class="text-muted small mb-2" style="font-size: 0.8rem;">
            {{ Str::limit($task->description, 60) }}
        </p>
    @endif

    <!-- Task Footer -->
    <div class="d-flex justify-content-between align-items-center">
        <!-- Assignee -->
        <div class="d-flex align-items-center">
            @if($task->assignedUser)
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-1" 
                     style="width: 20px; height: 20px;">
                    <span class="text-white" style="font-size: 0.7rem; font-weight: bold;">
                        {{ substr($task->assignedUser->name, 0, 1) }}
                    </span>
                </div>
                <small class="text-muted">{{ Str::limit($task->assignedUser->name, 10) }}</small>
            @else
                <small class="text-muted">
                    <i class="bi bi-person-dash"></i>
                </small>
            @endif
        </div>

        <!-- Story Points & Due Date -->
        <div class="d-flex align-items-center gap-2">
            @if($task->story_points)
                <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                    {{ $task->story_points }}pts
                </span>
            @endif
            
            @if($task->due_date)
                <small class="text-muted {{ $task->due_date->isPast() ? 'text-danger' : '' }}">
                    <i class="bi bi-calendar3"></i>
                    {{ $task->due_date->format('d/m') }}
                </small>
            @endif
        </div>
    </div>

    <!-- Progress indicator for tasks with story points -->
    @if($task->story_points && $task->status !== 'todo')
        <div class="mt-2">
            <div class="progress" style="height: 3px;">
                @php
                    $progress = match($task->status) {
                        'in_progress' => 33,
                        'review' => 66,
                        'done' => 100,
                        default => 0
                    };
                @endphp
                <div class="progress-bar bg-{{ $task->status_color }}" 
                     style="width: {{ $progress }}%"></div>
            </div>
        </div>
    @endif
</div>

<style>
.kanban-card .card-title {
    font-size: 0.9rem;
    line-height: 1.3;
    font-weight: 600;
}

.kanban-card:hover {
    cursor: pointer;
}

.kanban-card .badge {
    font-size: 0.65rem;
}
</style>
