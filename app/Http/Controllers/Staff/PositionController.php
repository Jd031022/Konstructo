<?php
// app/Http/Controllers/Staff/PositionController.php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|in:engineer,architect,BFP,cpdo,administrative_aide'  // Added cpdo here
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        if ($user->role !== 'staff') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update or create profile with position
        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['position' => $request->position]
        );

        // Log the position update
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'position_update',
            'description' => 'Staff updated their position',
            'metadata' => json_encode([
                'position' => $request->position,
                'ip_address' => $request->ip()
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success'
        ]);

        return response()->json([
            'message' => 'Position updated successfully',
            'position' => $request->position
        ]);
    }

    public function check()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'staff') {
            return response()->json(['needs_position' => false]);
        }

        $profile = $user->profile;
        $needsPosition = !$profile || !$profile->position;

        return response()->json([
            'needs_position' => $needsPosition
        ]);
    }
}