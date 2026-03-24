<?php

namespace App\Providers;

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
        return new PriceCalculatorService();
    });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
