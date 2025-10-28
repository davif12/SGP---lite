<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;
    protected $oldStatus;
    protected $newStatus;
    protected $changedBy;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task, string $oldStatus, string $newStatus, User $changedBy)
    {
        $this->task = $task;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->changedBy = $changedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $oldStatusLabel = $this->getStatusLabel($this->oldStatus);
        $newStatusLabel = $this->getStatusLabel($this->newStatus);

        return (new MailMessage)
            ->subject('Status da Task Alterado - ' . $this->task->title)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('O status de uma task foi alterado.')
            ->line('**Task:** ' . $this->task->title)
            ->line('**Projeto:** ' . $this->task->epic->project->name)
            ->line('**Status Anterior:** ' . $oldStatusLabel)
            ->line('**Novo Status:** ' . $newStatusLabel)
            ->line('**Alterado por:** ' . $this->changedBy->name)
            ->action('Ver Task', route('projects.epics.tasks.show', [
                $this->task->epic->project,
                $this->task->epic,
                $this->task
            ]))
            ->salutation('Equipe SGP Lite');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $oldStatusLabel = $this->getStatusLabel($this->oldStatus);
        $newStatusLabel = $this->getStatusLabel($this->newStatus);

        return [
            'type' => 'task_status_changed',
            'title' => 'Status da Task Alterado',
            'message' => $this->changedBy->name . ' alterou o status da task "' . $this->task->title . '" de ' . $oldStatusLabel . ' para ' . $newStatusLabel,
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->epic->project->id,
            'project_name' => $this->task->epic->project->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'old_status_label' => $oldStatusLabel,
            'new_status_label' => $newStatusLabel,
            'changed_by' => [
                'id' => $this->changedBy->id,
                'name' => $this->changedBy->name,
            ],
            'url' => route('projects.epics.tasks.show', [
                $this->task->epic->project,
                $this->task->epic,
                $this->task
            ]),
            'icon' => $this->getStatusIcon($this->newStatus),
            'color' => $this->getStatusColor($this->newStatus),
        ];
    }

    private function getStatusLabel(string $status): string
    {
        $labels = [
            'todo' => 'A Fazer',
            'in_progress' => 'Em Progresso',
            'review' => 'Em Revisão',
            'done' => 'Concluído'
        ];

        return $labels[$status] ?? $status;
    }

    private function getStatusIcon(string $status): string
    {
        $icons = [
            'todo' => 'bi-circle',
            'in_progress' => 'bi-arrow-right-circle',
            'review' => 'bi-eye',
            'done' => 'bi-check-circle'
        ];

        return $icons[$status] ?? 'bi-flag';
    }

    private function getStatusColor(string $status): string
    {
        $colors = [
            'todo' => 'secondary',
            'in_progress' => 'primary',
            'review' => 'warning',
            'done' => 'success'
        ];

        return $colors[$status] ?? 'secondary';
    }
}
