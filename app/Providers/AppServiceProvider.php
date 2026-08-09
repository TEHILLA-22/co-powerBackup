<?php

namespace App\Providers;

use App\Services\Pricing\Contracts\PricingEngineInterface;
use App\Services\Pricing\TierPricingEngine;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PricingEngineInterface::class, TierPricingEngine::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
