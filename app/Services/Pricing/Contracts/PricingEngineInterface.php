<?php

namespace App\Services\Pricing\Contracts;

use App\Services\Pricing\PriceResult;

interface PricingEngineInterface
{
    /**
     * Calculate the price for a variant at a given quantity.
     */
    public function calculatePrice(int $variantId, int $quantity, int $userId): PriceResult;
}