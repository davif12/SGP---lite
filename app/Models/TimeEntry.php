<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'description',
        'started_at',
        'ended_at',
        'duration', // in seconds
        'is_running',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration' => 'integer',
        'is_running' => 'boolean',
    ];

    /**
     * Get the task this time entry belongs to
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who logged this time
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human readable duration
     */
    public function getHumanDurationAttribute(): string
    {
        $seconds = $this->duration;
        
        if ($seconds < 60) {
            return $seconds . 's';
        }
        
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($minutes < 60) {
            return $minutes . 'm' . ($remainingSeconds > 0 ? ' ' . $remainingSeconds . 's' : '');
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return $hours . 'h' . ($remainingMinutes > 0 ? ' ' . $remainingMinutes . 'm' : '');
    }

    /**
     * Get duration in hours (decimal)
     */
    public function getDurationHoursAttribute(): float
    {
        return round($this->duration / 3600, 2);
    }

    /**
     * Get current running duration if entry is active
     */
    public function getCurrentDurationAttribute(): int
    {
        if (!$this->is_running || !$this->started_at) {
            return $this->duration ?? 0;
        }

        return $this->duration + now()->diffInSeconds($this->started_at);
    }

    /**
     * Get human readable current duration
     */
    public function getCurrentHumanDurationAttribute(): string
    {
        $seconds = $this->current_duration;
        
        if ($seconds < 60) {
            return $seconds . 's';
        }
        
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($minutes < 60) {
            return $minutes . 'm' . ($remainingSeconds > 0 ? ' ' . $remainingSeconds . 's' : '');
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return $hours . 'h' . ($remainingMinutes > 0 ? ' ' . $remainingMinutes . 'm' : '');
    }

    /**
     * Start time tracking
     */
    public function start(): self
    {
        $this->update([
            'started_at' => now(),
            'is_running' => true,
        ]);

        return $this;
    }

    /**
     * Stop time tracking
     */
    public function stop(): self
    {
        if (!$this->is_running || !$this->started_at) {
            return $this;
        }

        $sessionDuration = now()->diffInSeconds($this->started_at);
        
        $this->update([
            'ended_at' => now(),
            'duration' => ($this->duration ?? 0) + $sessionDuration,
            'is_running' => false,
        ]);

        return $this;
    }

    /**
     * Pause time tracking (same as stop but can be resumed)
     */
    public function pause(): self
    {
        return $this->stop();
    }

    /**
     * Resume time tracking
     */
    public function resume(): self
    {
        return $this->start();
    }

    /**
     * Scopes
     */
    public function scopeForTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRunning($query)
    {
        return $query->where('is_running', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_running', false);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('started_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('started_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('started_at', now()->month)
                    ->whereYear('started_at', now()->year);
    }

    /**
     * Get total duration for a collection of time entries
     */
    public static function getTotalDuration($entries): int
    {
        return $entries->sum(function ($entry) {
            return $entry->current_duration;
        });
    }

    /**
     * Get total duration in human format
     */
    public static function getTotalHumanDuration($entries): string
    {
        $totalSeconds = self::getTotalDuration($entries);
        
        if ($totalSeconds < 60) {
            return $totalSeconds . 's';
        }
        
        $minutes = floor($totalSeconds / 60);
        $remainingSeconds = $totalSeconds % 60;
        
        if ($minutes < 60) {
            return $minutes . 'm' . ($remainingSeconds > 0 ? ' ' . $remainingSeconds . 's' : '');
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return $hours . 'h' . ($remainingMinutes > 0 ? ' ' . $remainingMinutes . 'm' : '');
    }
}
