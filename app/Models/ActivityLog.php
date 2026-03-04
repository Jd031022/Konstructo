<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for today's logs
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Scope for specific IP
    public function scopeFromIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    // Scope for specific action
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }
}