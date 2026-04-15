<?php

namespace App\Listeners;

use App\Events\ProductViewed;
use Illuminate\Support\Facades\Log;

class UpdateRecentlyViewed
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductViewed  $event
     * @return void
     */
    public function handle(ProductViewed $event)
    {
        $productId = $event->product->id;
        
        Log::info("Recently viewed logic executed for Product ID {$productId}");
    }
}
