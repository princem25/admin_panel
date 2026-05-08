<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\SlackRateLimitException;
use Exception;

class FaultTolerantSlackChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        $webhookUrl = $notifiable->routeNotificationFor('slack', $notification);

        if (!$webhookUrl) {
            Log::channel('slack')->warning('Slack webhook URL not configured for notifiable', [
                'notifiable' => get_class($notifiable),
                'notification' => get_class($notification),
            ]);
            return;
        }

        // Check if toSlack method exists
        if (!method_exists($notification, 'toSlack')) {
            Log::channel('slack')->error('Notification missing toSlack method', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        $message = $notification->toSlack($notifiable);

        if ($notification instanceof ShouldQueue) {
            // Queued notification: let exceptions bubble up for retries
            return $this->sendWithRetries($webhookUrl, $message, $notification);
        } else {
            // Sync notification: wrap in rescue to protect request lifecycle
            return rescue(function () use ($webhookUrl, $message, $notification) {
                return $this->sendWithRetries($webhookUrl, $message, $notification);
            }, function ($e) use ($notification) {
                Log::channel('slack')->error('Slack notification failed (Sync)', [
                    'notification' => get_class($notification),
                    'exception' => $e->getMessage(),
                ]);
                return null;
            });
        }
    }

    /**
     * Send HTTP request to Slack.
     */
    protected function sendWithRetries($webhookUrl, $message, $notification)
    {
        $payload = is_string($message) ? ['text' => $message] : $message->toArray();

        $response = Http::post($webhookUrl, $payload);

        if ($response->status() === 429) {
            $retryAfter = $response->header('Retry-After') ?? 60;
            
            Log::channel('slack')->warning('Slack rate limit hit (429)', [
                'notification' => get_class($notification),
                'retry_after' => $retryAfter,
            ]);

            throw new SlackRateLimitException($retryAfter);
        }

        if ($response->failed()) {
            Log::channel('slack')->error('Slack notification failed', [
                'notification' => get_class($notification),
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new Exception('Slack send failed with status ' . $response->status());
        }

        return $response;
    }
}
