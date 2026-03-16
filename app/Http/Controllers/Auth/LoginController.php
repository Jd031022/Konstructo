<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Events\UserLoggedIn;
use App\Models\LoginAttempt;
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

        // Log the attempt
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
            
            // Dispatch event for logging
            event(new UserLoggedIn(
                $user,
                $request->ip(),
                $request->userAgent()
            ));
            
            $request->session()->regenerate();
            
            // Determine redirect based on role
            $redirect = match($user->role) {
                'admin' => route('admin.dashboard'),
                'staff' => route('staff.dashboard'),
                default => route('applicant.dashboard'),
            };
            
            return response()->json([
                'message' => 'Logged in successfully',
                'user' => $user,
                'redirect' => $redirect
            ], 200);
        }

        // Find user for better error message (check both email and username)
        $user = User::where('email', $login)
                    ->orWhere('username', $login)
                    ->first();
                    
        $attempt->update([
            'user_id' => $user->id ?? null,
            'failure_reason' => $user ? 'invalid_password' : 'user_not_found',
        ]);

        return response()->json(['error' => 'Invalid email/username or password'], 401);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
        
            // Check if sessions relationship exists before using it
            if (method_exists($user, 'sessions')) {
                $user->sessions()->where('session_id', session()->getId())->update(['is_active' => false]);
            }
        
            // Check if logActivity method exists
            if (method_exists($user, 'logActivity')) {
                $user->logActivity(
                    'logout',
                    'User logged out',
                    ['method' => $request->method()]
                );
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Logged out successfully',
                'clear_storage' => true // Add flag to clear client-side storage
            ]);
        }
        
        // Add flash data to clear storage on next page load
        return redirect()->route('login')->with('clear_storage', true);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
}