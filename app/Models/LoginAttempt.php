<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $table = 'login_attempts';

    protected $fillable = [
        'user_id',
        'username_attempted',
        'ip_address',
        'user_agent',
        'was_successful',
        'failure_reason',
    ];

    protected $casts = [
        'was_successful' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get failed attempts from last hour
    public function scopeFailedLastHour($query, $ip)
    {
        return $query->where('ip_address', $ip)
            ->where('was_successful', false)
            ->where('created_at', '>=', now()->subHour());
    }
}