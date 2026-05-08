<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductWaitlist;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WaitlistService
{
    /**
     * Add the user to the product waitlist.
     *
     * @param User $user
     * @param Product $product
     * @return array
     */
    public function joinWaitlist($user, Product $product): array
    {
        // Guard: product must be out of stock
        if ($product->stock > 0) {
            return [
                'status' => 'error',
                'message' => 'This product is currently available. Add it to your cart!'
            ];
        }

        // Guard: prevent duplicate entries
        $alreadyJoined = ProductWaitlist::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyJoined) {
            return [
                'status' => 'info',
                'message' => "You're already on the waitlist for \"{$product->name}\". We'll email you when it's back!"
            ];
        }

        // Store in waitlist
        ProductWaitlist::create([
            'product_id' => $product->id,
            'user_id'    => $user->id,
        ]);

        Log::channel('products')->info('User joined product waitlist', [
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'user_id'      => $user->id,
            'user_email'   => $user->email,
        ]);

        return [
            'status' => 'success',
            'message' => "You're on the waitlist! We'll notify you at {$user->email} when \"{$product->name}\" is back in stock."
        ];
    }

    /**
     * Check if the user is on the waitlist for a product.
     *
     * @param User|null $user
     * @param int $productId
     * @return bool
     */
    public function isUserOnWaitlist($user, int $productId): bool
    {
        if (!$user) {
            return false;
        }

        return ProductWaitlist::where('product_id', $productId)
            ->where('user_id', $user->id)
            ->exists();
    }
}
