<?php

namespace App\Listeners\Admin;

use App\Events\Admin\ProductStockLow;
use App\Mail\LowStockAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class SendStockLowEmail implements ShouldQueue
{
    /**
     * Sends a low-stock alert email to the admin with throttling.
     */
    public function handle(ProductStockLow $event): void
    {
        $product = $event->product;
        $key = 'low_stock_alert_' . $product->id;

        // Throttling: only one alert per product per hour
        if (!Cache::has($key)) {
            try {
                $product->load('category'); // Eager load category for the email template

                Mail::to(config('mail.admin.primary'))
                    ->cc(config('mail.admin.warehouse'))
                    ->bcc(config('mail.admin.archive'))
                    ->send(new LowStockAlert(collect([$product])));

                Cache::put($key, true, 3600); // 1 hour (3600 seconds)

                Log::channel('products')->info('Low stock alert sent and throttled for 1 hour', [
                    'product_id' => $product->id,
                    'stock' => $product->stock,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send LowStockAlert', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            Log::channel('products')->info('Low stock alert suppressed due to throttling', [
                'product_id' => $product->id,
            ]);
        }
    }
}
