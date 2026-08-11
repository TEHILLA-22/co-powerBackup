<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $fillable = [
        'user_id',
        'basket_key',
        'product_variant_id',
        'product_id',
        'quantity',
        'variant_type',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}