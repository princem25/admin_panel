<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWebhook')) {
            return;
        }

        $url = $notifiable->routeNotificationFor('webhook') ?? config('services.webhook.url');

        if (!$url) {
            return;
        }

        $payload = $notification->toWebhook($notifiable);

        rescue(function () use ($url, $payload) {
            Http::timeout(5)
                ->retry(3, 100)
                ->post($url, $payload)
                ->throw();
        }, function ($e) use ($url) {
            Log::error('Webhook notification failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        });
    }
}
