<?php

namespace App\Services\Pricing;

class PriceResult
{
    public function __construct(
        public float $finalPrice,
        public float $originalPrice = 0.0,
        public float $totalDiscount = 0.0,
        public ?string $discountType = null,
        public bool $retailPriceHidden = false,
        public ?string $moqMessage = null,
        public ?string $saleMessage = null,
        public array $appliedRules = [],
    ) {}

    public function getSavingsAmount(): float
    {
        return round($this->originalPrice - $this->finalPrice, 2);
    }

    public function getSavingsPercentage(): float
    {
        if ($this->originalPrice <= 0) {
            return 0.0;
        }
        return round((($this->originalPrice - $this->finalPrice) / $this->originalPrice) * 100, 1);
    }
}