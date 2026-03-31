<?php

namespace App\Services;

use App\Exceptions\ProductOutOfStockException;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected DiscountService $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * Helper to prevent concurrent cart operations for the same user/session.
     * Throws an exception if the lock cannot be acquired.
     */
    private function withinLock(\Closure $callback)
    {
        $lockKey = auth()->check() 
            ? 'cart_lock_user_' . auth()->id() 
            : 'cart_lock_session_' . Session::getId();

        $lock = Cache::lock($lockKey, 10); // 10 second lock

        if (!$lock->get()) {
            throw new \Exception('Please wait. Another cart operation is in progress.');
        }

        try {
            return $callback();
        } finally {
            $lock->release();
        }
    }

    /**
     * Get the cart array from the session.
     */
    public function getCart(): array
    {
        return Session::get('cart', []);
    }

    /**
     * Set the cart array into the session and sync to Redis if authenticated.
     */
    public function setCart(array $cart): void
    {
        $cartArray = array_values($cart);
        Session::put('cart', $cartArray);

        if (auth()->check()) {
            if (empty($cartArray)) {
                Redis::del('cart:user:' . auth()->id());
            } else {
                Redis::set('cart:user:' . auth()->id(), json_encode($cartArray));
            }
        }
    }

    /**
     * Add or increment a product in the session cart.
     * Validates stock before adding and decrements DB stock.
     *
     * @throws ProductOutOfStockException
     */
    public function addToCart(int $productId, int $qty = 1): void
    {
        $this->withinLock(function () use ($productId, $qty) {
            DB::transaction(function () use ($productId, $qty) {
                //  Lock the product row for update to prevent desync
                $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

                //  Prevent adding if no stock
                if ($product->stock <= 0) {
                    throw new ProductOutOfStockException("'{$product->name}' is out of stock.");
                }

                //  Prevent overselling
                $currentQtyInCart = $this->getQtyInCart($productId);
                if (($currentQtyInCart + $qty) > $product->stock) {
                    throw new ProductOutOfStockException(
                        "Only {$product->stock} units of '{$product->name}' available."
                    );
                }

                // Merge into cart session
                $cart = $this->getCart();
                $found = false;

                foreach ($cart as &$item) {
                    if ($item['product_id'] === $productId) {
                        $item['qty'] += $qty;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $cart[] = [
                        'product_id' => $productId,
                        'qty'        => $qty,
                    ];
                }

                $this->setCart($cart);

                //  Decrement stock in DB atomically within transaction
                $product->decrement('stock', $qty);

                Log::channel('products')->info('Stock decremented', [
                    'product_id' => $productId,
                    'qty'        => $qty,
                    'new_stock'  => $product->fresh()->stock,
                ]);
            });
        });
    }

    /**
     * Get the quantity of a specific product already in the cart.
     */
    public function getQtyInCart(int $productId): int
    {
        foreach ($this->getCart() as $item) {
            if ($item['product_id'] === $productId) {
                return $item['qty'];
            }
        }

        return 0;
    }

    /**
     * Increase quantity by 1 — respects stock limits.
     *
     * @throws ProductOutOfStockException
     */
    public function increase(int $productId): void
    {
        $this->addToCart($productId, 1);
    }

    /**
     * Decrease quantity of a product by 1, restoring stock in DB.
     */
    public function decrease(int $productId): void
    {
        $this->withinLock(function () use ($productId) {
            DB::transaction(function () use ($productId) {
                $cart = $this->getCart();
                $found = false;

                foreach ($cart as $key => &$item) {
                    if ($item['product_id'] === $productId) {
                        $found = true;
                        if ($item['qty'] > 1) {
                            $item['qty'] -= 1;
                        } else {
                            unset($cart[$key]);
                        }

                        // Restore 1 unit of stock atomically
                        Product::where('id', $productId)->lockForUpdate()->increment('stock', 1);
                        break;
                    }
                }

                if ($found) {
                    $this->setCart($cart);
                    Log::channel('products')->info('Stock restored (decrease)', ['product_id' => $productId]);
                }
            });
        });
    }

    /**
     * Remove a product completely from the cart — restores its stock.
     */
    public function remove(int $productId): void
    {
        $this->withinLock(function () use ($productId) {
            DB::transaction(function () use ($productId) {
                $cart = $this->getCart();
                $found = false;

                foreach ($cart as $key => $item) {
                    if ($item['product_id'] === $productId) {
                        $found = true;
                        //  Auto-restore stock atomically
                        Product::where('id', $productId)->lockForUpdate()->increment('stock', $item['qty']);

                        Log::channel('products')->info('Stock restored on cart removal', [
                            'product_id' => $productId,
                            'qty'        => $item['qty'],
                        ]);

                        unset($cart[$key]);
                        break;
                    }
                }

                if ($found) {
                    $this->setCart($cart);
                }
            });
        });
    }

    /**
     * Clear the cart — restores all stock quantities.
     */
    public function clearCart(): void
    {
        $this->withinLock(function () {
            DB::transaction(function () {
                $cart = $this->getCart();

                if (empty($cart)) {
                    return;
                }

                // Restore stock for all items atomically
                foreach ($cart as $item) {
                    Product::where('id', $item['product_id'])->lockForUpdate()->increment('stock', $item['qty']);
                }

                Session::forget('cart');

                if (auth()->check()) {
                    Redis::del('cart:user:' . auth()->id());
                }

                Log::channel('products')->info('Cart cleared and stock restored', ['count' => count($cart)]);
            });
        });
    }

    /**
     * Merge items from two different cart arrays (e.g. Session & Redis).
     */
    public function mergeCarts(array $redisCart, array $sessionCart): array
    {
        $merged = [];

        foreach ($redisCart as $item) {
            $merged[$item['product_id']] = $item;
        }

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
     * Prepare cart items for views, using DiscountService for final price.
     */
    public function getCartSummary(): array
    {
        $cart       = $this->getCart();
        $productIds = array_column($cart, 'product_id');
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = collect($cart)->map(function ($item) use ($products) {
            $product = $products->get($item['product_id']);
            if (!$product) {
                return null;
            }

            $finalPrice = $this->discountService->apply($product->price, $product->discount_price);

            return (object) [
                'product_id'   => $product->id,
                'quantity'     => $item['qty'],
                'unit_price'   => $finalPrice,
                'total_price'  => round($finalPrice * $item['qty'], 2),
                'product'      => $product,
                'has_discount' => $this->discountService->hasValidDiscount($product->price, $product->discount_price),
                'savings'      => $this->discountService->savings($product->price, $product->discount_price) * $item['qty'],
            ];
        })->filter()->values();

        $grandTotal   = $cartItems->sum('total_price');
        $totalSavings = $cartItems->sum('savings');

        return [
            'items'        => $cartItems,
            'grandTotal'   => $grandTotal,
            'totalSavings' => $totalSavings,
        ];
    }
}
