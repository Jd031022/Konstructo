<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $table = 'user_sessions';

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

    protected $casts = [
        'device_info' => 'array',
        'login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'logout_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get active sessions
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get sessions older than X minutes
    public function scopeInactiveFor($query, $minutes)
    {
        return $query->where('last_activity_at', '<', now()->subMinutes($minutes));
    }
}