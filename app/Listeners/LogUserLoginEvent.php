<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogUserLoginEvent implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(UserLoggedIn $event)
    {
        // Log the login activity
        $event->user->logActivity(
            'login',
            'User logged in successfully',
            [
                'ip' => $event->ipAddress,
                'user_agent' => $event->userAgent
            ]
        );

        // Log successful login attempt
        $event->user->logLoginAttempt(
            $event->user->username,
            true
        );

        // Start session tracking
        $event->user->startSession(session()->getId());
    }
}