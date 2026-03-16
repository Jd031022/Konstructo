<?php
// app/Models/UserProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profiles';

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
        'position', // Added position field
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'two_factor_secret',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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

    /**
     * Check if profile is complete (has all required fields for staff).
     */
    public function isComplete(): bool
    {
        $required = [
            $this->date_of_birth,
            $this->place_of_birth,
            $this->gender,
            $this->civil_status,
            $this->citizenship,
        ];

        return !in_array(null, $required) && !in_array('', $required);
    }

    /**
     * Get available staff positions.
     */
    public static function getAvailablePositions(): array
    {
        return [
            'engineer' => 'Engineer',
            'architect' => 'Architect',
            'BFP' => 'Bureau of Fire Protection (BFP)',
            'administrative_aide' => 'Administrative Aide',
        ];
    }

    /**
     * Get position display name.
     */
    public function getPositionDisplayAttribute(): ?string
    {
        if (!$this->position) {
            return null;
        }

        $positions = self::getAvailablePositions();
        
        return $positions[$this->position] ?? ucfirst(str_replace('_', ' ', $this->position));
    }

    /**
     * Get position badge color.
     */
    public function getPositionColorAttribute(): string
    {
        return match($this->position) {
            'engineer' => 'blue',
            'architect' => 'purple',
            'BFP' => 'red',
            'administrative_aide' => 'green',
            default => 'gray'
        };
    }

    /**
     * Check if user is engineer.
     */
    public function isEngineer(): bool
    {
        return $this->position === 'engineer';
    }

    /**
     * Check if user is architect.
     */
    public function isArchitect(): bool
    {
        return $this->position === 'architect';
    }

    /**
     * Check if user is BFP.
     */
    public function isBFP(): bool
    {
        return $this->position === 'BFP';
    }

    /**
     * Check if user is administrative aide.
     */
    public function isAdministrativeAide(): bool
    {
        return $this->position === 'administrative_aide';
    }

    /**
     * Check if user has technical position (Engineer or Architect).
     */
    public function isTechnical(): bool
    {
        return in_array($this->position, ['engineer', 'architect']);
    }

    /**
     * Scope a query to only include users with specific position.
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope a query to only include users with technical positions.
     */
    public function scopeTechnical($query)
    {
        return $query->whereIn('position', ['engineer', 'architect']);
    }

    /**
     * Scope a query to only include users who need to set position.
     */
    public function scopeNeedsPosition($query)
    {
        return $query->whereNull('position')
                    ->orWhere('position', '');
    }

    /**
     * Scope a query to only include users with position set.
     */
    public function scopeHasPosition($query)
    {
        return $query->whereNotNull('position')
                    ->where('position', '!=', '');
    }

    /**
     * Get the position badge HTML.
     */
    public function getPositionBadgeAttribute(): ?string
    {
        if (!$this->position) {
            return null;
        }

        $colors = [
            'engineer' => 'bg-blue-100 text-blue-800',
            'architect' => 'bg-purple-100 text-purple-800',
            'BFP' => 'bg-red-100 text-red-800',
            'administrative_aide' => 'bg-green-100 text-green-800',
        ];

        $color = $colors[$this->position] ?? 'bg-gray-100 text-gray-800';
        $displayName = $this->position_display;

        return "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$color}'>{$displayName}</span>";
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-capitalize position when saving
        static::saving(function ($profile) {
            if ($profile->position) {
                $profile->position = strtolower($profile->position);
            }
        });
    }
}