<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Services\Pricing\Contracts\PricingEngineInterface;
use App\Services\Pricing\TierPricingEngine;
use Illuminate\Support\Facades\View;
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
        View::composer(['partials.header', 'partials.mobile-menu', 'welcome'], function ($view) {
            $view->with('navCategories', $this->navCategories());
        });

        View::composer('welcome', function ($view) {
            $view->with('featuredProducts', Product::where('is_active', true)
                ->where('is_featured', true)
                ->with('variants')
                ->limit(8)
                ->get());
        });
    }

    /**
     * Parent categories with children and product counts (children rolled up).
     */
    protected function navCategories()
    {
        return Category::where('is_active', true)
            ->parents()
            ->with(['children' => function ($q) {
                $q->where('is_active', true)
                    ->withCount('products')
                    ->orderBy('display_order');
            }])
            ->withCount('products')
            ->orderBy('display_order')
            ->get()
            ->map(function ($parent) {
                $parent->products_count = $parent->products_count + $parent->children->sum('products_count');
                return $parent;
            });
    }
}
