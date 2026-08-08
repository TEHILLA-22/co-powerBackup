<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'product_ean',
        'variant_type',
        'quantity',
        'unit_price',
        'discount_price',
        'line_total',
        'tax_amount',
        'applied_discounts',
        'status',
        'shipped_quantity',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'applied_discounts' => 'json',
        'quantity' => 'integer',
        'shipped_quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->shipped_quantity;
    }
}