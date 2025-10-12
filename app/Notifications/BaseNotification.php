<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        // Default to mail + database for all notifications
        return ['mail', 'database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => static::class,
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}
