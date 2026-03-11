<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_info',
        'login_at',
        'last_activity_at',
        'logout_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'device_info' => 'array',
        'login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'logout_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive sessions.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to get sessions older than X minutes.
     */
    public function scopeInactiveFor($query, $minutes)
    {
        return $query->where('last_activity_at', '<', now()->subMinutes($minutes));
    }

    /**
     * Scope a query to get sessions by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to get current session.
     */
    public function scopeCurrent($query)
    {
        return $query->where('session_id', session()->getId());
    }

    /**
     * Get the device type (mobile, tablet, desktop) from user agent.
     */
    public function getDeviceTypeAttribute(): string
    {
        if (empty($this->user_agent)) {
            return 'Unknown';
        }

        $userAgent = strtolower($this->user_agent);
        
        if (preg_match('/(android|iphone|ipod|windows phone|mobile)/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/(ipad|tablet)/i', $userAgent)) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    /**
     * Get the browser name from user agent.
     */
    public function getBrowserAttribute(): string
    {
        if (empty($this->user_agent)) {
            return 'Unknown';
        }

        $userAgent = $this->user_agent;
        $browsers = [
            'Chrome' => 'chrome',
            'Firefox' => 'firefox',
            'Safari' => 'safari',
            'Edge' => 'edge',
            'Opera' => 'opera|opr',
            'IE' => 'msie|trident',
        ];

        foreach ($browsers as $name => $pattern) {
            if (preg_match("/{$pattern}/i", $userAgent)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Get the operating system from user agent.
     */
    public function getOsAttribute(): string
    {
        if (empty($this->user_agent)) {
            return 'Unknown';
        }

        $userAgent = strtolower($this->user_agent);
        $oses = [
            'Windows' => 'windows nt',
            'macOS' => 'mac os x',
            'Linux' => 'linux',
            'Android' => 'android',
            'iOS' => 'iphone|ipad|ipod',
            'Chrome OS' => 'cros',
        ];

        foreach ($oses as $name => $pattern) {
            if (preg_match("/{$pattern}/i", $userAgent)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    /**
     * Get the session duration in human readable format.
     */
    public function getDurationAttribute(): ?string
    {
        if (!$this->login_at) {
            return null;
        }

        $end = $this->logout_at ?? now();
        $minutes = $this->login_at->diffInMinutes($end);
        
        if ($minutes < 1) {
            return 'Less than a minute';
        }
        
        if ($minutes < 60) {
            return $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes');
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        $duration = $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
        
        if ($remainingMinutes > 0) {
            $duration .= ' ' . $remainingMinutes . ' ' . ($remainingMinutes === 1 ? 'minute' : 'minutes');
        }
        
        return $duration;
    }

    /**
     * Check if the session is expired.
     */
    public function isExpired(int $timeoutMinutes = 30): bool
    {
        if (!$this->is_active) {
            return true;
        }

        if (!$this->last_activity_at) {
            return false;
        }

        return $this->last_activity_at->diffInMinutes(now()) > $timeoutMinutes;
    }

    /**
     * Mark session as logged out.
     */
    public function logout(): bool
    {
        return $this->update([
            'is_active' => false,
            'logout_at' => now(),
        ]);
    }

    /**
     * Update last activity timestamp.
     */
    public function updateActivity(): bool
    {
        return $this->update([
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Get the location from IP (if you have a service for this).
     */
    public function getLocationAttribute(): ?string
    {
        // You can integrate with a geolocation service here
        // For now, return null or a default value
        return null;
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($session) {
            if (empty($session->login_at)) {
                $session->login_at = now();
            }
            if (empty($session->last_activity_at)) {
                $session->last_activity_at = now();
            }
        });

        static::updating(function ($session) {
            // If session becomes inactive but no logout_at set, set it now
            if ($session->isDirty('is_active') && !$session->is_active && empty($session->logout_at)) {
                $session->logout_at = now();
            }
        });
    }
}