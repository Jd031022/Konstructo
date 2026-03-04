<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Process the request first
        $response = $next($request);

        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = session()->getId();
            
            
        }

        return $response;
    }
}