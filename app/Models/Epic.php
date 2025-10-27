<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'project_id',
    ];

    protected $casts = [
        'status' => 'string',
        'priority' => 'string',
    ];

    /**
     * Get the project that owns the epic.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get status badge class for Bootstrap
     */
    public function getStatusBadgeClass()
    {
        switch($this->status) {
            case 'backlog':
                return 'bg-secondary';
            case 'in_progress':
                return 'bg-primary';
            case 'done':
                return 'bg-success';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get priority badge class for Bootstrap
     */
    public function getPriorityBadgeClass()
    {
        switch($this->priority) {
            case 'low':
                return 'bg-info';
            case 'medium':
                return 'bg-warning';
            case 'high':
                return 'bg-danger';
            default:
                return 'bg-warning';
        }
    }

    /**
     * Get status label in Portuguese
     */
    public function getStatusLabel()
    {
        switch($this->status) {
            case 'backlog':
                return 'Backlog';
            case 'in_progress':
                return 'Em Progresso';
            case 'done':
                return 'Concluído';
            default:
                return 'Backlog';
        }
    }

    /**
     * Get priority label in Portuguese
     */
    public function getPriorityLabel()
    {
        switch($this->priority) {
            case 'low':
                return 'Baixa';
            case 'medium':
                return 'Média';
            case 'high':
                return 'Alta';
            default:
                return 'Média';
        }
    }
}
