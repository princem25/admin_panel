<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Redis;
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
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Try getting cart from Redis
        $redisCartData = Redis::get("cart:user:{$user->id}");
        $redisCart = $redisCartData ? json_decode($redisCartData, true) : [];

        // Get whatever is currently in session (guest cart)
        $sessionCart = $this->cartService->getCart();

        if (empty($sessionCart) && !empty($redisCart)) {

            // Only Redis cart exists
            $this->cartService->setCart($redisCart);

        } elseif (!empty($sessionCart) && empty($redisCart)) {

            // Only session cart exists (user added as guest, first time log-in)
            $this->cartService->setCart($sessionCart);

        } elseif (!empty($sessionCart) && !empty($redisCart)) {

            // Both exist: we need to merge them
            $mergedCart = $this->cartService->mergeCarts($redisCart, $sessionCart);
            $this->cartService->setCart($mergedCart);
            
        }
    }
}
