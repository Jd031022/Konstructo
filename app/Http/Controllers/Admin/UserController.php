<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'applicants' => User::where('role', 'applicant')->count(),
            'active' => User::whereNotNull('email_verified_at')->count(),
        ];
        
        return view('admin.users', compact('users', 'stats'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|regex:/^[a-zA-Z0-9_-]+$/',
            'password' => 'required|string|min:8|max:16|confirmed',
            'role' => 'required|in:admin,staff,applicant',
            'phone_number' => ['required', 'string', 'regex:/^(09[0-9]{9}|[0-9]{10})$/'], // Fixed: Added delimiters
            'address' => 'required|string',
            'zip_code' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Format phone number to ensure it's 11 digits starting with 09
        $phoneNumber = $request->phone_number;
        
        // If it's 10 digits, add 09 prefix
        if (preg_match('/^[0-9]{10}$/', $phoneNumber)) {
            $phoneNumber = '09' . $phoneNumber;
        }
        
        // Ensure it's exactly 11 digits and starts with 09
        if (!preg_match('/^09[0-9]{9}$/', $phoneNumber)) {
            return response()->json(['errors' => ['phone_number' => ['Phone number must be 11 digits starting with 09']]], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'suffix' => $request->suffix,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone_number' => $phoneNumber,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|string|regex:/^[a-zA-Z0-9_-]+$/|unique:users,username,' . $id,
            'role' => 'required|in:admin,staff,applicant',
            'phone_number' => ['required', 'string', 'regex:/^(09[0-9]{9}|[0-9]{10})$/'], // Fixed: Added delimiters
            'address' => 'required|string',
            'zip_code' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Format phone number to ensure it's 11 digits starting with 09
        $phoneNumber = $request->phone_number;
        
        // If it's 10 digits, add 09 prefix
        if (preg_match('/^[0-9]{10}$/', $phoneNumber)) {
            $phoneNumber = '09' . $phoneNumber;
        }
        
        // Ensure it's exactly 11 digits and starts with 09
        if (!preg_match('/^09[0-9]{9}$/', $phoneNumber)) {
            return response()->json(['errors' => ['phone_number' => ['Phone number must be 11 digits starting with 09']]], 422);
        }

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'suffix' => $request->suffix,
            'email' => $request->email,
            'username' => $request->username,
            'role' => $request->role,
            'phone_number' => $phoneNumber,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'You cannot delete your own account'], 403);
        }
        
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->email_verified_at) {
            $user->email_verified_at = null;
            $message = 'User deactivated successfully';
        } else {
            $user->email_verified_at = now();
            $message = 'User activated successfully';
        }
        
        $user->save();
        
        return response()->json(['message' => $message]);
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        // Generate a random password
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->save();
        
        return response()->json([
            'message' => 'Password reset successfully',
            'new_password' => $newPassword
        ]);
    }

    /**
     * Get all users with their latest activity from activity_logs table
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        try {
            // Get all users
            $users = User::select('id', 'first_name', 'last_name', 'middle_name', 'suffix', 'email', 'role', 'email_verified_at', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    // Get full name
                    $fullName = trim($user->first_name . ' ' . ($user->middle_name ?? '') . ' ' . $user->last_name . ' ' . ($user->suffix ?? ''));
                    if (empty($fullName)) {
                        $fullName = $user->email;
                    }
                    
                    // Get initials
                    $firstInitial = !empty($user->first_name) ? strtoupper(substr($user->first_name, 0, 1)) : '';
                    $lastInitial = !empty($user->last_name) ? strtoupper(substr($user->last_name, 0, 1)) : '';
                    $initials = $firstInitial . $lastInitial;
                    if (empty(trim($initials))) {
                        $initials = 'U';
                    }
                    
                    // Get the latest activity for this user from activity_logs table
                    $latestActivity = ActivityLog::where('user_id', $user->id)
                        ->where('status', 'success')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    // Determine last active time
                    if ($latestActivity) {
                        $lastActive = $latestActivity->created_at->diffForHumans();
                    } else {
                        $lastActive = 'Never';
                    }
                    
                    // Determine badge colors
                    $roleBadge = match($user->role) {
                        'admin' => 'purple',
                        'staff' => 'blue',
                        'applicant' => 'gray',
                        default => 'gray'
                    };
                    
                    $statusBadge = $user->email_verified_at ? 'green' : 'yellow';
                    $status = $user->email_verified_at ? 'active' : 'inactive';
                    
                    return [
                        'id' => $user->id,
                        'name' => $fullName,
                        'initials' => $initials,
                        'email' => $user->email,
                        'role' => $user->role,
                        'role_badge' => $roleBadge,
                        'status' => $status,
                        'status_badge' => $statusBadge,
                        'last_active' => $lastActive,
                        'created_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : null,
                    ];
                });
            
            // Calculate statistics
            $stats = [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'staff' => User::where('role', 'staff')->count(),
                'applicants' => User::where('role', 'applicant')->count(),
                'active' => User::whereNotNull('email_verified_at')->count(),
            ];
            
            return response()->json([
                'users' => $users,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getUsers: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to load users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single user by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Format phone number for display (remove 09 prefix if needed)
            $phoneNumber = $user->phone_number;
            // If it starts with 09 and is 11 digits, remove the 09 for display
            if (substr($phoneNumber, 0, 2) === '09' && strlen($phoneNumber) === 11) {
                $phoneNumber = substr($phoneNumber, 2);
            }
            
            return response()->json([
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'middle_name' => $user->middle_name,
                'suffix' => $user->suffix,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
                'phone_number' => $phoneNumber,
                'address' => $user->address,
                'zip_code' => $user->zip_code,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getUser: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to load user: ' . $e->getMessage()
            ], 500);
        }
    }
}