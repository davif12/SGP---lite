<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'story_points',
        'estimated_hours',
        'logged_hours',
        'position',
        'due_date',
        'epic_id',
        'parent_id',
        'is_subtask',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'story_points' => 'integer',
        'estimated_hours' => 'integer',
        'logged_hours' => 'integer',
        'position' => 'integer',
        'is_subtask' => 'boolean',
    ];

    // Status constants
    const STATUS_TODO = 'todo';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_REVIEW = 'review';
    const STATUS_DONE = 'done';

    const STATUSES = [
        self::STATUS_TODO => 'A Fazer',
        self::STATUS_IN_PROGRESS => 'Em Progresso',
        self::STATUS_REVIEW => 'Em Revisão',
        self::STATUS_DONE => 'Concluído',
    ];

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

    const PRIORITIES = [
        self::PRIORITY_LOW => 'Baixa',
        self::PRIORITY_MEDIUM => 'Média',
        self::PRIORITY_HIGH => 'Alta',
        self::PRIORITY_CRITICAL => 'Crítica',
    ];

    // Relationships
    public function epic(): BelongsTo
    {
        return $this->belongsTo(Epic::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->oldest();
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')->orderBy('created_at', 'desc');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class)->orderBy('started_at', 'desc');
    }

    public function runningTimeEntry(): HasMany
    {
        return $this->hasMany(TimeEntry::class)->where('is_running', true);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id')->orderBy('position');
    }

    public function completedSubtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id')->where('status', self::STATUS_DONE);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function getPriorityColorAttribute(): string
    {
        switch($this->priority) {
            case self::PRIORITY_LOW:
                return 'success';
            case self::PRIORITY_MEDIUM:
                return 'warning';
            case self::PRIORITY_HIGH:
                return 'danger';
            case self::PRIORITY_CRITICAL:
                return 'dark';
            default:
                return 'secondary';
        }
    }

    public function getStatusColorAttribute(): string
    {
        switch($this->status) {
            case self::STATUS_TODO:
                return 'secondary';
            case self::STATUS_IN_PROGRESS:
                return 'primary';
            case self::STATUS_REVIEW:
                return 'warning';
            case self::STATUS_DONE:
                return 'success';
            default:
                return 'secondary';
        }
    }

    /**
     * Get total logged time in seconds
     */
    public function getTotalLoggedTimeAttribute(): int
    {
        return $this->timeEntries->sum('current_duration');
    }

    /**
     * Get total logged time in human format
     */
    public function getTotalLoggedTimeHumanAttribute(): string
    {
        return TimeEntry::getTotalHumanDuration($this->timeEntries);
    }

    /**
     * Get estimated time in human format
     */
    public function getEstimatedTimeHumanAttribute(): string
    {
        if (!$this->estimated_hours) {
            return 'Não estimado';
        }

        return $this->estimated_hours . 'h';
    }

    /**
     * Get time progress percentage
     */
    public function getTimeProgressAttribute(): int
    {
        if (!$this->estimated_hours) {
            return 0;
        }

        $loggedHours = $this->total_logged_time / 3600;
        return min(100, round(($loggedHours / $this->estimated_hours) * 100));
    }

    /**
     * Check if task has running time entry
     */
    public function getIsTimerRunningAttribute(): bool
    {
        return $this->runningTimeEntry()->exists();
    }

    /**
     * Get current running time entry
     */
    public function getCurrentTimeEntryAttribute(): ?TimeEntry
    {
        return $this->runningTimeEntry()->first();
    }

    /**
     * Get subtasks progress percentage
     */
    public function getSubtasksProgressAttribute(): int
    {
        $totalSubtasks = $this->subtasks()->count();
        
        if ($totalSubtasks === 0) {
            return 0;
        }

        $completedSubtasks = $this->completedSubtasks()->count();
        return round(($completedSubtasks / $totalSubtasks) * 100);
    }

    /**
     * Get subtasks summary
     */
    public function getSubtasksSummaryAttribute(): string
    {
        $total = $this->subtasks()->count();
        $completed = $this->completedSubtasks()->count();
        
        if ($total === 0) {
            return 'Nenhuma subtask';
        }

        return "{$completed}/{$total} concluídas";
    }

    /**
     * Check if task has subtasks
     */
    public function getHasSubtasksAttribute(): bool
    {
        return $this->subtasks()->exists();
    }

    /**
     * Get full task path (for subtasks)
     */
    public function getFullPathAttribute(): string
    {
        if (!$this->is_subtask || !$this->parent) {
            return $this->title;
        }

        return $this->parent->title . ' > ' . $this->title;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeInEpic($query, $epicId)
    {
        return $query->where('epic_id', $epicId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    public function scopeMainTasks($query)
    {
        return $query->where('is_subtask', false);
    }

    public function scopeSubtasks($query)
    {
        return $query->where('is_subtask', true);
    }

    public function scopeWithSubtasks($query)
    {
        return $query->with(['subtasks' => function ($query) {
            $query->orderBy('position');
        }]);
    }

    // Helper methods
    public function isAssignedTo(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }

    public function canBeMovedTo(string $status): bool
    {
        $allowedTransitions = [
            self::STATUS_TODO => [self::STATUS_IN_PROGRESS],
            self::STATUS_IN_PROGRESS => [self::STATUS_TODO, self::STATUS_REVIEW],
            self::STATUS_REVIEW => [self::STATUS_IN_PROGRESS, self::STATUS_DONE],
            self::STATUS_DONE => [self::STATUS_REVIEW],
        ];

        return in_array($status, $allowedTransitions[$this->status] ?? []);
    }

    public function moveToStatus(string $status, int $position = null): bool
    {
        if (!$this->canBeMovedTo($status)) {
            return false;
        }

        $this->status = $status;
        
        if ($position !== null) {
            $this->position = $position;
        }

        return $this->save();
    }
}
