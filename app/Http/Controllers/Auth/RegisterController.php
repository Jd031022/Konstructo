<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GmailService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache; // Add this for storing verification codes
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    protected $gmailService;

    // Inject GmailService via constructor
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

        // Send verification email with personalized first name
        try {
            $emailSent = $this->gmailService->sendVerificationEmail(
                $request->email, 
                $verificationCode,
                $request->first_name // Pass the first name for personalization
            );
            
            if (!$emailSent) {
                // Log but don't stop registration - you can retry later
                Log::warning('Verification email failed to send for: ' . $request->email);
            }
        } catch (\Exception $e) {
            Log::error('Email sending error: ' . $e->getMessage());
            // Don't throw - user can still register, maybe resend later
        }

        // Create user - but DON'T auto-verify email
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
            // 'email_verified_at' => null, // This is the default, so you can remove this line
        ]);

        // Log the registration attempt
        $user->logLoginAttempt($request->username, true);

        // Return response with verification info
        return response()->json([
            'message' => 'Registration successful. Please check your email for verification code.',
            'requires_verification' => true,
            'email' => $request->email, // Send back so frontend knows which email to verify
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'username'])
        ], 201);
    }

    /**
     * Verify the email with code
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the stored code from cache
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

        // Find user and verify email
        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();

        // Clear the verification code from cache
        Cache::forget('verification_' . $request->email);

        return response()->json([
            'message' => 'Email verified successfully!',
            'verified' => true
        ]);
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Check if already verified
        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified.'
            ], 400);
        }

        // Generate new code
        $newCode = rand(100000, 999999);
        Cache::put('verification_' . $request->email, $newCode, now()->addMinutes(10));

        // Send new code
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