<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\Task;
use App\Models\User;
use App\Traits\BroadcastsNotifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable, BroadcastsNotifications;

    protected $task;
    protected $assignedBy;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task, User $assignedBy)
    {
        $this->task = $task;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nova Task Atribuída - ' . $this->task->title)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('Uma nova task foi atribuída a você.')
            ->line('**Task:** ' . $this->task->title)
            ->line('**Projeto:** ' . $this->task->epic->project->name)
            ->line('**Épico:** ' . $this->task->epic->name)
            ->line('**Atribuída por:** ' . $this->assignedBy->name)
            ->when($this->task->due_date, function ($mail) {
                return $mail->line('**Data de Vencimento:** ' . $this->task->due_date->format('d/m/Y'));
            })
            ->action('Ver Task', route('projects.epics.tasks.show', [
                $this->task->epic->project,
                $this->task->epic,
                $this->task
            ]))
            ->line('Acesse o sistema para mais detalhes!')
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
        return [
            'type' => 'task_assigned',
            'title' => 'Nova Task Atribuída',
            'message' => $this->assignedBy->name . ' atribuiu a task "' . $this->task->title . '" para você',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->epic->project->id,
            'project_name' => $this->task->epic->project->name,
            'epic_id' => $this->task->epic->id,
            'epic_name' => $this->task->epic->name,
            'assigned_by' => [
                'id' => $this->assignedBy->id,
                'name' => $this->assignedBy->name,
            ],
            'url' => route('projects.epics.tasks.show', [
                $this->task->epic->project,
                $this->task->epic,
                $this->task
            ]),
            'icon' => 'bi-person-check',
            'color' => 'primary',
        ];
    }

    /**
     * Handle notification sent event
     */
    public function sent($notifiable, $channel)
    {
        if ($channel === 'database') {
            $this->broadcastNotification($notifiable, $this);
        }
    }
}
