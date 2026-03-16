<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Events\UserLoggedIn;
use App\Models\LoginAttempt;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Determine if the login input is an email or username
        $login = $request->login;
        $loginType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Log the attempt in login_attempts table
        $attempt = LoginAttempt::create([
            'username_attempted' => $login,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'was_successful' => false,
        ]);

        // Check for too many failed attempts
        $failedAttempts = LoginAttempt::where('ip_address', $request->ip())
            ->where('was_successful', false)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        
        if ($failedAttempts > 5) {
            $attempt->update([
                'failure_reason' => 'too_many_attempts'
            ]);
            
            // Log the failed attempt in activity_logs
            ActivityLog::create([
                'user_id' => null,
                'action' => 'login',
                'description' => 'Too many failed login attempts',
                'metadata' => json_encode([
                    'login_attempted' => $login,
                    'login_type' => $loginType,
                    'failure_reason' => 'too_many_attempts',
                    'attempt_count' => $failedAttempts,
                    'ip_address' => $request->ip()
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'failed'
            ]);
            
            return response()->json([
                'error' => 'Too many login attempts. Please try again later.'
            ], 429);
        }

        // Attempt login with either email or username
        if (Auth::attempt([$loginType => $login, 'password' => $request->password])) {
            $user = Auth::user();
            
            // Update attempt as successful
            $attempt->update([
                'user_id' => $user->id,
                'was_successful' => true,
                'failure_reason' => null,
            ]);
            
            // Log successful login in activity_logs
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => 'User logged in successfully',
                'metadata' => json_encode([
                    'login_type' => $loginType,
                    'login_value' => $login,
                    'user_role' => $user->role,
                    'ip_address' => $request->ip()
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success'
            ]);
            
            // Dispatch event for logging
            event(new UserLoggedIn(
                $user,
                $request->ip(),
                $request->userAgent()
            ));
            
            $request->session()->regenerate();
            
            // Check if user is staff and needs to set position
            $needsPosition = false;
            if ($user->role === 'staff') {
                $profile = $user->profile;
                $needsPosition = !$profile || !$profile->position;
            }
            
            // Determine redirect based on role
            $redirect = match($user->role) {
                'admin' => route('admin.dashboard'),
                'staff' => route('staff.dashboard'),
                default => route('applicant.dashboard'),
            };
            
            return response()->json([
                'message' => 'Logged in successfully',
                'user' => $user,
                'redirect' => $redirect,
                'needs_position' => $needsPosition
            ], 200);
        }

        // Find user for better error message
        $user = User::where('email', $login)
                    ->orWhere('username', $login)
                    ->first();
        
        $failureReason = $user ? 'invalid_password' : 'user_not_found';
        
        $attempt->update([
            'user_id' => $user->id ?? null,
            'failure_reason' => $failureReason,
        ]);
        
        // Log the failed attempt
        ActivityLog::create([
            'user_id' => $user->id ?? null,
            'action' => 'login',
            'description' => $user ? 'Invalid password provided' : 'User account not found',
            'metadata' => json_encode([
                'login_attempted' => $login,
                'login_type' => $loginType,
                'failure_reason' => $failureReason,
                'user_exists' => $user ? true : false,
                'ip_address' => $request->ip()
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'failed'
        ]);

        return response()->json(['error' => 'Invalid email/username or password'], 401);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Log logout activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'description' => 'User logged out',
                'metadata' => json_encode([
                    'method' => $request->method(),
                    'session_id' => session()->getId(),
                    'ip_address' => $request->ip()
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success'
            ]);
        
            if (method_exists($user, 'sessions')) {
                $user->sessions()->where('session_id', session()->getId())->update(['is_active' => false]);
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Logged out successfully',
                'clear_storage' => true
            ]);
        }
        
        return redirect()->route('login')->with('clear_storage', true);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
}