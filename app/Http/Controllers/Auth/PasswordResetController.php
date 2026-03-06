<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Send reset code to email
     */
    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Delete any existing unused codes for this email
        PasswordReset::where('email', $request->email)
            ->where('used', false)
            ->delete();
        
        // Create new reset record
        $passwordReset = PasswordReset::create([
            'email' => $request->email,
            'code' => $code,
            'expires_at' => now()->addMinutes(15), // Code expires in 15 minutes
        ]);

        // Send email with code
        try {
            Mail::send('emails.password-reset', ['code' => $code], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Password Reset Code - Konstructo');
                $message->from(env('EMAIL_USER'), 'Konstructo');
            });

            return response()->json([
                'message' => 'Reset code sent to your email',
                'email' => $request->email
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send email. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify the reset code
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reset = PasswordReset::where('email', $request->email)
            ->where('code', $request->code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            return response()->json([
                'error' => 'Invalid or expired code'
            ], 400);
        }

        return response()->json([
            'message' => 'Code verified successfully',
            'token' => encrypt($reset->id) // Send encrypted reset ID as token
        ], 200);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'token' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'confirmed',
                'regex:/[A-Z]/',      // at least one uppercase
                'regex:/[0-9]/',       // at least one number
                'regex:/[@$!%*?&]/'    // at least one special character
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character (@$!%*?&)',
            'password.min' => 'Password must be between 8 and 16 characters',
            'password.max' => 'Password must be between 8 and 16 characters',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify token
        try {
            $resetId = decrypt($request->token);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        // Find and verify the reset record
        $reset = PasswordReset::where('id', $resetId)
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            return response()->json([
                'error' => 'Invalid or expired reset request'
            ], 400);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        // Mark code as used
        $reset->used = true;
        $reset->save();

        // Log activity
        $user->logActivity(
            'password_reset',
            'Password reset successfully',
            ['method' => 'email_code']
        );

        return response()->json([
            'message' => 'Password reset successfully. You can now login with your new password.'
        ], 200);
    }

    /**
     * Resend reset code
     */
    public function resendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Delete old codes
        PasswordReset::where('email', $request->email)
            ->where('used', false)
            ->delete();

        // Generate new code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $passwordReset = PasswordReset::create([
            'email' => $request->email,
            'code' => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        // Send email
        try {
            Mail::send('emails.password-reset', ['code' => $code], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Password Reset Code - Konstructo');
                $message->from(env('EMAIL_USER'), 'Konstructo');
            });

            return response()->json([
                'message' => 'New reset code sent to your email'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send email. Please try again.'
            ], 500);
        }
    }
}