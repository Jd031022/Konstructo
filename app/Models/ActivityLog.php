<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get the user that owns the activity log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted created_at date
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('M d, Y h:i A');
    }

    /**
     * Get human readable created_at
     */
    public function getHumanCreatedAtAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get user's full name or 'Unknown User'
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->first_name . ' ' . $this->user->last_name : 'Unknown User';
    }

    /**
     * Get user's initials for avatar
     */
    public function getUserInitialsAttribute(): string
    {
        if (!$this->user) {
            return 'UN';
        }
        return strtoupper(substr($this->user->first_name, 0, 1) . substr($this->user->last_name, 0, 1));
    }

    /**
     * Get action color class for badge
     */
    public function getActionColorAttribute(): string
    {
        $colors = [
            'login' => 'bg-green-100 text-green-600',
            'logout' => 'bg-gray-100 text-gray-600',
            'create' => 'bg-blue-100 text-blue-600',
            'update' => 'bg-yellow-100 text-yellow-600',
            'delete' => 'bg-red-100 text-red-600',
            'export' => 'bg-purple-100 text-purple-600',
            'settings' => 'bg-orange-100 text-orange-600',
            'test' => 'bg-pink-100 text-pink-600',
        ];
        
        return $colors[$this->action] ?? 'bg-gray-100 text-gray-600';
    }

    /**
     * Get status color class for badge
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status === 'success' 
            ? 'bg-green-100 text-green-600' 
            : 'bg-red-100 text-red-600';
    }

    // ========== SCOPES ==========

    /**
     * Scope for today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for yesterday's logs
     */
    public function scopeYesterday($query)
    {
        return $query->whereDate('created_at', today()->subDay());
    }

    /**
     * Scope for this week's logs
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's logs
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for last 7 days
     */
    public function scopeLastDays($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific date range
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for specific IP address
     */
    public function scopeFromIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope for specific action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for multiple actions
     */
    public function scopeActions($query, array $actions)
    {
        return $query->whereIn('action', $actions);
    }

    /**
     * Scope for successful logs
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed logs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for unknown users (null user_id)
     */
    public function scopeUnknownUsers($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope for search across multiple fields
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('ip_address', 'like', "%{$searchTerm}%")
              ->orWhere('action', 'like', "%{$searchTerm}%")
              ->orWhere('status', 'like', "%{$searchTerm}%")
              ->orWhere('description', 'like', "%{$searchTerm}%")
              ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                  $userQuery->where('first_name', 'like', "%{$searchTerm}%")
                            ->orWhere('last_name', 'like', "%{$searchTerm}%")
                            ->orWhere('username', 'like', "%{$searchTerm}%")
                            ->orWhere('email', 'like', "%{$searchTerm}%");
              });
        });
    }

    // ========== STATIC METHODS ==========

    /**
     * Log an activity
     */
    public static function log($data)
    {
        return self::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'action' => $data['action'],
            'description' => $data['description'] ?? '',
            'metadata' => $data['metadata'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'status' => $data['status'] ?? 'success',
        ]);
    }

    /**
     * Quick log for successful actions
     */
    public static function logSuccess($action, $description = '', $metadata = null)
    {
        return self::log([
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'status' => 'success'
        ]);
    }

    /**
     * Quick log for failed actions
     */
    public static function logFailure($action, $description = '', $metadata = null)
    {
        return self::log([
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'status' => 'failed'
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public static function getStats($days = 7)
    {
        $startDate = now()->subDays($days);
        
        return [
            'total' => self::where('created_at', '>=', $startDate)->count(),
            'success' => self::where('created_at', '>=', $startDate)->success()->count(),
            'failed' => self::where('created_at', '>=', $startDate)->failed()->count(),
            'unique_users' => self::where('created_at', '>=', $startDate)
                                ->whereNotNull('user_id')
                                ->distinct('user_id')
                                ->count('user_id'),
            'unique_ips' => self::where('created_at', '>=', $startDate)
                               ->whereNotNull('ip_address')
                               ->distinct('ip_address')
                               ->count('ip_address'),
            'actions' => self::where('created_at', '>=', $startDate)
                            ->selectRaw('action, count(*) as count')
                            ->groupBy('action')
                            ->orderBy('count', 'desc')
                            ->limit(5)
                            ->get()
        ];
    }

    /**
     * Get daily trends for chart
     */
    public static function getDailyTrend($days = 7)
    {
        $trend = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayLogs = self::whereDate('created_at', $date);
            
            $trend[] = [
                'date' => now()->subDays($i)->format('M d'),
                'total' => (clone $dayLogs)->count(),
                'success' => (clone $dayLogs)->success()->count(),
                'failed' => (clone $dayLogs)->failed()->count(),
            ];
        }
        
        return $trend;
    }
}