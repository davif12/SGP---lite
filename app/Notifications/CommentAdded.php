<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentAdded extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
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
        return (new MailMessage)
            ->subject('Novo Comentário - ' . $this->comment->task->title)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('Um novo comentário foi adicionado a uma task.')
            ->line('**Task:** ' . $this->comment->task->title)
            ->line('**Projeto:** ' . $this->comment->task->epic->project->name)
            ->line('**Comentário de:** ' . $this->comment->user->name)
            ->line('**Comentário:** ' . \Str::limit($this->comment->content, 100))
            ->action('Ver Task', route('projects.epics.tasks.show', [
                $this->comment->task->epic->project,
                $this->comment->task->epic,
                $this->comment->task
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
        return [
            'type' => 'comment_added',
            'title' => 'Novo Comentário',
            'message' => $this->comment->user->name . ' comentou na task "' . $this->comment->task->title . '"',
            'comment_id' => $this->comment->id,
            'comment_content' => \Str::limit($this->comment->content, 100),
            'task_id' => $this->comment->task->id,
            'task_title' => $this->comment->task->title,
            'project_id' => $this->comment->task->epic->project->id,
            'project_name' => $this->comment->task->epic->project->name,
            'commented_by' => [
                'id' => $this->comment->user->id,
                'name' => $this->comment->user->name,
            ],
            'url' => route('projects.epics.tasks.show', [
                $this->comment->task->epic->project,
                $this->comment->task->epic,
                $this->comment->task
            ]),
            'icon' => 'bi-chat-dots',
            'color' => 'info',
        ];
    }
}
