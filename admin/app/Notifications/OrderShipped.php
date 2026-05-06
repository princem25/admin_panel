<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderShipped extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable->role === 'admin' ? ['mail', 'database'] : ['mail'];
    }

    /**
     * Route channels to different queues.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
            'database' => 'default',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        app()->setLocale($notifiable->preferred_locale ?? config('app.locale'));

        return (new MailMessage)
            ->subject(__('notifications.order_shipped_subject'))
            ->greeting(__('Hello ') . $notifiable->name)
            ->line(__('notifications.order_shipped_message'))
            ->line(__('Tracking Number: ') . ($this->order->tracking_number ?? __('In Progress')))
            ->line(__('You can check the real-time status and delivery progress on your order details page.'))
            ->action(__('View Your Order'), url('/orders/' . $this->order->id))
            ->line(__('Thank you for shopping with us!'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        app()->setLocale($notifiable->preferred_locale ?? config('app.locale'));

        return [
            'order_id'        => $this->order->id,
            'tracking_number' => $this->order->tracking_number,
            'message'         => __('notifications.order_shipped_message'),
            'url'             => "/orders/{$this->order->id}",
            'icon'            => 'truck',
        ];
    }

    /**
     * Handle notification failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('OrderShipped notification failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
