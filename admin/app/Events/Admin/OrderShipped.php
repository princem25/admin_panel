<?php

namespace App\Events\Admin;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderShipped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        Log::channel('orders')->info('Event dispatched: OrderShipped', [
            'order_id' => $order->id,
            'customer_id' => $order->user_id,
            'status' => $order->status,
            'total_amount' => $order->total,
        ]);
    }
}
