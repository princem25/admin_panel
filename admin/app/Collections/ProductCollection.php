<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ProductCollection extends Collection
{
    /**
     * Search products by name (case-insensitive)
     */
    public function searchTerm($term): self
    {
        try {
            if (empty($term)) return $this;

            return $this->filter(function ($product) use ($term) {
                return str_contains(strtolower($product->name), strtolower($term));
            });
        } catch (\Exception $e) {
            $this->logError('search', $e);
            return $this;
        }
    }

    /**
     * Filter by category_id (single or array)
     */
    public function byCategory($categories): self
    {
        try {
            if (empty($categories)) return $this;

            $categories = is_array($categories) ? $categories : [$categories];
            return $this->whereIn('category_id', $categories);
        } catch (\Exception $e) {
            $this->logError('byCategory', $e);
            return $this;
        }
    }

    /**
     * Filter by price range
     */
    public function byPriceRange($min = null, $max = null): self
    {
        try {
            if ($min === null && $max === null) return $this;

            return $this->filter(function ($product) use ($min, $max) {
                $price = $product->discount_price ?? $product->price;
                $minPass = $min === null || $price >= $min;
                $maxPass = $max === null || $price <= $max;
                return $minPass && $maxPass;
            });
        } catch (\Exception $e) {
            $this->logError('byPriceRange', $e);
            return $this;
        }
    }

    /**
     * Return products in stock (stock > 0)
     */
    public function inStock($active = true): self
    {
        try {
            if (!$active) return $this;
            return $this->where('stock', '>', 0);
        } catch (\Exception $e) {
            $this->logError('inStock', $e);
            return $this;
        }
    }

    /**
     * Return products on sale (discount > 0)
     */
    public function onSale($active = true): self
    {
        try {
            if (!$active) return $this;
            return $this->filter(function ($product) {
                $hasDiscountValue = ($product->discount ?? 0) > 0;
                $hasDiscountPrice = !empty($product->discount_price) && $product->discount_price < $product->price;
                return $hasDiscountValue || $hasDiscountPrice;
            });
        } catch (\Exception $e) {
            $this->logError('onSale', $e);
            return $this;
        }
    }

    /**
     * Return featured products
     */
    public function featured($active = true): self
    {
        try {
            if (!$active) return $this;
            return $this->where('is_featured', true);
        } catch (\Exception $e) {
            $this->logError('featured', $e);
            return $this;
        }
    }

    /**
     * Sort products by various types
     */
    public function sortProducts($type = 'newest'): self
    {
        try {
            return match ($type) {
                'price_low'  => $this->sortBy(fn($p) => $p->discount_price ?? $p->price),
                'price_high' => $this->sortByDesc(fn($p) => $p->discount_price ?? $p->price),
                'popularity' => $this->sortByDesc('stock'),
                'newest'     => $this->sortByDesc('created_at'),
                default      => $this->sortByDesc('created_at'),
            };
        } catch (\Exception $e) {
            $this->logError('sortProducts', $e);
            return $this;
        }
    }

    /**
     * Calculate total inventory value
     */
    public function totalValue(): float
    {
        try {
            return $this->sum(fn ($product) => ($product->discount_price ?? $product->price ?? 0) * ($product->stock ?? 0));
        } catch (\Exception $e) {
            $this->logError('totalValue', $e);
            return 0.0;
        }
    }

    protected function logError(string $method, \Exception $e): void
    {
        Log::error('Product filtering error', [
            'method'  => $method,
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString()
        ]);
    }
}
