<?php

namespace App\Listeners;

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
     * Merges guest session cart into user's persistent Redis cart accurately.
     */
    public function handle(Login $event): void
    {
        // 1. Prevent duplicate merge operations within the same login session
        if (Session::has('cart_merged')) {
            return;
        }

        $user = $event->user;

        // 2. Retrieve guest cart (session) and persistent user cart (redis)
        $sessionCart = $this->cartService->getCart();
        $redisCartData = Redis::get("cart:user:{$user->id}");
        $redisCart = $redisCartData ? json_decode($redisCartData, true) : [];

        // 3. Optimization: If guest cart is empty, just sync Redis to Session and mark done
        if (empty($sessionCart)) {
            if (!empty($redisCart)) {
                $this->cartService->setCart($redisCart);
            }
            Session::put('cart_merged', true);
            return;
        }

        // 4. Execution: Merge guest items into user cart
        Log::channel('products')->info('Cart Merge: Initiated', ['user_id' => $user->id]);

        $mergedCart = $this->cartService->mergeCarts($redisCart, $sessionCart);

        // 5. Update both the current session buffer and the persistent store
        $this->cartService->setCart($mergedCart);

        // 6. Mark as merged to ensure idempotency
        Session::put('cart_merged', true);

        Log::channel('products')->info('Cart Merge: Completed', [
            'user_id'     => $user->id,
            'item_count'  => count($mergedCart),
        ]);
    }
}
