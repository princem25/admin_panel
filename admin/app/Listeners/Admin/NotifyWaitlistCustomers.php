<?php

namespace App\Listeners\Admin;

use App\Events\Admin\ProductRestocked;
use App\Mail\ProductRestockedMail;
use App\Models\ProductWaitlist;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyWaitlistCustomers implements ShouldQueue
{
    /**
     * Notifies all waitlisted users that the product is back in stock,
     * re-enables the product listing, and clears the waitlist.
     */
    public function handle(ProductRestocked $event): void
    {
        $product = $event->product;

        $waitlistEntries = ProductWaitlist::where('product_id', $product->id)
            ->with('user')
            ->get();

        if ($waitlistEntries->isEmpty()) {
            Log::channel('products')->info('Listener handled: NotifyWaitlistCustomers (no waitlist entries)', [
                'product_id' => $product->id,
            ]);
            return;
        }

        foreach ($waitlistEntries as $entry) {
            if ($entry->user && $entry->user->email) {
                sleep(5); // Sleep 5s to avoid Mailtrap limits
                retry(3, function () use ($entry, $product) {
                    Mail::to($entry->user->email)->send(new ProductRestockedMail($product));
                }, 100, function (\Exception $e) {
                    Log::channel('products')->warning('Email retry failed, retrying...', ['error' => $e->getMessage()]);
                    return true;
                });
            }
        }

        ProductWaitlist::where('product_id', $product->id)->delete();

        Log::channel('products')->info('Listener handled: NotifyWaitlistCustomers', [
            'product_id' => $product->id,
            'notified_count' => $waitlistEntries->count(),
        ]);
    }
}
        