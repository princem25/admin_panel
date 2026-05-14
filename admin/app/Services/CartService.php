<?php

namespace App\Services;

use App\Events\Admin\ProductStockChanged;
use App\Events\Customer\ProductAddedToCart;
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
    protected ProductService $productService;

    public function __construct(DiscountService $discountService, ProductService $productService)
    {
        $this->discountService = $discountService;
        $this->productService = $productService;
    }

    /**
     * Helper to prevent concurrent cart operations for the same user/session.
     * Throws an exception if the lock cannot be acquired.
     */
    private function withinLock(\Closure $callback)
    {
        $lockKey = 'cart_lock_user_' . auth()->id();

        $lock = Cache::lock($lockKey, 10); // 10 second lock

        if (!$lock->get()) {
            Log::channel('products')->warning('Cart operation locked', [
                'user_id' => auth()->id(),
                'lock_key' => $lockKey
            ]);
            throw new \Exception('Please wait. Another cart operation is in progress.');
        }

        try {
            return $callback();
        } catch (\Exception $e) {
            Log::channel('products')->error('Cart operation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            throw $e;
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
        try {
            $cartArray = array_values($cart);
            Session::put('cart', $cartArray);

            if (blank($cartArray)) {
                Redis::del('cart:user:' . auth()->id());
            } else {
                Redis::set('cart:user:' . auth()->id(), json_encode($cartArray));
            }

            // Invalidate the cart summary cache for this user
            $userTag = 'customer_' . (auth()->id() ?? 'guest');
            Cache::tags([$userTag])->flush();

            Log::channel('products')->info('Cart session/redis updated', [
                'user_id' => auth()->id(),
                'items_count' => count($cartArray)
            ]);
        } catch (\Exception $e) {
            Log::channel('products')->error('Failed to update cart storage', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            // Still throw to notify the controller/user
            throw new \Exception('Could not save cart changes. Please try again.');
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
            $product = Product::findOrFail($productId);
                
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

            // Track as 'recently viewed'
            $this->productService->trackRecentlyViewed($productId);

            // Fire event for product added to cart
            event(new ProductAddedToCart($product, auth()->user()));
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
                    break;
                }
            }

            if ($found) {
                $this->setCart($cart);
            }
        });
    }

    /**
     * Remove a product completely from the cart — restores its stock.
     */
    public function remove(int $productId): void
    {
        $this->withinLock(function () use ($productId) {
            $cart = $this->getCart();
            $found = false;

            foreach ($cart as $key => $item) {
                if ($item['product_id'] === $productId) {
                    $found = true;
                    unset($cart[$key]);
                    break;
                }
            }

            if ($found) {
                $this->setCart($cart);
            }
        });
    }

    public function clearCart(): void
    {
        $this->withinLock(function () {
            $cart = $this->getCart();

            if (empty($cart)) {
                return;
            }

            $this->setCart([]);

            Log::channel('products')->info('Cart cleared and stock restored', ['count' => count($cart)]);
        });
    }

    /**
     * Complete the order — clears the cart without restoring stock.
     * Should be called after successfully saving an Order and its items.
     */
    public function completeOrder(): void
    {
        $this->withinLock(function () {
            Session::forget('cart');

            // Invalidate all customer-tagged caches on order completion
            Cache::tags(['customer'])->flush();

            Redis::del('cart:user:' . auth()->id());

            Log::channel('products')->info('Cart cleared (Order Completed)', ['user_id' => auth()->id()]);
        });
    }

    /**
     * Prepare cart items for views, using DiscountService for final price.
     */
    public function getCartSummary(): array
    {
        try {
            // ⚡ SELF-HEALING LOGIC: Prune deleted products from session & Redis before calculating
            $cart = $this->getCart();
            if (!empty($cart)) {
                $productIds = array_column($cart, 'product_id');
                $validIds = \App\Models\Product::whereIn('id', $productIds)->pluck('id')->toArray();
                
                if (count($productIds) !== count($validIds)) {
                    $healedCart = array_filter($cart, function($item) use ($validIds) {
                        return in_array($item['product_id'], $validIds);
                    });
                    $this->setCart(array_values($healedCart)); // Syncs fixed cart to Session AND Redis
                    Cache::tags(['customer'])->flush(); // Invalidate stale cached summaries
                }
            }

            $cacheKey = 'cart_summary_' . (auth()->id() ?? 'guest');
            $userTag = 'customer_' . (auth()->id() ?? 'guest');

            // Cache the entire cart summary for 10 minutes under 'customer' and user-specific tag
            return Cache::tags(['customer', $userTag])->remember($cacheKey, 600, function () {
                $cart = $this->getCart();
                
                $cartItems = collect($cart)->map(function ($item) {
                    $productId = $item['product_id'];
                    
                    $product = rescue(function () use ($productId) {
                        return Cache::tags(['products'])->remember("product_{$productId}", 3600, function () use ($productId) {
                            return Product::find($productId);
                        });
                    }, function (\Exception $e) use ($productId) {
                        Log::channel('products')->error('Product cache/fetch failure', [
                            'product_id' => $productId,
                            'error' => $e->getMessage()
                        ]);
                        return null;
                    });

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
            });
        } catch (\Exception $e) {
            Log::channel('products')->error('Cart summary calculation failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage()
            ]);

            return [
                'items'        => collect([]),
                'grandTotal'   => 0,
                'totalSavings' => 0,
            ];
        }
    }
}
