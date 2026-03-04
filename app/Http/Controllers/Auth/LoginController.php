<?php

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
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ALWAYS LOG THE ATTEMPT FIRST
        $attempt = LoginAttempt::create([
            'username_attempted' => $request->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'was_successful' => false, // Default to false, will update if successful
        ]);

        // Check for too many failed attempts from this IP
        $failedAttempts = LoginAttempt::where('ip_address', $request->ip())
            ->where('was_successful', false)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        
        if ($failedAttempts > 5) { // Changed from >=5 to >5 to allow this attempt
            // Update the attempt with failure reason
            $attempt->update([
                'failure_reason' => 'too_many_attempts'
            ]);
            
            return response()->json([
                'error' => 'Too many login attempts. Please try again later.'
            ], 429);
        }

        // Attempt login
        if (Auth::attempt($request->only('username', 'password'))) {
            $user = Auth::user();
            
            // UPDATE the attempt as successful
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
            
            return response()->json([
                'message' => 'Logged in successfully',
                'user' => $user
            ], 200);
        }

        $user = User::where('username', $request->username)->first();
        $attempt->update([
            'user_id' => $user->id ?? null,
            'failure_reason' => $user ? 'invalid_password' : 'user_not_found',
        ]);

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
        
            $user->endSession(session()->getId());
        
            $user->logActivity(
                'logout',
                'User logged out',
                ['method' => $request->method()]
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }
}