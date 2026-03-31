<?php

namespace App\Services;

class DiscountService
{
    /**
     * Return the effective (final) price after applying discount.
     * If discount is valid, it is used; otherwise the original price is returned.
     */
    public function apply(float $price, ?float $discountPrice = null): float
    {
        if ($discountPrice !== null && $discountPrice > 0 && $discountPrice < $price) {
            return $discountPrice;
        }

        return $price;
    }

    /**
     * Calculate the discount percentage.
     * Returns 0 if no valid discount exists.
     */
    public function calculatePercentage(float $price, ?float $discountPrice = null): int
    {
        if (!$discountPrice || $discountPrice <= 0 || $discountPrice >= $price) {
            return 0;
        }

        return (int) round((($price - $discountPrice) / $price) * 100);
    }

    /**
     * Check if a discount is valid (not null, positive, and less than price).
     */
    public function hasValidDiscount(float $price, ?float $discountPrice = null): bool
    {
        return $discountPrice !== null && $discountPrice > 0 && $discountPrice < $price;
    }

    /**
     * Calculate total savings amount.
     */
    public function savings(float $price, ?float $discountPrice = null): float
    {
        if (!$this->hasValidDiscount($price, $discountPrice)) {
            return 0.0;
        }

        return round($price - $discountPrice, 2);
    }
}
