<?php

namespace App\Services\Pricing;

use App\Models\ProductVariant;
use App\Models\CustomerTier;
use App\Models\User;
use App\Services\Pricing\Contracts\PricingEngineInterface;

class TierPricingEngine implements PricingEngineInterface
{
    public function calculatePrice(int $variantId, int $quantity, int $userId): PriceResult
    {
        $variant = ProductVariant::with('product')->find($variantId);

        if (!$variant) {
            return new PriceResult(
                finalPrice: 0.0,
                originalPrice: 0.0,
                totalDiscount: 0.0,
            );
        }

        $originalPrice = (float) $variant->base_price;
        $finalPrice = $originalPrice;
        $discountType = null;
        $discount = 0.0;

        // Sale price takes priority when active
        if ($variant->product->is_on_sale && $variant->sale_price) {
            $finalPrice = (float) $variant->sale_price;
            $discountType = 'sale';
        }

        // Apply tier discount on top of the base/sale price
        $tierDiscount = $this->getTierDiscount($userId);
        if ($tierDiscount > 0) {
            $finalPrice *= (1 - ($tierDiscount / 100));
            $discountType = $discountType === 'sale' ? 'sale' : 'tier';
        }

        $finalPrice = round($finalPrice, 2);
        $discount = round($originalPrice - $finalPrice, 2);

        return new PriceResult(
            finalPrice: $finalPrice,
            originalPrice: $originalPrice,
            totalDiscount: $discount,
            discountType: $discountType,
        );
    }

    protected function getTierDiscount(int $userId): float
    {
        $user = User::find($userId);

        if (!$user || !$user->customer_tier_id) {
            return 0.0;
        }

        $tier = CustomerTier::find($user->customer_tier_id);

        return $tier ? (float) $tier->discount_percentage : 0.0;
    }
}