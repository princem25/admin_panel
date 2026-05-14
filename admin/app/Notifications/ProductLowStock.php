<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class ProductLowStock extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;
    public $orderId;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product, $orderId = null)
    {
        $this->product = $product;
        $this->orderId = $orderId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['slack', 'mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->product->name)
            ->line('The following product is running low on stock:')
            ->line('Product: ' . $this->product->name)
            ->line('Current Stock: ' . $this->product->stock)
            ->action('Update Stock', url('/admin/products/' . $this->product->id . '/edit'))
            ->line('Please restock soon.');
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->from('Stock Bot', ':warning:')
            ->content("⚠️ Low Stock Alert")
            ->attachment(function ($attachment) {
                $attachment->title($this->product->name)
                    ->color('#ffa500') // Orange
                    ->fields([
                        'Product' => $this->product->name,
                        'Remaining Stock' => $this->product->stock,
                        'Order ID' => $this->orderId ?? 'N/A',
                    ])
                    ->fallback("Low Stock Alert: {$this->product->name} (Stock: {$this->product->stock})");
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->product->stock,
            'order_id' => $this->orderId,
            'message' => 'Product ' . $this->product->name . ' is low on stock',
        ];
    }
}
