<?php
// app/Http/Controllers/Staff/PositionController.php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PositionController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|in:engineer,architect,BFP,bfp,cpdo,administrative_aide,treasurer,assessor'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        if ($user->role !== 'staff') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update or create profile with position
        $profile = $user->userProfile()->updateOrCreate(
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
            'success' => true,
            'message' => 'Position updated successfully',
            'position' => $request->position
        ]);
    }

    public function check()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'position' => null,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            if ($user->role !== 'staff') {
                return response()->json([
                    'success' => true,
                    'position' => null,
                    'role' => $user->role,
                    'message' => 'User is not a staff member'
                ]);
            }

            // Load the userProfile relationship
            $user->load('userProfile');
            $profile = $user->userProfile;
            
            $position = $profile ? $profile->position : null;

            Log::info('Position check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'role' => $user->role,
                'has_profile' => $profile ? 'yes' : 'no',
                'position' => $position
            ]);

            return response()->json([
                'success' => true,
                'position' => $position,
                'has_profile' => $profile ? true : false,
                'user_id' => $user->id,
                'role' => $user->role
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking position: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'position' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get the current user's position (simplified version)
     */
    public function getPosition()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'position' => null
                ], 401);
            }
            
            $user->load('userProfile');
            $position = $user->userProfile ? $user->userProfile->position : null;
            
            return response()->json([
                'success' => true,
                'position' => $position
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting position: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'position' => null
            ], 500);
        }
    }
}