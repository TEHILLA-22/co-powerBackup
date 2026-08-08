<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'discount_percentage',
        'priority_shipping',
        'dedicated_account_manager',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'priority_shipping' => 'boolean',
        'dedicated_account_manager' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}