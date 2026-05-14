<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\WebhookChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Log;

class NewOrderReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast', 'slack', WebhookChannel::class];
    }

    /**
     * Route channels to different queues.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
            'broadcast' => 'realtime',
            'database' => 'default',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        app()->setLocale($notifiable->preferred_locale ?? config('app.locale'));

        return (new MailMessage)
            ->subject(__('notifications.new_order_received_subject', ['order_id' => $this->order->id]))
            ->line(__('notifications.new_order_received_message'))
            ->line(__('Order ID: ') . $this->order->id)
            ->line(__('Total: ') . number_format($this->order->total_amount, 2))
            ->action(__('View Order'), url('/admin/orders/' . $this->order->id))
            ->line(__('Thank you for using our application!'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        app()->setLocale($notifiable->preferred_locale ?? config('app.locale'));

        return [
            'order_id' => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'message' => __('notifications.new_order_received_message'),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'message' => __('notifications.new_order_received_message'),
        ]);
    }

    /**
     * Get the webhook representation of the notification.
     */
    public function toWebhook($notifiable): array
    {
        return [
            'event' => 'order.received',
            'order_id' => $this->order->id,
            'total' => $this->order->total_amount,
            'customer' => $this->order->full_name,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->success()
            ->content(__('notifications.new_order_received_message'))
            ->attachment(function ($attachment) {
                $attachment->title(__('Order ID: ') . $this->order->id, url('/admin/orders/' . $this->order->id))
                           ->fields([
                                __('Total') => '₹' . number_format($this->order->total_amount, 2),
                                __('Customer') => $this->order->full_name,
                                __('Status') => strtoupper($this->order->status),
                           ]);
            });
    }

    /** 
     * Handle notification failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('NewOrderReceived notification failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
