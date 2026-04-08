<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\GmailService;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('profile')->orderBy('created_at', 'desc')->paginate(10);
        
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'applicants' => User::where('role', 'applicant')->count(),
            'active' => User::whereNotNull('email_verified_at')->count(),
        ];
        
        return view('admin.users', compact('users', 'stats'));
    }

    /**
     * Get user statistics for dashboard
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        try {
            $adminCount = User::where('role', 'admin')->count();
            $staffCount = User::where('role', 'staff')->count();
            $applicantCount = User::where('role', 'applicant')->count();
            $totalUsers = User::count();
            $activeUsers = User::whereNotNull('email_verified_at')->count();
            
            // Approval stats for applicants
            $pendingApplicants = User::where('role', 'applicant')
                ->where('approval_status', 'pending')
                ->count();
            $approvedApplicants = User::where('role', 'applicant')
                ->where('approval_status', 'approved')
                ->count();
            $rejectedApplicants = User::where('role', 'applicant')
                ->where('approval_status', 'rejected')
                ->count();
            
            return response()->json([
                'success' => true,
                'admin_count' => $adminCount,
                'staff_count' => $staffCount,
                'applicant_count' => $applicantCount,
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'pending_applicants' => $pendingApplicants,
                'approved_applicants' => $approvedApplicants,
                'rejected_applicants' => $rejectedApplicants
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getStats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading user stats'
            ], 500);
        }
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
            'phone_number' => ['required', 'string', 'regex:/^(09[0-9]{9}|[0-9]{10})$/'],
            'address' => 'required|string',
            'zip_code' => 'required|string|max:10',
            'position' => 'required_if:role,staff|nullable|in:engineer,architect,BFP,cpdo,administrative_aide'
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

        // Set approval status based on role
        $approvalStatus = ($request->role === 'applicant') ? 'pending' : 'approved';
        $emailVerifiedAt = ($request->role !== 'applicant') ? now() : null;

        // Store the plain password for email before hashing
        $plainPassword = $request->password;

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'suffix' => $request->suffix,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($plainPassword),
            'role' => $request->role,
            'phone_number' => $phoneNumber,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'email_verified_at' => $emailVerifiedAt,
            'approval_status' => $approvalStatus,
        ]);

        // Create profile for staff with position
        if ($request->role === 'staff' && $request->filled('position')) {
            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['position' => $request->position]
            );
        }

        // Send email with credentials using GmailService
        $emailSent = false;
        try {
            $gmailService = new GmailService();
            $fullName = trim($request->first_name . ' ' . $request->last_name);
            $emailSent = $gmailService->sendCredentialsEmail(
                $user->email,
                $fullName,
                $user->username,
                $plainPassword,
                false // not a reset
            );
            
            if ($emailSent) {
                Log::info('Credentials email sent successfully to: ' . $user->email);
            } else {
                Log::warning('Failed to send credentials email to: ' . $user->email);
            }
        } catch (\Exception $e) {
            Log::error('Exception sending credentials email: ' . $e->getMessage());
            $emailSent = false;
        }

        // Log the creation
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_user',
            'description' => "Created user: {$user->first_name} {$user->last_name}",
            'metadata' => json_encode([
                'user_id' => $user->id, 
                'role' => $user->role,
                'approval_status' => $user->approval_status,
                'position' => $request->position,
                'email_sent' => $emailSent
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success'
        ]);

        // Load profile for response
        $user->load('profile');

        $message = 'User created successfully';
        if ($emailSent) {
            $message .= ' Credentials have been sent to the user\'s email.';
        } else {
            $message .= ' However, failed to send email credentials. Please inform the user manually.';
        }

        return response()->json([
            'message' => $message,
            'user' => $user,
            'email_sent' => $emailSent
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
            'phone_number' => ['required', 'string', 'regex:/^(09[0-9]{9}|[0-9]{10})$/'],
            'address' => 'required|string',
            'zip_code' => 'required|string|max:10',
            'position' => 'required_if:role,staff|nullable|in:engineer,architect,BFP,cpdo,administrative_aide'
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

        $oldRole = $user->role;
        $newRole = $request->role;

        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'suffix' => $request->suffix,
            'email' => $request->email,
            'username' => $request->username,
            'role' => $newRole,
            'phone_number' => $phoneNumber,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
        ];

        // If role changed from applicant to admin/staff, auto-approve
        if ($oldRole === 'applicant' && in_array($newRole, ['admin', 'staff'])) {
            $updateData['approval_status'] = 'approved';
            $updateData['approved_at'] = now();
            $updateData['approved_by'] = auth()->id();
            $updateData['email_verified_at'] = now(); // Auto-verify email for admin/staff
        }

        // If role changed to applicant, set approval to pending
        if ($newRole === 'applicant' && $oldRole !== 'applicant') {
            $updateData['approval_status'] = 'pending';
            $updateData['approved_at'] = null;
            $updateData['approved_by'] = null;
        }

        $user->update($updateData);

        // Handle position for staff
        if ($newRole === 'staff') {
            if ($request->filled('position')) {
                UserProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    ['position' => $request->position]
                );
            }
        } else {
            // If user is no longer staff, remove position data
            if ($user->profile) {
                $user->profile->delete();
            }
        }

        // Log the update
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_user',
            'description' => "Updated user: {$user->first_name} {$user->last_name}",
            'metadata' => json_encode([
                'user_id' => $user->id, 
                'changes' => array_keys($request->all()),
                'old_role' => $oldRole,
                'new_role' => $newRole,
                'position' => $request->position
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success'
        ]);

        // Load profile for response
        $user->load('profile');

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
        
        $userName = $user->first_name . ' ' . $user->last_name;
        
        // Delete associated profile first (cascade should handle this, but explicit for safety)
        if ($user->profile) {
            $user->profile->delete();
        }
        
        $user->delete();
        
        // Log the deletion
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_user',
            'description' => "Deleted user: {$userName}",
            'metadata' => json_encode(['deleted_user_id' => $id]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success'
        ]);
        
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // For non-applicants, toggle email verification
        if (!$user->isApplicant()) {
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
        
        // For applicants, toggle approval status instead
        if ($user->isPending()) {
            $user->approve(auth()->id());
            $message = 'User approved successfully';
        } elseif ($user->isApproved()) {
            $user->reject(auth()->id(), 'Account status changed by admin');
            $message = 'User rejected successfully';
        } else {
            $user->approve(auth()->id());
            $message = 'User approved successfully';
        }
        
        return response()->json(['message' => $message]);
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        // Generate a random password
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->save();
        
        // Send email with new password using GmailService
        $emailSent = false;
        try {
            $gmailService = new GmailService();
            $fullName = trim($user->first_name . ' ' . $user->last_name);
            $emailSent = $gmailService->sendCredentialsEmail(
                $user->email,
                $fullName,
                $user->username,
                $newPassword,
                true // is reset
            );
            
            if ($emailSent) {
                Log::info('Password reset email sent successfully to: ' . $user->email);
            } else {
                Log::warning('Failed to send password reset email to: ' . $user->email);
            }
        } catch (\Exception $e) {
            Log::error('Exception sending password reset email: ' . $e->getMessage());
            $emailSent = false;
        }
        
        // Log the password reset
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reset_password',
            'description' => "Reset password for user: {$user->first_name} {$user->last_name}",
            'metadata' => json_encode([
                'user_id' => $user->id,
                'email_sent' => $emailSent
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success'
        ]);
        
        $message = 'Password reset successfully';
        if ($emailSent) {
            $message .= ' The new password has been sent to the user\'s email.';
        } else {
            $message .= ' However, failed to send email. Please provide the password manually: ' . $newPassword;
        }
        
        return response()->json([
            'message' => $message,
            'new_password' => $emailSent ? null : $newPassword,
            'email_sent' => $emailSent
        ]);
    }

    /**
     * Resend credentials to user
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendCredentials($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Generate a temporary password
            $tempPassword = Str::random(10);
            $user->password = Hash::make($tempPassword);
            $user->save();
            
            // Send email with credentials using GmailService
            $gmailService = new GmailService();
            $fullName = trim($user->first_name . ' ' . $user->last_name);
            $emailSent = $gmailService->sendCredentialsEmail(
                $user->email,
                $fullName,
                $user->username,
                $tempPassword,
                true // is reset
            );
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'resend_credentials',
                'description' => "Resent credentials to user: {$user->first_name} {$user->last_name}",
                'metadata' => json_encode([
                    'user_id' => $user->id,
                    'email_sent' => $emailSent
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'success'
            ]);
            
            if ($emailSent) {
                return response()->json([
                    'message' => 'Credentials have been resent to the user\'s email.'
                ]);
            } else {
                return response()->json([
                    'warning' => 'Failed to send email. Please provide the password manually: ' . $tempPassword,
                    'new_password' => $tempPassword
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error in resendCredentials: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to resend credentials: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users with their latest activity from activity_logs table
     * Includes position from user_profiles for staff users
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        try {
            $query = User::with('profile')  // Eager load profile relationship
                ->select(
                    'id', 
                    'first_name', 
                    'last_name', 
                    'middle_name', 
                    'suffix', 
                    'email', 
                    'username',
                    'role', 
                    'email_verified_at', 
                    'created_at', 
                    'approval_status', 
                    'rejection_reason', 
                    'approved_at'
                )
                ->orderBy('created_at', 'desc');
            
            // Apply filters
            if ($request->has('role') && $request->role !== 'all') {
                $query->where('role', $request->role);
            }
            
            if ($request->has('approval_status') && $request->approval_status !== 'all') {
                $query->where('approval_status', $request->approval_status);
            }
            
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            }
            
            $users = $query->get()->map(function ($user) {
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
                
                // Get position from profile (for staff users)
                $position = null;
                if ($user->role === 'staff' && $user->profile) {
                    $position = $user->profile->position;
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
                
                // Determine role badge colors
                $roleBadge = match($user->role) {
                    'admin' => 'purple',
                    'staff' => 'blue',
                    'applicant' => 'gray',
                    default => 'gray'
                };
                
                // Determine status (for non-applicants, status is based on email verification)
                if (!$user->isApplicant()) {
                    $status = $user->email_verified_at ? 'active' : 'inactive';
                    $statusBadge = $user->email_verified_at ? 'green' : 'yellow';
                } else {
                    // For applicants, status is based on approval_status
                    $status = $user->approval_status;
                    $statusBadge = match($user->approval_status) {
                        'approved' => 'green',
                        'pending' => 'yellow',
                        'rejected' => 'red',
                        default => 'gray'
                    };
                }
                
                return [
                    'id' => $user->id,
                    'name' => $fullName,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'username' => $user->username,
                    'initials' => $initials,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_badge' => $roleBadge,
                    'position' => $position,
                    'status' => $status,
                    'status_badge' => $statusBadge,
                    'last_active' => $lastActive,
                    'created_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : null,
                    'approval_status' => $user->approval_status,
                    'rejection_reason' => $user->rejection_reason,
                    'approved_at' => $user->approved_at,
                    'email_verified_at' => $user->email_verified_at,
                ];
            });
            
            // Calculate statistics with approval stats
            $stats = [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'staff' => User::where('role', 'staff')->count(),
                'applicants' => User::where('role', 'applicant')->count(),
                'active' => User::whereNotNull('email_verified_at')->count(),
                'pending_applicants' => User::where('role', 'applicant')->where('approval_status', 'pending')->count(),
                'approved_applicants' => User::where('role', 'applicant')->where('approval_status', 'approved')->count(),
                'rejected_applicants' => User::where('role', 'applicant')->where('approval_status', 'rejected')->count(),
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
     * Includes position from profile for staff users
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        try {
            $user = User::with('profile')->findOrFail($id);
            
            // Format phone number for display (remove 09 prefix if needed)
            $phoneNumber = $user->phone_number;
            // If it starts with 09 and is 11 digits, remove the 09 for display
            if (substr($phoneNumber, 0, 2) === '09' && strlen($phoneNumber) === 11) {
                $phoneNumber = substr($phoneNumber, 2);
            }
            
            // Get position from profile
            $position = null;
            if ($user->profile) {
                $position = $user->profile->position;
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
                'position' => $position,
                'phone_number' => $phoneNumber,
                'address' => $user->address,
                'zip_code' => $user->zip_code,
                'approval_status' => $user->approval_status,
                'rejection_reason' => $user->rejection_reason,
                'approved_at' => $user->approved_at,
                'approved_by' => $user->approved_by,
                'email_verified_at' => $user->email_verified_at,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getUser: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to load user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a user account (for applicants)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Only applicants can be approved
            if (!$user->isApplicant()) {
                return response()->json(['error' => 'Only applicants can be approved'], 400);
            }
            
            // Check if already approved
            if ($user->isApproved()) {
                return response()->json(['error' => 'User is already approved'], 400);
            }
            
            // Approve the user
            $user->approve(auth()->id());
            
            // Log the approval
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'approve_user',
                'description' => "Approved user: {$user->first_name} {$user->last_name}",
                'metadata' => json_encode([
                    'approved_user_id' => $user->id,
                    'approved_user_email' => $user->email,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success'
            ]);
            
            // Send email notification to user
            try {
                $gmailService = new GmailService();
                $gmailService->sendAccountApprovalEmail($user->email, $user->first_name);
            } catch (\Exception $e) {
                Log::error('Failed to send approval email: ' . $e->getMessage());
            }
            
            return response()->json([
                'message' => 'User approved successfully',
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in approve: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to approve user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reject a user account (for applicants)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500'
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            $user = User::findOrFail($id);
            
            // Only applicants can be rejected
            if (!$user->isApplicant()) {
                return response()->json(['error' => 'Only applicants can be rejected'], 400);
            }
            
            // Check if already rejected
            if ($user->isRejected()) {
                return response()->json(['error' => 'User is already rejected'], 400);
            }
            
            $reason = $request->input('reason');
            
            // Reject the user
            $user->reject(auth()->id(), $reason);
            
            // Log the rejection
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'reject_user',
                'description' => "Rejected user: {$user->first_name} {$user->last_name}" . ($reason ? " - Reason: {$reason}" : ""),
                'metadata' => json_encode([
                    'rejected_user_id' => $user->id,
                    'rejected_user_email' => $user->email,
                    'reason' => $reason
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success'
            ]);
            
            // Send email notification to user
            try {
                $gmailService = new GmailService();
                $gmailService->sendAccountRejectionEmail($user->email, $user->first_name, $reason);
            } catch (\Exception $e) {
                Log::error('Failed to send rejection email: ' . $e->getMessage());
            }
            
            return response()->json([
                'message' => 'User rejected successfully',
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in reject: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to reject user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get pending applicants
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingApplicants()
    {
        try {
            $pendingApplicants = User::where('role', 'applicant')
                ->where('approval_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(['id', 'first_name', 'last_name', 'email', 'username', 'created_at'])
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'username' => $user->username,
                        'registered_at' => $user->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            
            return response()->json([
                'pending_count' => $pendingApplicants->count(),
                'pending_applicants' => $pendingApplicants
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getPendingApplicants: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load pending applicants'], 500);
        }
    }
}