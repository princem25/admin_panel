<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LowStockAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $products;

    /**
     * Create a new message instance.
     *
     * @param mixed $products Collection or array of low-stock products
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $count = count($this->products);
        $firstProduct = $this->products->first();
        $productName = $firstProduct ? $firstProduct->name : 'Unknown';

        $subject = $count === 1 
            ? "⚠️ Low Stock Alert: " . $productName 
            : "⚠️ Low Stock Alert: Multiple Products ({$count})";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.stock-low-alert',
            with: [
                'products' => $this->products,
            ],
        );
    }
}
