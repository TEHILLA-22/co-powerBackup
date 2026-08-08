<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_company',
        'customer_email',
        'customer_tier',
        
        // Status
        'status',
        'submitted_at',
        'processing_started_at',
        'approved_at',
        'rejected_at',
        'processed_by',
        'approved_by',
        'review_notes',
        'rejection_reason',
        
        // Financials
        'subtotal',
        'discount_total',
        'shipping_cost',
        'tax_total',
        'grand_total',
        
        // Shipping
        'shipping_address',
        'billing_address',
        'shipping_method',
        'shipping_weight',
        'tracking_number',
        'carrier',
        
        // Payment
        'payment_method',
        'payment_status',
        'paid_at',
        'transaction_id',
        
        // Fulfillment
        'shipped_at',
        'delivered_at',
        
        // Notes
        'customer_notes',
        'admin_notes',
        
        // Metadata
        'metadata',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'shipping_weight' => 'decimal:2',
        'submitted_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'metadata' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ============ STATUS LABELS ============
    
    public function getStatusLabelAttribute()
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'processing' => 'Processing Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'processing_fulfillment' => 'Processing Fulfillment',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'gray',
            'submitted' => 'yellow',
            'processing' => 'orange',
            'approved' => 'blue',
            'rejected' => 'red',
            'processing_fulfillment' => 'indigo',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red',
        ][$this->status] ?? 'gray';
    }

    // ============ STATUS TRANSITIONS ============
    
    public function canTransitionTo($status)
    {
        $transitions = [
            'draft' => ['submitted'],
            'submitted' => ['processing'],
            'processing' => ['approved', 'rejected'],
            'approved' => ['processing_fulfillment', 'cancelled'],
            'processing_fulfillment' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'rejected' => ['cancelled'],
        ];

        return in_array($status, $transitions[$this->status] ?? []);
    }

    public function transitionTo($status, $adminId = null, $notes = null)
    {
        if (!$this->canTransitionTo($status)) {
            throw new \Exception("Cannot transition from {$this->status} to {$status}");
        }

        $oldStatus = $this->status;
        $this->status = $status;

        // Update timestamps based on status
        switch ($status) {
            case 'submitted':
                $this->submitted_at = now();
                break;
            case 'processing':
                $this->processing_started_at = now();
                $this->processed_by = $adminId;
                $this->review_notes = $notes;
                break;
            case 'approved':
                $this->approved_at = now();
                $this->approved_by = $adminId;
                $this->review_notes = $notes;
                break;
            case 'rejected':
                $this->rejected_at = now();
                $this->approved_by = $adminId;
                $this->rejection_reason = $notes;
                break;
            case 'shipped':
                $this->shipped_at = now();
                break;
            case 'delivered':
                $this->delivered_at = now();
                break;
        }

        $this->save();

        return $this;
    }

    // ============ REVIEW METHODS ============
    
    public function startProcessing($adminId, $notes = null)
    {
        return $this->transitionTo('processing', $adminId, $notes);
    }

    public function approve($adminId, $notes = null, $verifyUser = true)
    {
        $this->transitionTo('approved', $adminId, $notes);

        // If this is the user's first order and admin approves, verify the user
        if ($verifyUser && $this->user->orders()->where('status', 'approved')->count() === 0) {
            $this->user->verifyAdmin($adminId);
        }

        return $this;
    }

    public function reject($adminId, $reason)
    {
        return $this->transitionTo('rejected', $adminId, $reason);
    }

    // ============ HELPER METHODS ============
    
    public function getTotalItemsAttribute()
    {
        return $this->items()->sum('quantity');
    }

    public function getTotalWeightAttribute()
    {
        return $this->items->sum(function($item) {
            return ($item->variant->weight_kg ?? 0) * $item->quantity;
        });
    }

    public function isFirstOrder()
    {
        return $this->user->orders()->where('status', 'approved')->count() === 0;
    }

    public function needsAdminVerification()
    {
        return $this->user->needsAdminVerification();
    }
}