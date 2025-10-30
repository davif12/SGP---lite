<?php

namespace App\Traits;

use App\Events\NotificationSent;
use Illuminate\Notifications\Notification;

trait BroadcastsNotifications
{
    /**
     * Broadcast the notification after it's sent
     */
    public function broadcastNotification($notifiable, Notification $notification)
    {
        $data = $notification->toArray($notifiable);
        
        // Add common notification metadata
        $notificationData = array_merge($data, [
            'id' => $notification->id ?? uniqid(),
            'type' => get_class($notification),
            'created_at' => now()->toISOString(),
            'read_at' => null,
        ]);

        // Broadcast the notification
        broadcast(new NotificationSent($notifiable, $notificationData));
    }
}
