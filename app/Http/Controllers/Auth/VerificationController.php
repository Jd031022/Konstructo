<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VerificationController extends Controller
{
    protected $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // Generate 6-digit code
        $code = rand(100000, 999999);
        
        // Store code in cache for 10 minutes
        Cache::put('verification_' . $request->email, $code, now()->addMinutes(10));
        
        // Send email
        $sent = $this->gmailService->sendVerificationEmail($request->email, $code);
        
        if ($sent) {
            return response()->json(['message' => 'Verification code sent']);
        }
        
        return response()->json(['error' => 'Failed to send code'], 500);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);
        
        $cachedCode = Cache::get('verification_' . $request->email);
        
        if (!$cachedCode) {
            return response()->json(['error' => 'Code expired or not found'], 400);
        }
        
        if ($cachedCode != $request->code) {
            return response()->json(['error' => 'Invalid code'], 400);
        }
        
        // Code is valid - proceed with registration
        Cache::forget('verification_' . $request->email);
        
        return response()->json(['message' => 'Email verified successfully']);
    }
}