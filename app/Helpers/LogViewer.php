<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use App\Models\LoginAttempt;
use App\Models\UserSession;

class LogViewer
{
    public static function getUserActivity($userId, $days = 7)
    {
        return ActivityLog::with('user')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getLoginAttempts($days = 7)
    {
        return LoginAttempt::with('user')
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getActiveSessions()
    {
        return UserSession::with('user')
            ->where('is_active', true)
            ->where('last_activity_at', '>=', now()->subMinutes(15))
            ->get();
    }

    public static function getSuspiciousActivity($threshold = 5)
    {
        return LoginAttempt::selectRaw('ip_address, count(*) as attempt_count')
            ->where('created_at', '>=', now()->subHours(24))
            ->where('was_successful', false)
            ->groupBy('ip_address')
            ->having('attempt_count', '>=', $threshold)
            ->get();
    }
}