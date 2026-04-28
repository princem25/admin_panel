<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class ExternalApiService
{
    /**
     * Fetch all categories.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function getCategories()
    {
        return Http::jsonApi()->get('/products/categories');
    }

    /**
     * Fetch products, optionally filtered by category.
     *
     * @param string|null $category
     * @param int $limit
     * @return \Illuminate\Http\Client\Response
     */
    public function getProducts($category = null, $limit = 20)
    {
        $url = '/products';
        
        if ($category && $category !== 'all') {
            $url .= '/category/' . urlencode($category);
        }

        return Http::jsonApi()->get($url, ['limit' => $limit]);
    }

    /**
     * Fetch products and categories concurrently using Http::pool.
     *
     * @param string|null $category
     * @param int $limit
     * @return array
     */
    public function getDashboardData($category = null, $limit = 20)
    {
        $url = '/products';
        if ($category && $category !== 'all') {
            $url .= '/category/' . urlencode($category);
        }

        return Http::pool(fn (Pool $pool) => [
            $pool->as('categories')->jsonApi()->get('/products/categories'),
            $pool->as('products')->jsonApi()->get($url, ['limit' => $limit]),
        ]);
    }
}
