<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'engineers' => User::where('role', 'engineer')->count(),
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
            'role' => 'required|in:admin,engineer,applicant',
            'phone_number' => 'required|string|regex:/^09[0-9]{9}$/',
            'address' => 'required|string',
            'zip_code' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'email_verified_at' => now(), // Auto-verify admin-created accounts
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
            'role' => 'required|in:admin,engineer,applicant',
            'phone_number' => 'required|string|regex:/^09[0-9]{9}$/',
            'address' => 'required|string',
            'zip_code' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only([
            'first_name', 'last_name', 'middle_name', 'suffix',
            'email', 'username', 'role', 'phone_number', 'address', 'zip_code'
        ]));

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
        
        // Here you would typically send an email with the new password
        // For now, we'll return it in the response
        
        return response()->json([
            'message' => 'Password reset successfully',
            'new_password' => $newPassword
        ]);
    }

    /**
     * Get all users with their latest activity for the admin panel
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        try {
            // Get all users with their latest activity in a single query
            $users = User::select('id', 'first_name', 'last_name', 'middle_name', 'suffix', 'email', 'role', 'email_verified_at', 'created_at')
                ->with(['latestActivity' => function($query) {
                    $query->select('id', 'user_id', 'created_at');
                }])
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
                    
                    // Determine badge colors
                    $roleBadge = match($user->role) {
                        'admin' => 'purple',
                        'engineer' => 'blue',
                        'applicant' => 'gray',
                        default => 'gray'
                    };
                    
                    $statusBadge = $user->email_verified_at ? 'green' : 'yellow';
                    $status = $user->email_verified_at ? 'active' : 'inactive';
                    
                    // Get last activity from eager loaded relationship
                    $lastActive = 'Never';
                    if ($user->latestActivity && $user->latestActivity->created_at) {
                        $lastActive = $user->latestActivity->created_at->diffForHumans();
                    }
                    
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
                'engineers' => User::where('role', 'engineer')->count(),
                'applicants' => User::where('role', 'applicant')->count(),
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
            
            return response()->json([
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'middle_name' => $user->middle_name,
                'suffix' => $user->suffix,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
                'phone_number' => $user->phone_number,
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