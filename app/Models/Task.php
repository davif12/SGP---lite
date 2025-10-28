<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'story_points',
        'position',
        'due_date',
        'epic_id',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'story_points' => 'integer',
        'position' => 'integer',
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
        return $query->orderBy('position')->orderBy('created_at');
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
