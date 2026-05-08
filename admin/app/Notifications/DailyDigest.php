<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class DailyDigest extends Notification
{
    use Queueable;

    public $metrics;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $metrics)
    {
        $this->metrics = $metrics;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->content('📊 *Daily Digest*')
            ->attachment(function ($attachment) {
                $attachment->title('Store Metrics for Yesterday')
                    ->fields([
                        'Total Orders' => (string) $this->metrics['total_orders'],
                        'Total Revenue' => 'Rs.' . number_format($this->metrics['total_revenue'], 2),
                        'New Customers' => (string) $this->metrics['new_customers'],
                        'Failed Jobs' => (string) $this->metrics['failed_jobs'],
                    ]);
            })
            ->attachment(function ($attachment) {
                $attachment->title('Low-Stock Products (' . $this->metrics['low_stock_products'] . ')')
                    ->content($this->metrics['low_stock_list']);
            })
            ->attachment(function ($attachment) {
                $attachment->title('New Customer Names')
                    ->content($this->metrics['new_customer_names']);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->metrics;
    }
}
