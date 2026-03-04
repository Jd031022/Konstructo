<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

trait LogActivity
{
    /**
     * Log user activity
     */
    public function logActivity($action, $description = null, $metadata = null, $status = 'success')
    {
        return ActivityLog::create([
            'user_id' => $this->id,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'status' => $status,
        ]);
    }

    /**
     * Log login attempt (successful or failed)
     */
    public function logLoginAttempt($username, $success, $reason = null)
    {
        return \App\Models\LoginAttempt::create([
            'user_id' => $success ? $this->id : null,
            'username_attempted' => $username,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'was_successful' => $success,
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Start user session
     */
    public function startSession($sessionId)
    {
        // End any existing active sessions
        \App\Models\UserSession::where('user_id', $this->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'logout_at' => now()]);

        // Create new session
        return \App\Models\UserSession::create([
            'user_id' => $this->id,
            'session_id' => $sessionId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'device_info' => json_encode([
                'browser' => Request::userAgent(),
                'platform' => php_uname('s'),
                'language' => Request::getPreferredLanguage(),
            ]),
            'login_at' => now(),
            'last_activity_at' => now(),
            'is_active' => true,
        ]);
    }

    /**
     * End user session
     */
    public function endSession($sessionId)
    {
        return \App\Models\UserSession::where('user_id', $this->id)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'logout_at' => now(),
            ]);
    }

    /**
     * Update last activity
     */
    public function updateLastActivity($sessionId)
    {
        return \App\Models\UserSession::where('user_id', $this->id)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->update(['last_activity_at' => now()]);
    }
}