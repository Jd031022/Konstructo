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
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Support both JSON and form submissions
        $isJson = $request->wantsJson() || $request->ajax();
        
        // Validate based on input type (support both 'email' and 'login' fields)
        $loginField = $request->has('email') ? 'email' : ($request->has('login') ? 'login' : null);
        
        if (!$loginField) {
            if ($isJson) {
                return response()->json(['error' => 'Email or login field is required'], 422);
            }
            return back()->withErrors(['email' => 'Email is required']);
        }
        
        $loginValue = $request->input($loginField);
        $password = $request->password;
        
        // Determine if the login input is an email or username
        $loginType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        // Use the correct field name for authentication
        $credentials = [
            $loginType => $loginValue,
            'password' => $password
        ];
        
        // ========== FIXED RATE LIMITING - TRACK BY IP + USERNAME ==========
        // Clear old failed attempts for this IP + Username combination (older than 3 minutes)
        try {
            LoginAttempt::where('ip_address', $request->ip())
                ->where('username_attempted', $loginValue)
                ->where('was_successful', false)
                ->where('created_at', '<', now()->subMinutes(3))
                ->delete();
        } catch (\Exception $e) {
            // Skip if table doesn't exist
        }
        
        // Check for too many failed attempts for this SPECIFIC username from this IP
        try {
            $failedAttempts = LoginAttempt::where('ip_address', $request->ip())
                ->where('username_attempted', $loginValue)
                ->where('was_successful', false)
                ->where('created_at', '>=', now()->subMinutes(3))
                ->count();
            
            // BLOCK if 3 or more attempts for the SAME username in 3 minutes
            if ($failedAttempts >= 3) {
                // Get the oldest attempt to calculate when they can retry
                $oldestAttempt = LoginAttempt::where('ip_address', $request->ip())
                    ->where('username_attempted', $loginValue)
                    ->where('was_successful', false)
                    ->where('created_at', '>=', now()->subMinutes(3))
                    ->orderBy('created_at', 'asc')
                    ->first();
                
                if ($oldestAttempt) {
                    // Calculate when the block will lift (3 minutes from first failed attempt)
                    $unlockTime = $oldestAttempt->created_at->addMinutes(3);
                    $remainingSeconds = now()->diffInSeconds($unlockTime, false);
                    
                    // If still blocked
                    if ($remainingSeconds > 0) {
                        $remainingMinutes = ceil($remainingSeconds / 60);
                        
                        $this->logActivity(null, 'login', 'Too many failed login attempts for this account', [
                            'login_attempted' => $loginValue,
                            'login_type' => $loginType,
                            'failure_reason' => 'too_many_attempts',
                            'attempt_count' => $failedAttempts,
                            'ip_address' => $request->ip(),
                            'retry_after_seconds' => $remainingSeconds,
                            'unlock_at' => $unlockTime->toDateTimeString()
                        ], $request, 'failed');
                        
                        $errorMessage = "Too many failed attempts for this account. Please try again in {$remainingSeconds} seconds.";
                        
                        if ($isJson) {
                            return response()->json([
                                'error' => $errorMessage,
                                'retry_after' => $remainingSeconds,
                                'retry_after_seconds' => $remainingSeconds,
                                'unlock_at' => $unlockTime->toDateTimeString()
                            ], 429)->header('Retry-After', $remainingSeconds);
                        }
                        return back()->withErrors(['email' => $errorMessage])->onlyInput('email');
                    }
                }
                
                // If we get here, the time has passed - clear old attempts
                LoginAttempt::where('ip_address', $request->ip())
                    ->where('username_attempted', $loginValue)
                    ->where('was_successful', false)
                    ->where('created_at', '<', now()->subMinutes(3))
                    ->delete();
            }
        } catch (\Exception $e) {
            // Skip rate limiting if table doesn't exist
            \Log::warning('Rate limiting check failed: ' . $e->getMessage());
        }
        
        // ========== GLOBAL RATE LIMIT (Prevents DOS attacks) ==========
        // Optional: Add a higher global limit per IP to prevent abuse
        try {
            $globalAttempts = LoginAttempt::where('ip_address', $request->ip())
                ->where('was_successful', false)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->count();
            
            // Global block if too many attempts from same IP across different accounts (20 attempts in 10 minutes)
            if ($globalAttempts >= 20) {
                $this->logActivity(null, 'login', 'Global rate limit exceeded', [
                    'ip_address' => $request->ip(),
                    'attempt_count' => $globalAttempts
                ], $request, 'failed');
                
                if ($isJson) {
                    return response()->json([
                        'error' => 'Too many login attempts from this IP. Please try again in 10 minutes.'
                    ], 429);
                }
                return back()->withErrors(['email' => 'Too many login attempts. Please try again in 10 minutes.'])->onlyInput('email');
            }
        } catch (\Exception $e) {
            // Skip global rate limiting if table doesn't exist
        }
        // ========== END RATE LIMITING FIX ==========
        
        // Log the attempt in login_attempts table
        try {
            $attempt = LoginAttempt::create([
                'username_attempted' => $loginValue,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'was_successful' => false,
            ]);
        } catch (\Exception $e) {
            $attempt = null;
        }
        
        // Attempt login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Update attempt as successful
            if ($attempt) {
                try {
                    $attempt->update([
                        'user_id' => $user->id,
                        'was_successful' => true,
                        'failure_reason' => null,
                    ]);
                } catch (\Exception $e) {}
            }
            
            // Clear all failed attempts for this IP AND this username on successful login
            try {
                LoginAttempt::where('ip_address', $request->ip())
                    ->where('username_attempted', $loginValue)
                    ->where('was_successful', false)
                    ->delete();
            } catch (\Exception $e) {}
            
            // CHECK IF USER CAN LOGIN
            if (!$this->canLogin($user)) {
                Auth::logout();
                
                $redirectUrl = null;
                $errorMessage = $this->getLoginBlockMessage($user, $redirectUrl);
                
                $this->logActivity($user->id, 'login', 'Login blocked: ' . $errorMessage, [
                    'login_type' => $loginType,
                    'user_role' => $user->role,
                    'approval_status' => $user->approval_status,
                    'email_verified' => !is_null($user->email_verified_at),
                ], $request, 'failed');
                
                if ($isJson) {
                    return response()->json([
                        'error' => $errorMessage,
                        'redirect' => $redirectUrl
                    ], 403);
                }
                
                if ($redirectUrl) {
                    return redirect()->to($redirectUrl)->with('error', $errorMessage);
                }
                
                return back()->withErrors(['email' => $errorMessage]);
            }
            
            // Log successful login
            $this->logActivity($user->id, 'login', 'User logged in successfully', [
                'login_type' => $loginType,
                'user_role' => $user->role,
            ], $request, 'success');
            
            // Dispatch event
            try {
                event(new UserLoggedIn($user, $request->ip(), $request->userAgent()));
            } catch (\Exception $e) {}
            
            $request->session()->regenerate();
            
            // Update last login timestamp
            try {
                $user->updateLastLogin();
            } catch (\Exception $e) {
                $user->last_login_at = now();
                $user->save();
            }
            
            // Determine redirect based on role
            $redirect = match($user->role) {
                'admin' => route('admin.dashboard', absolute: false),
                'staff' => route('staff.dashboard', absolute: false),
                default => route('applicant.dashboard', absolute: false),
            };
            
            if ($isJson) {
                return response()->json([
                    'message' => 'Logged in successfully',
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'role' => $user->role,
                        'name' => $user->first_name . ' ' . $user->last_name
                    ],
                    'redirect' => $redirect,
                ], 200);
            }
            
            return redirect()->intended($redirect);
        }
        
        // Find user for better error message
        $user = User::where('email', $loginValue)
                    ->orWhere('username', $loginValue)
                    ->first();
        
        $failureReason = $user ? 'invalid_password' : 'user_not_found';
        
        if ($attempt) {
            try {
                $attempt->update([
                    'user_id' => $user->id ?? null,
                    'failure_reason' => $failureReason,
                ]);
            } catch (\Exception $e) {}
        }
        
        // Log the failed attempt
        $this->logActivity($user->id ?? null, 'login', $user ? 'Invalid password provided' : 'User account not found', [
            'login_attempted' => $loginValue,
            'login_type' => $loginType,
            'failure_reason' => $failureReason,
        ], $request, 'failed');
        
        if ($isJson) {
            return response()->json(['error' => 'Invalid email/username or password'], 401);
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            $this->logActivity($user->id, 'logout', 'User logged out', [
                'method' => $request->method(),
                'session_id' => session()->getId(),
            ], $request, 'success');
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if ($request->wantsJson() || $request->ajax()) {
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
    
    /**
     * Helper method to check if user can login
     */
    private function canLogin($user)
    {
        // Email must be verified for all users
        if (is_null($user->email_verified_at)) {
            return false;
        }
        
        // Admin and staff can login regardless of approval status
        if (in_array($user->role, ['admin', 'staff'])) {
            return true;
        }
        
        // For applicants, need approved status
        return $user->approval_status === 'approved';
    }
    
    /**
     * Get login block message
     */
    private function getLoginBlockMessage($user, &$redirectUrl)
    {
        $redirectUrl = null;
        
        if (is_null($user->email_verified_at)) {
            return 'Please verify your email address before logging in.';
        }
        
        if ($user->role === 'applicant') {
            if ($user->approval_status === 'pending') {
                $redirectUrl = route('applicant.account-status', absolute: false);
                return 'Your account is pending admin approval. You will be notified once approved.';
            }
            
            if ($user->approval_status === 'rejected') {
                $redirectUrl = route('applicant.account-status', absolute: false);
                return 'Your account application has been rejected. Please contact support for more information.';
            }
        }
        
        return 'Your account cannot be accessed at this time.';
    }
    
    /**
     * Helper method to log activities safely
     */
    private function logActivity($userId, $action, $description, $metadata, $request, $status)
    {
        try {
            ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'metadata' => json_encode($metadata),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            // Don't break the login flow
        }
    }
}