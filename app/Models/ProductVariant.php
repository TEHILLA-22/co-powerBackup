<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'variant_type',
        'variant_name',
        'quantity_per_unit',
        'units_per_case',
        'cases_per_layer',
        'layers_per_pallet',

        // MOQ override
        'moq',
        'moq_increment',

        'base_price',
        'cost_price',
        'sale_price',
        'stock_quantity',
        'reserved_quantity',
        'reorder_level',
        'reorder_quantity',
        'last_stock_update',
        'weight_kg',
        'length_cm',
        'width_cm',
        'height_cm',
        'is_active',
        'in_stock',
        'allow_backorder',
        'min_order_quantity',
        'max_order_quantity',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'weight_kg' => 'decimal:3',
        'length_cm' => 'decimal:2',
        'width_cm' => 'decimal:2',
        'height_cm' => 'decimal:2',
        'moq' => 'integer',
        'moq_increment' => 'integer',
        'is_active' => 'boolean',
        'in_stock' => 'boolean',
        'allow_backorder' => 'boolean',
        'last_stock_update' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getAvailableQuantityAttribute()
    {
        return $this->stock_quantity - $this->reserved_quantity;
    }

    public function getCurrentPriceAttribute()
    {
        if ($this->sale_price && $this->product->is_on_sale) {
            return $this->sale_price;
        }
        return $this->base_price;
    }

    public function getSkuAttribute()
    {
        $baseSku = $this->product->sku;
        if ($this->variant_type !== 'unit') {
            return $baseSku . '-' . strtoupper(substr($this->variant_type, 0, 1));
        }
        return $baseSku;
    }

    // ============ MOQ METHODS ============

    /**
     * Get effective MOQ (variant override or product default)
     */
    public function getEffectiveMoq(?int $tierId = null): int
    {
        if ($this->moq !== null) {
            return (int) $this->moq;
        }

        return $this->product->getEffectiveMoq($tierId);
    }

    /**
     * Get MOQ increment
     */
    public function getEffectiveIncrement(?int $tierId = null): int
    {
        if ($this->moq_increment !== null) {
            return (int) $this->moq_increment;
        }

        return $this->product->getEffectiveIncrement($tierId);
    }

    /**
     * Validate quantity against MOQ
     */
    public function validateMoq(int $quantity, ?int $tierId = null): array
    {
        if (!$this->product->moq_enforced) {
            return ['valid' => true];
        }

        $moq = $this->getEffectiveMoq($tierId);
        $increment = $this->getEffectiveIncrement($tierId);

        if ($quantity < $moq) {
            return [
                'valid' => false,
                'message' => "Minimum order quantity is {$moq} units for {$this->variant_type}.",
                'required' => $moq,
                'current' => $quantity,
            ];
        }

        if ($increment > 1 && ($quantity - $moq) % $increment !== 0) {
            return [
                'valid' => false,
                'message' => "Quantity must be in increments of {$increment} for {$this->variant_type}.",
                'required' => $increment,
                'current' => $quantity,
            ];
        }

        return ['valid' => true];
    }
}
