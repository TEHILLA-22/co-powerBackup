<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ean',
        'sku',
        'upc',
        'name',
        'slug',
        'short_description',
        'description',
        'brand',
        'manufacturer',
        'category_id',

        // MOQ
        'moq',
        'moq_enforced',
        'moq_increment',
        'tier_moq',

        'main_image',
        'gallery_images',
        'is_active',
        'is_featured',
        'is_on_sale',
        'sale_start_date',
        'sale_end_date',
        'seo_tags',
        'meta_title',
        'meta_description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'gallery_images' => 'json',
        'seo_tags' => 'json',
        'tier_moq' => 'json',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_on_sale' => 'boolean',
        'moq_enforced' => 'boolean',
        'moq' => 'integer',
        'moq_increment' => 'integer',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // ============ MOQ METHODS ============

    /**
     * Get effective MOQ for a specific customer tier
     */
    public function getEffectiveMoq(?int $tierId = null): int
    {
        // Check tier-specific MOQ
        if ($tierId && $this->tier_moq && isset($this->tier_moq[$tierId])) {
            return (int) $this->tier_moq[$tierId];
        }

        return $this->moq;
    }

    /**
     * Get MOQ increment
     */
    public function getEffectiveIncrement(?int $tierId = null): int
    {
        return $this->moq_increment ?? 1;
    }

    /**
     * Validate quantity against MOQ
     */
    public function validateMoq(int $quantity, ?int $tierId = null): array
    {
        if (!$this->moq_enforced) {
            return ['valid' => true];
        }

        $moq = $this->getEffectiveMoq($tierId);
        $increment = $this->getEffectiveIncrement($tierId);

        if ($quantity < $moq) {
            return [
                'valid' => false,
                'message' => "Minimum order quantity is {$moq} units.",
                'required' => $moq,
                'current' => $quantity,
            ];
        }

        if ($increment > 1 && ($quantity - $moq) % $increment !== 0) {
            return [
                'valid' => false,
                'message' => "Quantity must be in increments of {$increment}.",
                'required' => $increment,
                'current' => $quantity,
            ];
        }

        return ['valid' => true];
    }

    public function getLowestPriceAttribute()
    {
        return $this->variants()->min('base_price');
    }

    public function getHighestPriceAttribute()
    {
        return $this->variants()->max('base_price');
    }

    public function getTotalStockAttribute()
    {
        return $this->variants()->sum('stock_quantity');
    }
}
