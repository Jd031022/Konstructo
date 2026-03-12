<?php
// app/Models/UserProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'place_of_birth',
        'gender',
        'civil_status',
        'citizenship',
        'tin',
        'telephone',
        'alternative_email',
        'house_number',
        'street',
        'barangay',
        'city',
        'province',
        'last_login_at',
        'password_changed_at',
        'two_factor_secret',
        'two_factor_enabled',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->house_number,
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Check if 2FA is enabled.
     */
    public function isTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && !is_null($this->two_factor_secret);
    }
}