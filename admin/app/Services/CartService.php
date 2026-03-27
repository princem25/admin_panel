<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Get all cart items for a specific user.
     * Optionally eager loads the product.
     */
    public function getUserCart($userId)
    {
        return Cart::with('product')->where('user_id', $userId)->get();
    }

    /**
     * Calculate the grand total for a collection of cart items.
     */
    public function calculateGrandTotal($cartItems)
    {
        return $cartItems->sum('total_price');
    }

    /**
     * Utility method to get both items and the calculated total.
     */
    public function getCartSummary($userId)
    {
        $cartItems = $this->getUserCart($userId);
        $grandTotal = $this->calculateGrandTotal($cartItems);

        return [
            'items' => $cartItems,
            'grandTotal' => $grandTotal,
        ];
    }
}
