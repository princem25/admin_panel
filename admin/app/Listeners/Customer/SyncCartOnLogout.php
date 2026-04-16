<?php

namespace App\Listeners\Customer;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Services\CartService;

class SyncCartOnLogout
{
    protected $cartService;

    /**
     * Create the event listener.
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }   

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;

        // Get their final cart before session is destroyed
        $cart = $this->cartService->getCart();

        // 3. On user logout: Take cart data from session, Store/update it into Redis, Clear session
        if (!empty($cart)) {

            Redis::set("cart:user:{$user->id}", json_encode($cart));

            Log::channel('products')->info('Logout cart sync completed', [
                'user_id' => $user->id,
                'item_count' => count($cart),
                'action' => 'stored_in_redis',
            ]);

        } else {

            // If the cart is empty, we remove the key from Redis to mirror the state
            Redis::del("cart:user:{$user->id}");

            Log::channel('products')->info('Logout cart sync completed', [
                'user_id' => $user->id,
                'item_count' => 0,
                'action' => 'removed_from_redis',
            ]);
            
        }
    }
}
