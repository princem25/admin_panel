<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Services\PriceCalculatorService;
use Illuminate\Support\ServiceProvider;

class CustomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PriceCalculatorService::class, function () {
            return new PriceCalculatorService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
             // --- Model Observers ---
        Product::observe(ProductObserver::class);
        Order::observe(OrderObserver::class);
    }
}
