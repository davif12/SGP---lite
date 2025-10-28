<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'properties',
        'subject_type',
        'subject_id',
        'causer_id',
        'project_id',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the subject model (Task, Epic, Project, etc.)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who caused this activity
     */
    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Get the project this activity belongs to
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the icon for this activity type
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'created' => 'bi-plus-circle',
            'updated' => 'bi-pencil',
            'deleted' => 'bi-trash',
            'status_changed' => 'bi-arrow-right-circle',
            'assigned' => 'bi-person-check',
            'unassigned' => 'bi-person-x',
            'commented' => 'bi-chat-dots',
            'member_added' => 'bi-people-fill',
            'member_removed' => 'bi-person-dash',
        ];

        return $icons[$this->type] ?? 'bi-activity';
    }

    /**
     * Get the color for this activity type
     */
    public function getColorAttribute(): string
    {
        $colors = [
            'created' => 'success',
            'updated' => 'primary',
            'deleted' => 'danger',
            'status_changed' => 'info',
            'assigned' => 'warning',
            'unassigned' => 'secondary',
            'commented' => 'info',
            'member_added' => 'success',
            'member_removed' => 'danger',
        ];

        return $colors[$this->type] ?? 'secondary';
    }

    /**
     * Scope to get activities for a specific project
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope to get activities by a specific user
     */
    public function scopeByCauser($query, $userId)
    {
        return $query->where('causer_id', $userId);
    }

    /**
     * Scope to get activities of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get recent activities
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Create a new activity log entry
     */
    public static function log(string $type, string $description, Model $subject, ?User $causer = null, array $properties = []): self
    {
        $projectId = self::getProjectId($subject);

        return self::create([
            'type' => $type,
            'description' => $description,
            'properties' => $properties,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'causer_id' => $causer ? $causer->id : auth()->id(),
            'project_id' => $projectId,
        ]);
    }

    /**
     * Get project ID from subject model
     */
    private static function getProjectId(Model $subject): ?int
    {
        if ($subject instanceof Project) {
            return $subject->id;
        }

        if ($subject instanceof Epic) {
            return $subject->project_id;
        }

        if ($subject instanceof Task) {
            return $subject->epic->project_id;
        }

        if ($subject instanceof Comment) {
            return $subject->task->epic->project_id;
        }

        return null;
    }
}
