<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $status;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $status, $recipient = null)
    {
        $this->order = $order;
        $this->status = $status;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your Order #' . $this->order->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.status',
            with: [
                'order' => $this->order,
                'status' => ucfirst($this->status),
                'url' => (isset($this->recipient->role) && $this->recipient->role === 'admin')
                            ? url("/admin/orders/{$this->order->id}")
                            : url("/user/orders/{$this->order->id}"),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
