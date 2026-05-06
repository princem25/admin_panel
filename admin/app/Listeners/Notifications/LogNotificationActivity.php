<?php

namespace App\Listeners\Notifications;

use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Log;

class LogNotificationActivity
{
    /**
     * Handle the NotificationSent event.
     */
    public function handleSent(NotificationSent $event): void
    {
        Log::info('Notification sent successfully', [
            'notifiable_id' => $event->notifiable->id ?? 'unknown',
            'channel' => $event->channel,
            'notification' => class_basename($event->notification),
        ]);
    }

    /**
     * Handle the NotificationFailed event.
     */
    public function handleFailed(NotificationFailed $event): void
    {
        Log::error('Notification delivery failed', [
            'notifiable_id' => $event->notifiable->id ?? 'unknown',
            'channel' => $event->channel,
            'notification' => class_basename($event->notification),
            'error' => $event->data['message'] ?? 'Unknown error',
        ]);
    }
}
