<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'mobile',
        'company_name',
        'company_registration_number',
        'vat_number',
        'customer_tier_id',
        
        // Verification states
        'is_verified',
        'verified_at',
        'is_admin_verified',
        'admin_verified_at',
        'admin_verified_by',
        'is_active',
        'suspended_at',
        'suspension_reason',
        
        // Preferences
        'language',
        'currency',
        'timezone',
        
        // Security
        'login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
        
        // OTP
        'otp_code',
        'otp_expires_at',
        'otp_attempts',
    ];

    protected $hidden = ['password', 'remember_token', 'otp_code'];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_admin_verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'admin_verified_at' => 'datetime',
        'suspended_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'otp_expires_at' => 'datetime',
        'login_attempts' => 'integer',
        'otp_attempts' => 'integer',
    ];

    public function customerTier()
    {
        return $this->belongsTo(CustomerTier::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function adminVerifiedBy()
    {
        return $this->belongsTo(User::class, 'admin_verified_by');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // ============ VERIFICATION METHODS ============
    
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until > now();
    }

    public function isFullyVerified()
    {
        return $this->is_verified && $this->is_admin_verified && $this->is_active;
    }

    public function needsAdminVerification()
    {
        return $this->is_verified && !$this->is_admin_verified && $this->is_active;
    }

    public function needsOtpVerification()
    {
        return !$this->is_verified && $this->is_active;
    }

    public function canPlaceOrder()
    {
        return $this->is_verified && $this->is_active;
    }

    // ============ ADMIN VERIFICATION ============
    
    public function verifyAdmin($adminId)
    {
        $this->update([
            'is_admin_verified' => true,
            'admin_verified_at' => now(),
            'admin_verified_by' => $adminId,
        ]);
        
        return $this;
    }

    public function unverifyAdmin($reason = null)
    {
        $this->update([
            'is_admin_verified' => false,
            'admin_verified_at' => null,
            'admin_verified_by' => null,
            'suspended_at' => now(),
            'suspension_reason' => $reason,
            'is_active' => false,
        ]);
        
        return $this;
    }

    // ============ OTP METHODS ============
    
    public function generateOtp()
    {
        $this->otp_code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_expires_at = now()->addMinutes(10);
        $this->otp_attempts = 0;
        $this->save();
        
        return $this->otp_code;
    }

    public function verifyOtp($code)
    {
        if ($this->otp_expires_at && now()->greaterThan($this->otp_expires_at)) {
            return ['success' => false, 'message' => 'OTP has expired.'];
        }

        if ($this->otp_attempts >= 5) {
            return ['success' => false, 'message' => 'Too many OTP attempts. Please request a new code.'];
        }

        if ($this->otp_code !== $code) {
            $this->increment('otp_attempts');
            return ['success' => false, 'message' => 'Invalid OTP code.'];
        }

        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
        ]);

        return ['success' => true, 'message' => 'Email verified successfully.'];
    }

    // ============ SECURITY METHODS ============
    
    public function recordLogin($ip = null)
    {
        $this->last_login_at = now();
        $this->last_login_ip = $ip ?? request()->ip();
        $this->login_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }

    public function incrementLoginAttempts()
    {
        $this->login_attempts++;
        if ($this->login_attempts >= 5) {
            $this->locked_until = now()->addMinutes(30);
        }
        $this->save();
    }

    public function getRemainingLockoutTimeAttribute()
    {
        if (!$this->isLocked()) {
            return 0;
        }
        return now()->diffInMinutes($this->locked_until);
    }

    // ============ ORDER STATUS HELPERS ============
    
    public function getOrderStatusCounts()
    {
        return $this->orders()
            ->select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }
}