<?php

namespace App\Listeners\Admin;

use App\Events\Admin\ProductStockLow;
use App\Mail\StockLowAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendStockLowEmail implements ShouldQueue
{
    /**
     * Sends a low-stock alert email to the admin.
     */
    public function handle(ProductStockLow $event): void
    {
        $adminEmail = config('mail.admin_address', 'admin@example.com');

        Mail::to($adminEmail)->send(new StockLowAlert($event->product));

        Log::channel('products')->info('Listener stocklow handled: SendStockLowEmail', [
            'product_id' => $event->product->id,
            'stock' => $event->product->stock,
            'admin_email' => $adminEmail,
        ]);
    }
}
