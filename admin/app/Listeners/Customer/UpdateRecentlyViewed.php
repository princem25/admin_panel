<?php

namespace App\Listeners\Customer;

use App\Events\Customer\ProductViewed;
use Illuminate\Support\Facades\Log;

class UpdateRecentlyViewed
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Customer\ProductViewed  $event
     * @return void
     */
    public function handle(ProductViewed $event)
    {
        $productId = $event->product->id;
        
        Log::info("Recently viewed logic executed for Product ID {$productId}");
    }
}
