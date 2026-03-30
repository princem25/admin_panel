<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get the cart array from the session.
     */
    public function getCart()
    {
        return Session::get('cart', []);
    }

    /**
     * Set the cart array into the session and sync to Redis if authenticated.
     */
    public function setCart($cart)
    {
        $cartArray = array_values($cart);
        Session::put('cart', $cartArray);

        if (auth()->check()) {
            if (empty($cartArray)) {
                \Illuminate\Support\Facades\Redis::del("cart:user:" . auth()->id());
            } else {
                \Illuminate\Support\Facades\Redis::set("cart:user:" . auth()->id(), json_encode($cartArray));
            }
        }
    }

    /**
     * Add or increment a product in the session cart
     */
    public function addToCart($productId, $qty = 1)
    {
        $cart = $this->getCart();
        $found = false;

        foreach ($cart as &$item) {
            if ($item['product_id'] == $productId) {
                $item['qty'] += $qty;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = [
                'product_id' => $productId,
                'qty' => $qty
            ];
        }

        $this->setCart($cart);
    }

    /**
     * Increase quantity of a product by 1
     */
    public function increase($productId)
    {
        $this->addToCart($productId, 1);
    }

    /**
     * Decrease quantity of a product by 1
     */
    public function decrease($productId)
    {
        $cart = $this->getCart();

        foreach ($cart as $key => &$item) {
            if ($item['product_id'] == $productId) {
                if ($item['qty'] > 1) {
                    $item['qty'] -= 1;
                } else {
                    unset($cart[$key]);
                }
                break;
            }
        }

        $this->setCart($cart);
    }

    /**
     * Remove a product completely from the cart
     */
    public function remove($productId)
    {
        $cart = $this->getCart();

        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $productId) {
                unset($cart[$key]);
                break;
            }
        }

        $this->setCart($cart);
    }

    /**
     * Clear the cart
     */
    public function clearCart()
    {
        Session::forget('cart');
        
        if (auth()->check()) {
            \Illuminate\Support\Facades\Redis::del("cart:user:" . auth()->id());
        }
    }

    /**
     * Merge items from two different cart arrays (e.g. Session & Redis).
     * If the same product exists in both, their quantities are summed.
     */
    public function mergeCarts(array $redisCart, array $sessionCart): array
    {
        $merged = [];

        // Add everything from Redis first
        foreach ($redisCart as $item) {
            $merged[$item['product_id']] = $item;
        }

        // Merge session items into the array
        foreach ($sessionCart as $item) {
            $pid = $item['product_id'];
            if (isset($merged[$pid])) {
                $merged[$pid]['qty'] += $item['qty'];
            } else {
                $merged[$pid] = $item;
            }
        }

        return array_values($merged);
    }

    /**
     * Prepare cart items for the views, including grand total.
     */
    public function getCartSummary()
    {
        $cart = $this->getCart();
        $productIds = array_column($cart, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = collect($cart)->map(function ($item) use ($products) {
            $product = $products->get($item['product_id']);
            if (!$product) return null;

            return (object) [
                'product_id' => $product->id,
                'quantity' => $item['qty'],
                'total_price' => $product->price * $item['qty'],
                'product' => $product,
            ];
        })->filter()->values();

        $grandTotal = $cartItems->sum('total_price');

        return [
            'items' => $cartItems,
            'grandTotal' => $grandTotal,
        ];
    }
}
