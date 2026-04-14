<?php

namespace App\Console\Commands;

use App\Events\OrderDelivered;
use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderShipped;
use App\Models\Order;
use Illuminate\Console\Command;

class TestOrderLifecycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:test-lifecycle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Event-Driven Order Lifecycle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting Order Lifecycle Test...\n");

        // Create a dummy order object in memory (without saving to DB to avoid constraints)
        $order = new Order();
        $order->id = 999;
        $order->user_id = 1;
        $order->total_amount = 150.00;
        $order->status = 'pending';

        $this->info("--- Dispatching OrderPlaced ---");
        event(new OrderPlaced($order));
        
        $this->info("\n--- Dispatching OrderPaid ---");
        $order->status = 'paid';
        event(new OrderPaid($order));

        $this->info("\n--- Dispatching OrderShipped ---");
        $order->status = 'shipped';
        event(new OrderShipped($order));

        $this->info("\n--- Dispatching OrderDelivered ---");
        $order->status = 'delivered';
        event(new OrderDelivered($order));

        $this->info("\nOrder Lifecycle Test Completed Successfully.");
    }
}
