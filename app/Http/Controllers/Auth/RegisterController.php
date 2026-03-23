<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GmailService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    protected $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'suffix' => 'nullable|string|max:50',
            'phone_number' => 'required|string|size:11|regex:/^09\d{9}$/|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'zip_code' => 'required|string|max:10',
            'address' => 'required|string',
            'username' => 'required|string|max:100|unique:users|alpha_dash',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
        ], [
            'phone_number.regex' => 'Phone number must start with 09 and be 11 digits',
            'password.regex' => 'Password must contain at least 1 uppercase letter, 1 number, and 1 special character',
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate 6-digit verification code
        $verificationCode = rand(100000, 999999);
        
        // Store code in cache for 10 minutes
        Cache::put('verification_' . $request->email, $verificationCode, now()->addMinutes(10));

        // Send verification email
        try {
            $emailSent = $this->gmailService->sendVerificationEmail(
                $request->email, 
                $verificationCode,
                $request->first_name
            );
            
            if (!$emailSent) {
                Log::warning('Verification email failed to send for: ' . $request->email);
            }
        } catch (\Exception $e) {
            Log::error('Email sending error: ' . $e->getMessage());
        }

        // Create user - approval_status defaults to 'pending'
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'suffix' => $request->suffix,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'zip_code' => $request->zip_code,
            'address' => $request->address,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'applicant', // Always set role to applicant for registration
            'approval_status' => 'pending', // Explicitly set pending
        ]);

        // Log the registration attempt
        $user->logLoginAttempt($request->username, true);

        return response()->json([
            'message' => 'Registration successful. Please check your email for verification code.',
            'requires_verification' => true,
            'email' => $request->email,
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'username'])
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $storedCode = Cache::get('verification_' . $request->email);

        if (!$storedCode) {
            return response()->json([
                'message' => 'Verification code expired or not found. Please request a new one.'
            ], 400);
        }

        if ($storedCode != $request->code) {
            return response()->json([
                'message' => 'Invalid verification code.'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        
        // Check if email is already verified
        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified.'
            ], 400);
        }
        
        // Verify email
        $user->email_verified_at = now();
        $user->save();

        Cache::forget('verification_' . $request->email);

        // After email verification, send notification to admins about pending approval
        $this->notifyAdminsOfPendingApproval($user);

        // Log the user in automatically after verification
        Auth::login($user);

        return response()->json([
            'message' => 'Email verified successfully! Your account is now pending admin approval. You will be notified once approved.',
            'verified' => true,
            'requires_approval' => true,
            'redirect' => route('applicant.account-status')
        ]);
    }

    /**
     * Notify admins about new pending user approval
     */
    private function notifyAdminsOfPendingApproval(User $user)
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            // Create notification in database
            try {
                $admin->notify(new \App\Notifications\NewUserPendingApproval($user));
            } catch (\Exception $e) {
                Log::error('Failed to create database notification: ' . $e->getMessage());
            }
            
            // Optionally send email notification
            try {
                $this->gmailService->sendAdminNotification(
                    $admin->email,
                    'New User Pending Approval',
                    "User {$user->first_name} {$user->last_name} ({$user->email}) has registered and verified their email. Please review and approve their account.",
                    $user->first_name . ' ' . $user->last_name,
                    $user->email
                );
            } catch (\Exception $e) {
                Log::error('Failed to send admin notification email: ' . $e->getMessage());
            }
        }
    }

    public function resendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified.'
            ], 400);
        }

        $newCode = rand(100000, 999999);
        Cache::put('verification_' . $request->email, $newCode, now()->addMinutes(10));

        try {
            $this->gmailService->sendVerificationEmail(
                $request->email, 
                $newCode,
                $user->first_name
            );
            
            return response()->json([
                'message' => 'New verification code sent successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Resend verification failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
    }
}