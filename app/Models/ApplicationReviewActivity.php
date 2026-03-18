<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationReviewActivity extends Model
{
    use HasFactory;

    protected $table = 'application_review_activities';

    protected $fillable = [
        'application_id',
        'reviewer_id',
        'action',
        'old_status',
        'new_status',
        'remarks',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the application that owns the activity
     */
    public function application()
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }

    /**
     * Get the reviewer who performed the action
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Scope a query to only include activities for a specific application
     */
    public function scopeForApplication($query, $applicationId)
    {
        return $query->where('application_id', $applicationId);
    }

    /**
     * Scope a query to only include activities by a specific reviewer
     */
    public function scopeByReviewer($query, $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }

    /**
     * Scope a query to only include activities with a specific action
     */
    public function scopeWithAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include status change activities
     */
    public function scopeStatusChanges($query)
    {
        return $query->whereNotNull('old_status')->whereNotNull('new_status');
    }

    /**
     * Scope a query to only include today's activities
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope a query to only include this week's activities
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope a query to only include this month's activities
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    /**
     * Get the action display text
     */
    public function getActionDisplayAttribute()
    {
        return match($this->action) {
            'status_updated' => 'Status Updated',
            'note_added' => 'Note Added',
            'document_verified' => 'Documents Verified',
            'document_rejected' => 'Documents Rejected',
            'hard_copy_received' => 'Hard Copy Received',
            'application_created' => 'Application Created',
            'application_submitted' => 'Application Submitted',
            'application_deleted' => 'Application Deleted',
            'application_updated' => 'Application Updated',
            'assigned_to_staff' => 'Assigned to Staff',
            'review_started' => 'Review Started',
            'review_completed' => 'Review Completed',
            'returned_for_revision' => 'Returned for Revision',
            'forwarded_to_engineer' => 'Forwarded to Engineer',
            'forwarded_to_building_official' => 'Forwarded to Building Official',
            'payment_verified' => 'Payment Verified',
            'payment_rejected' => 'Payment Rejected',
            'certificate_generated' => 'Certificate Generated',
            'certificate_released' => 'Certificate Released',
            default => ucfirst(str_replace('_', ' ', $this->action))
        };
    }

    /**
     * Get the action icon class
     */
    public function getActionIconAttribute()
    {
        return match($this->action) {
            'status_updated' => 'text-blue-600',
            'note_added' => 'text-yellow-600',
            'document_verified' => 'text-green-600',
            'document_rejected' => 'text-red-600',
            'hard_copy_received' => 'text-purple-600',
            'application_created' => 'text-emerald-600',
            'application_submitted' => 'text-indigo-600',
            'application_deleted' => 'text-red-600',
            'application_updated' => 'text-gray-600',
            'assigned_to_staff' => 'text-orange-600',
            'review_started' => 'text-cyan-600',
            'review_completed' => 'text-teal-600',
            'returned_for_revision' => 'text-amber-600',
            'forwarded_to_engineer' => 'text-violet-600',
            'forwarded_to_building_official' => 'text-fuchsia-600',
            'payment_verified' => 'text-lime-600',
            'payment_rejected' => 'text-rose-600',
            'certificate_generated' => 'text-pink-600',
            'certificate_released' => 'text-sky-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Get the action icon SVG path (for use with Heroicons)
     */
    public function getActionIconPathAttribute()
    {
        return match($this->action) {
            'status_updated' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'note_added' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'document_verified' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'document_rejected' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            'hard_copy_received' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'application_created' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
            'application_submitted' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'application_deleted' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            'assigned_to_staff' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        };
    }

    /**
     * Get the CSS class for the old status
     */
    public function getOldStatusClassAttribute()
    {
        return $this->getStatusClass($this->old_status);
    }

    /**
     * Get the CSS class for the new status
     */
    public function getNewStatusClassAttribute()
    {
        return $this->getStatusClass($this->new_status);
    }

    /**
     * Get status CSS class helper
     */
    private function getStatusClass($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'verified' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'draft' => 'bg-gray-100 text-gray-800',
            'under-review' => 'bg-purple-100 text-purple-800',
            'document-verification' => 'bg-indigo-100 text-indigo-800',
            'approved' => 'bg-emerald-100 text-emerald-800',
            'for-release' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get the formatted created date
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y h:i A') : null;
    }

    /**
     * Get the time ago (e.g., "2 hours ago")
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    /**
     * Get the reviewer name
     */
    public function getReviewerNameAttribute()
    {
        return $this->reviewer ? $this->reviewer->first_name . ' ' . $this->reviewer->last_name : 'System';
    }

    /**
     * Get the reviewer initials
     */
    public function getReviewerInitialsAttribute()
    {
        if (!$this->reviewer) return 'SYS';
        
        $first = substr($this->reviewer->first_name, 0, 1);
        $last = substr($this->reviewer->last_name, 0, 1);
        
        return strtoupper($first . $last);
    }

    /**
     * Get the reviewer role
     */
    public function getReviewerRoleAttribute()
    {
        return $this->reviewer ? ucfirst($this->reviewer->role) : 'System';
    }

    /**
     * Get the reviewer avatar color
     */
    public function getReviewerAvatarColorAttribute()
    {
        if (!$this->reviewer) return 'bg-gray-500';
        
        return match($this->reviewer->role) {
            'admin' => 'bg-purple-500',
            'staff' => 'bg-blue-500',
            default => 'bg-gray-500'
        };
    }

    /**
     * Get a human-readable description of the activity
     */
    public function getDescriptionAttribute()
    {
        if ($this->old_status && $this->new_status && $this->old_status !== $this->new_status) {
            $oldStatus = str_replace('-', ' ', $this->old_status);
            $newStatus = str_replace('-', ' ', $this->new_status);
            
            if ($this->reviewer) {
                return "Status changed from {$oldStatus} to {$newStatus} by {$this->reviewer->first_name} {$this->reviewer->last_name}";
            } else {
                return "Status changed from {$oldStatus} to {$newStatus}";
            }
        }
        
        $actionDisplay = $this->action_display;
        
        if ($this->reviewer) {
            return "{$actionDisplay} by {$this->reviewer->first_name} {$this->reviewer->last_name}";
        }
        
        return $actionDisplay;
    }

    /**
     * Check if this is a status change activity
     */
    public function getIsStatusChangeAttribute()
    {
        return $this->old_status && $this->new_status && $this->old_status !== $this->new_status;
    }

    /**
     * Check if this activity was performed by a specific user
     */
    public function isPerformedBy($userId)
    {
        return $this->reviewer_id === $userId;
    }

    /**
     * Check if this activity is for a specific application
     */
    public function isForApplication($applicationId)
    {
        return $this->application_id === $applicationId;
    }

    /**
     * Get the IP address location (placeholder - requires additional service)
     */
    public function getIpLocationAttribute()
    {
        // This would require an IP geolocation service
        return null;
    }

    /**
     * Parse user agent to get browser and OS
     */
    public function getUserAgentInfoAttribute()
    {
        $userAgent = $this->user_agent;
        
        if (!$userAgent) {
            return ['browser' => 'Unknown', 'os' => 'Unknown'];
        }

        // Simple parsing - can be enhanced with a package like jenssegers/agent
        $browser = 'Unknown';
        $os = 'Unknown';

        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        }

        if (strpos($userAgent, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $os = 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $os = 'Android';
        } elseif (strpos($userAgent, 'iOS') !== false || strpos($userAgent, 'iPhone') !== false) {
            $os = 'iOS';
        }

        return [
            'browser' => $browser,
            'os' => $os,
            'full' => $userAgent
        ];
    }

    /**
     * Log a new activity
     */
    public static function log($applicationId, $reviewerId, $action, $oldStatus = null, $newStatus = null, $remarks = null)
    {
        try {
            return self::create([
                'application_id' => $applicationId,
                'reviewer_id' => $reviewerId,
                'action' => $action,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'remarks' => $remarks,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to log activity: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get recent activities for dashboard
     */
    public static function getRecentActivities($limit = 10)
    {
        return self::with(['application.user', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities for a specific application with eager loading
     */
    public static function getForApplication($applicationId)
    {
        return self::with('reviewer')
            ->where('application_id', $applicationId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get status change history for an application
     */
    public static function getStatusHistory($applicationId)
    {
        return self::where('application_id', $applicationId)
            ->whereNotNull('old_status')
            ->whereNotNull('new_status')
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Count activities by action type
     */
    public static function countByAction($action)
    {
        return self::where('action', $action)->count();
    }

    /**
     * Get activity statistics
     */
    public static function getStats($days = 30)
    {
        $startDate = now()->subDays($days);
        
        return [
            'total' => self::where('created_at', '>=', $startDate)->count(),
            'by_action' => self::where('created_at', '>=', $startDate)
                ->selectRaw('action, count(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
            'by_reviewer' => self::where('created_at', '>=', $startDate)
                ->with('reviewer')
                ->get()
                ->groupBy(function($item) {
                    return $item->reviewer ? $item->reviewer->first_name . ' ' . $item->reviewer->last_name : 'System';
                })
                ->map(function($group) {
                    return $group->count();
                })
                ->toArray(),
            'daily' => self::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, count(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray()
        ];
    }
}