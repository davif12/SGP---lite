<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectMemberAdded extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    protected $addedBy;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Project $project, User $addedBy)
    {
        $this->project = $project;
        $this->addedBy = $addedBy;
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
            ->subject('Adicionado ao Projeto - ' . $this->project->name)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('Você foi adicionado como membro de um novo projeto!')
            ->line('**Projeto:** ' . $this->project->name)
            ->line('**Descrição:** ' . ($this->project->description ?: 'Sem descrição'))
            ->line('**Adicionado por:** ' . $this->addedBy->name)
            ->action('Ver Projeto', route('projects.show', $this->project))
            ->line('Agora você pode colaborar neste projeto!')
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
            'type' => 'project_member_added',
            'title' => 'Adicionado ao Projeto',
            'message' => $this->addedBy->name . ' adicionou você ao projeto "' . $this->project->name . '"',
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'project_description' => $this->project->description,
            'added_by' => [
                'id' => $this->addedBy->id,
                'name' => $this->addedBy->name,
            ],
            'url' => route('projects.show', $this->project),
            'icon' => 'bi-people-fill',
            'color' => 'success',
        ];
    }
}
