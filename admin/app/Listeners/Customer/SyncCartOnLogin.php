<?php

namespace App\Listeners\Customer;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use App\Services\CartService;

class SyncCartOnLogin
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
     * Handle the login event.
     * Retrieves the user's persistent Redis cart, discarding any guest items.
     */
    public function handle(Login $event): void
    {
        // 1. Prevent duplicate operations within the same login session
        if (Session::has('cart_merged')) {
            Log::channel('products')->info('Login cart sync skipped: cart already merged', [
                'user_id' => $event->user->id,
            ]);
            return;
        }

        $user = $event->user;

        // 2. Retrieve persistent user cart (redis)
        $redisCartData = Redis::get("cart:user:{$user->id}");
        $redisCart = $redisCartData ? json_decode($redisCartData, true) : [];

        // 3. Update the cart with auth user's data (this discards guest items)
        $this->cartService->setCart($redisCart);

        // 4. Mark as retrieved to ensure idempotency
        Session::put('cart_merged', true);

        Log::channel('products')->info('Cart Retrieved: Completed', [
            'user_id'     => $user->id,
            'item_count'  => count($redisCart),
        ]);
    }
}
