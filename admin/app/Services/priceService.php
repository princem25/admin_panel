<?php
namespace App\Services;

class PriceCalculatorService
{
    public function calculate($price, $discount = 0)
    {
        // Apply discount
        $priceAfterDiscount = $price - ($price * $discount / 100);

        // Add tax from config
        $tax = config('company.tax');
        $finalPrice = $priceAfterDiscount + ($priceAfterDiscount * $tax / 100);

        return round($finalPrice, 2);
    }
}