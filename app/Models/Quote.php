<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quote_number',
        'user_id',
        'customer_company',
        'customer_email',
        'customer_tier',
        'status',
        'items',
        'subtotal',
        'discount_total',
        'shipping_cost',
        'tax_total',
        'grand_total',
        'valid_from',
        'valid_until',
        'submitted_at',
        'approved_at',
        'converted_at',
        'processed_by',
        'review_notes',
        'rejection_reason',
        'customer_notes',
        'admin_notes',
        'converted_order_id',
        'metadata',
    ];

    protected $casts = [
        'items' => 'json',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'converted_at' => 'datetime',
        'metadata' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    public function convertedOrder()
    {
        return $this->belongsTo(Order::class, 'converted_order_id');
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }
}