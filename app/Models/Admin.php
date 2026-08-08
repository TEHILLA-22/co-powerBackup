<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_enabled',
        'password_changed_at',
        'force_password_change',
        'language',
        'timezone',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'force_password_change' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'password_changed_at' => 'datetime',
        'login_attempts' => 'integer',
        'email_verified_at' => 'datetime',
        'two_factor_recovery_codes' => 'json',
    ];

    // ============ RELATIONSHIPS ============
    
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // ============ ROLE CHECKS ============
    
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    // ============ SECURITY METHODS ============
    
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until > now();
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getRemainingLockoutTimeAttribute()
    {
        if (!$this->isLocked()) {
            return 0;
        }
        return now()->diffInMinutes($this->locked_until);
    }

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

    public function resetLoginAttempts()
    {
        $this->login_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }

    // ============ TWO FACTOR AUTH ============
    
    public function enableTwoFactor()
    {
        $this->two_factor_enabled = true;
        $this->save();
    }

    public function disableTwoFactor()
    {
        $this->two_factor_enabled = false;
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->save();
    }

    public function hasTwoFactorEnabled()
    {
        return $this->two_factor_enabled && $this->two_factor_secret;
    }
}