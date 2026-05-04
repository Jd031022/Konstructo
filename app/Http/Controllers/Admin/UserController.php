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

    /**
     * Normalize position value - ensure BFP is uppercase
     */
    private function normalizePosition($position)
    {
        if (empty($position)) {
            return null;
        }
        
        // Convert 'bfp' (lowercase) to 'BFP' (uppercase)
        if (strtolower($position) === 'bfp') {
            return 'BFP';
        }
        
        return $position;
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
        'position' => 'required_if:role,staff|nullable|in:engineer,architect,BFP,cpdo,administrative_aide,treasurer,assessor,mayor',
        'specialization' => 'required_if:position,engineer|nullable|in:civil_engineer,electrical_engineer,chemical_engineer,mechanical_engineer'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Set approval status based on role
    $approvalStatus = ($request->role === 'applicant') ? 'pending' : 'approved';
    $emailVerifiedAt = ($request->role !== 'applicant') ? now() : null;

    // Store the plain password for email before hashing
    $plainPassword = $request->password;

    // Generate a dummy phone number that will pass validation (09 + 9 digits)
    $dummyPhoneNumber = '09123456789';
    
    // Dummy address and zip code
    $dummyAddress = 'System Generated User';
    $dummyZipCode = '0000';

    $user = User::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'middle_name' => $request->middle_name,
        'suffix' => $request->suffix,
        'email' => $request->email,
        'username' => $request->username,
        'password' => Hash::make($plainPassword),
        'role' => $request->role,
        'phone_number' => $dummyPhoneNumber,
        'address' => $dummyAddress,
        'zip_code' => $dummyZipCode,
        'email_verified_at' => $emailVerifiedAt,
        'approval_status' => $approvalStatus,
    ]);

    // Create profile for staff with position and specialization
    if ($request->role === 'staff' && $request->filled('position')) {
        $normalizedPosition = $this->normalizePosition($request->position);
        $profileData = ['position' => $normalizedPosition];
        
        // Add specialization if position is engineer
        if ($request->position === 'engineer' && $request->filled('specialization')) {
            $profileData['specialization'] = $request->specialization;
        }
        
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
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
            'specialization' => $request->specialization ?? null,
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
        'position' => 'required_if:role,staff|nullable|in:engineer,architect,BFP,cpdo,administrative_aide,treasurer,assessor,mayor',
        'specialization' => 'required_if:position,engineer|nullable|in:civil_engineer,electrical_engineer,chemical_engineer,mechanical_engineer'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
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
    ];

    // If role changed from applicant to admin/staff, auto-approve
    if ($oldRole === 'applicant' && in_array($newRole, ['admin', 'staff'])) {
        $updateData['approval_status'] = 'approved';
        $updateData['approved_at'] = now();
        $updateData['approved_by'] = auth()->id();
        $updateData['email_verified_at'] = now();
    }

    // If role changed to applicant, set approval to pending
    if ($newRole === 'applicant' && $oldRole !== 'applicant') {
        $updateData['approval_status'] = 'pending';
        $updateData['approved_at'] = null;
        $updateData['approved_by'] = null;
    }

    $user->update($updateData);

    // Handle position and specialization for staff
    if ($newRole === 'staff') {
        if ($request->filled('position')) {
            $normalizedPosition = $this->normalizePosition($request->position);
            $profileData = ['position' => $normalizedPosition];
            
            // Add specialization if position is engineer
            if ($request->position === 'engineer' && $request->filled('specialization')) {
                $profileData['specialization'] = $request->specialization;
            } else {
                $profileData['specialization'] = null;
            }
            
            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }
    } else {
        // If user is no longer staff, remove profile data
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
            'position' => $request->position,
            'specialization' => $request->specialization ?? null
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
        $query = User::with('profile')
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
            
            // Get position and specialization from profile (for staff users)
            $position = null;
            $specialization = null;
            if ($user->role === 'staff' && $user->profile) {
                $position = $user->profile->position;
                $specialization = $user->profile->specialization;
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
            
            // Get display position (include specialization for engineers)
            $positionDisplay = $this->getPositionDisplay($position, $user->role);
            if ($position === 'engineer' && $specialization) {
                $positionDisplay = $this->getSpecializationDisplay($specialization);
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
                'specialization' => $specialization,
                'position_display' => $positionDisplay,
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

// Add a new helper method for specialization display:

private function getSpecializationDisplay($specialization): string
{
    $specializations = [
        'civil_engineer' => 'Civil Engineer',
        'electrical_engineer' => 'Electrical Engineer',
        'chemical_engineer' => 'Chemical Engineer',
        'mechanical_engineer' => 'Mechanical Engineer',
    ];
    
    return $specializations[$specialization] ?? ucfirst(str_replace('_', ' ', $specialization ?? ''));
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
        
        // Get position and specialization from profile
        $position = null;
        $specialization = null;
        if ($user->profile) {
            $position = $user->profile->position;
            $specialization = $user->profile->specialization;
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
            'specialization' => $specialization,
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

      /**
     * Export users with multiple format options
     */
    public function exportUsers(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            $users = $this->getUsersForExport($request);
            
            switch ($format) {
                case 'csv':
                    return $this->exportAsCSV($users);
                case 'excel':
                    return $this->exportAsExcel($users);
                case 'pdf':
                    return $this->exportAsPDF($users);
                case 'html':
                default:
                    return $this->exportAsHTML($users);
            }
        } catch (\Exception $e) {
            Log::error('Users export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get filtered users for export
     */
    private function getUsersForExport(Request $request)
    {
        $query = User::query();
        
        // Apply filters similar to the page
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Export as CSV (Excel compatible)
     */
    private function exportAsCSV($users)
    {
        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() use ($users) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($handle, [
                'User ID',
                'Name',
                'Email',
                'Username',
                'Role',
                'Position',
                'Status',
                'Registered Date',
                'Last Active',
                'Email Verified'
            ]);
            
            foreach ($users as $user) {
                $fullName = trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name);
                $positionDisplay = $this->getPositionDisplay($user->position, $user->role);
                $status = ($user->is_active ?? true) ? 'Active' : 'Inactive';
                $emailVerified = $user->email_verified_at ? 'Yes' : 'No';
                
                fputcsv($handle, [
                    $user->id,
                    $fullName,
                    $user->email,
                    $user->username,
                    ucfirst($user->role),
                    $positionDisplay,
                    $status,
                    $user->created_at ? $user->created_at->format('Y-m-d') : '',
                    $user->last_login_at ? date('Y-m-d', strtotime($user->last_login_at)) : 'Never',
                    $emailVerified
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Export as Excel (XLSX using CSV format with .xlsx extension)
     */
    private function exportAsExcel($users)
    {
        $filename = 'users_export_' . date('Y-m-d_His') . '.xlsx';
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() use ($users) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($handle, [
                'User ID',
                'Name',
                'Email',
                'Username',
                'Role',
                'Position',
                'Status',
                'Registered Date',
                'Last Active',
                'Email Verified'
            ]);
            
            foreach ($users as $user) {
                $fullName = trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name);
                $positionDisplay = $this->getPositionDisplay($user->position, $user->role);
                $status = ($user->is_active ?? true) ? 'Active' : 'Inactive';
                $emailVerified = $user->email_verified_at ? 'Yes' : 'No';
                
                fputcsv($handle, [
                    $user->id,
                    $fullName,
                    $user->email,
                    $user->username,
                    ucfirst($user->role),
                    $positionDisplay,
                    $status,
                    $user->created_at ? $user->created_at->format('Y-m-d') : '',
                    $user->last_login_at ? date('Y-m-d', strtotime($user->last_login_at)) : 'Never',
                    $emailVerified
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Export as PDF
     */
    private function exportAsPDF($users)
    {
        $stats = [
            'total' => $users->count(),
            'admins' => $users->where('role', 'admin')->count(),
            'staff' => $users->where('role', 'staff')->count(),
            'applicants' => $users->where('role', 'applicant')->count(),
            'active' => $users->filter(function($u) { return $u->is_active ?? true; })->count(),
        ];
        
        $html = $this->generatePDFHTML($users, $stats);
        
        // Use DomPDF if installed, otherwise fallback to HTML
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('users_export_' . date('Y-m-d_His') . '.pdf');
        }
        
        // Fallback to HTML download
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="users_export_' . date('Y-m-d_His') . '.html"');
    }

    /**
     * Export as HTML
     */
    private function exportAsHTML($users)
    {
        $stats = [
            'total' => $users->count(),
            'admins' => $users->where('role', 'admin')->count(),
            'staff' => $users->where('role', 'staff')->count(),
            'applicants' => $users->where('role', 'applicant')->count(),
            'active' => $users->filter(function($u) { return $u->is_active ?? true; })->count(),
        ];
        
        $html = $this->generateHTMLExport($users, $stats);
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="users_export_' . date('Y-m-d_His') . '.html"');
    }

    /**
     * Generate PDF HTML content
     */
    private function generatePDFHTML($users, $stats)
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Konstructo Users Export - ' . date('Y-m-d H:i:s') . '</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: "Poppins", Arial, sans-serif;
                    padding: 20px;
                    color: #333;
                    font-size: 10px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #155386;
                }
                .header h1 {
                    color: #155386;
                    font-size: 20px;
                }
                .header p {
                    color: #666;
                    font-size: 10px;
                }
                .stats {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 20px;
                    flex-wrap: wrap;
                }
                .stat-box {
                    background: #f5f5f5;
                    padding: 10px;
                    border-radius: 8px;
                    text-align: center;
                    min-width: 100px;
                }
                .stat-box .label {
                    font-size: 9px;
                    color: #666;
                }
                .stat-box .value {
                    font-size: 18px;
                    font-weight: bold;
                    color: #155386;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background: #155386;
                    color: white;
                    font-size: 9px;
                }
                td {
                    font-size: 9px;
                }
                tr:nth-child(even) {
                    background: #f9f9f9;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    padding-top: 10px;
                    border-top: 1px solid #ddd;
                    font-size: 8px;
                    color: #999;
                }
                .status-active { color: #10b981; font-weight: bold; }
                .status-inactive { color: #ef4444; font-weight: bold; }
                .role-badge {
                    display: inline-block;
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-size: 8px;
                }
                .role-admin { background: #e9d5ff; color: #6b21a5; }
                .role-staff { background: #dbeafe; color: #1e40af; }
                .role-applicant { background: #e5e7eb; color: #374151; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Konstructo Users Export</h1>
                <p>Generated on: ' . date('F d, Y g:i:s A') . '</p>
            </div>
            
            <div class="stats">
                <div class="stat-box"><div class="label">Total Users</div><div class="value">' . $stats['total'] . '</div></div>
                <div class="stat-box"><div class="label">Admins</div><div class="value">' . $stats['admins'] . '</div></div>
                <div class="stat-box"><div class="label">Staff</div><div class="value">' . $stats['staff'] . '</div></div>
                <div class="stat-box"><div class="label">Applicants</div><div class="value">' . $stats['applicants'] . '</div></div>
                <div class="stat-box"><div class="label">Active</div><div class="value">' . $stats['active'] . '</div></div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($users as $user) {
            $fullName = trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name);
            $roleClass = $user->role === 'admin' ? 'role-admin' : ($user->role === 'staff' ? 'role-staff' : 'role-applicant');
            $statusClass = ($user->is_active ?? true) ? 'status-active' : 'status-inactive';
            $statusText = ($user->is_active ?? true) ? 'Active' : 'Inactive';
            $positionDisplay = $this->getPositionDisplay($user->position, $user->role);
            
            $html .= '<tr>
                        <td>' . $user->id . '</td>
                        <td>' . htmlspecialchars($fullName) . '</td>
                        <td>' . htmlspecialchars($user->email) . '</td>
                        <td>' . htmlspecialchars($user->username) . '</td>
                        <td><span class="role-badge ' . $roleClass . '">' . ucfirst($user->role) . '</span></td>
                        <td>' . htmlspecialchars($positionDisplay) . '</td>
                        <td class="' . $statusClass . '">' . $statusText . '</td>
                        <td>' . ($user->created_at ? $user->created_at->format('Y-m-d') : '') . '</td>
                     </tr>';
        }
        
        $html .= '</tbody>
            </table>
            
            <div class="footer">
                <p>Konstructo - Smart Infrastructure Oversight</p>
                <p>This report was generated automatically on ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Generate HTML Export content
     */
    private function generateHTMLExport($users, $stats)
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Konstructo Users Export - ' . date('Y-m-d H:i:s') . '</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: "Poppins", -apple-system, Arial, sans-serif;
                    background: #f0f2f5;
                    padding: 30px 20px;
                    color: #1a1a2e;
                }
                .container {
                    max-width: 1400px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 20px;
                    padding: 30px;
                    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #e9ecef;
                    flex-wrap: wrap;
                }
                .header h1 {
                    color: #155386;
                    font-size: 28px;
                    font-weight: 600;
                }
                .header-date {
                    color: #6c757d;
                    font-size: 14px;
                }
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(5, 1fr);
                    gap: 20px;
                    margin-bottom: 30px;
                }
                .stat-card {
                    background: #f8f9fa;
                    border-radius: 16px;
                    padding: 20px;
                    text-align: center;
                    border: 1px solid #e5e7eb;
                }
                .stat-label {
                    font-size: 13px;
                    color: #6c757d;
                    margin-bottom: 8px;
                    text-transform: uppercase;
                }
                .stat-value {
                    font-size: 32px;
                    font-weight: 700;
                    color: #155386;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 12px 15px;
                    text-align: left;
                    border-bottom: 1px solid #e9ecef;
                }
                th {
                    background: #f8f9fa;
                    font-weight: 600;
                    font-size: 12px;
                    color: #495057;
                    text-transform: uppercase;
                }
                td {
                    font-size: 13px;
                }
                tr:hover {
                    background: #f8f9fa;
                }
                .role-badge {
                    display: inline-block;
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 11px;
                    font-weight: 500;
                }
                .role-admin { background: #e9d5ff; color: #6b21a5; }
                .role-staff { background: #dbeafe; color: #1e40af; }
                .role-applicant { background: #e5e7eb; color: #374151; }
                .status-active { color: #10b981; font-weight: 600; }
                .status-inactive { color: #ef4444; font-weight: 600; }
                .footer {
                    text-align: center;
                    padding: 20px;
                    color: #6c757d;
                    font-size: 11px;
                    border-top: 1px solid #e9ecef;
                    margin-top: 20px;
                }
                .print-btn {
                    background: #155386;
                    color: white;
                    border: none;
                    padding: 8px 18px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 13px;
                    font-family: "Poppins", sans-serif;
                }
                @media print {
                    body { background: white; padding: 0; }
                    .container { box-shadow: none; padding: 15px; }
                    .print-btn { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div>
                        <h1>Konstructo Users Export</h1>
                        <div class="header-date">Generated: ' . date('F d, Y g:i:s A') . '</div>
                    </div>
                    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-label">Total Users</div><div class="stat-value">' . $stats['total'] . '</div></div>
                    <div class="stat-card"><div class="stat-label">Admins</div><div class="stat-value">' . $stats['admins'] . '</div></div>
                    <div class="stat-card"><div class="stat-label">Staff</div><div class="stat-value">' . $stats['staff'] . '</div></div>
                    <div class="stat-card"><div class="stat-label">Applicants</div><div class="stat-value">' . $stats['applicants'] . '</div></div>
                    <div class="stat-card"><div class="stat-label">Active</div><div class="stat-value">' . $stats['active'] . '</div></div>
                </div>
                
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Last Active</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($users as $user) {
            $fullName = trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name);
            $roleClass = $user->role === 'admin' ? 'role-admin' : ($user->role === 'staff' ? 'role-staff' : 'role-applicant');
            $statusClass = ($user->is_active ?? true) ? 'status-active' : 'status-inactive';
            $statusText = ($user->is_active ?? true) ? 'Active' : 'Inactive';
            $positionDisplay = $this->getPositionDisplay($user->position, $user->role);
            
            $html .= '<tr>
                        <td>' . $user->id . '</td>
                        <td>' . htmlspecialchars($fullName) . '</td>
                        <td>' . htmlspecialchars($user->email) . '</td>
                        <td>' . htmlspecialchars($user->username) . '</td>
                        <td><span class="role-badge ' . $roleClass . '">' . ucfirst($user->role) . '</span></td>
                        <td>' . htmlspecialchars($positionDisplay) . '</td>
                        <td class="' . $statusClass . '">' . $statusText . '</td>
                        <td>' . ($user->created_at ? $user->created_at->format('M d, Y') : 'N/A') . '</td>
                        <td>' . ($user->last_login_at ? date('M d, Y', strtotime($user->last_login_at)) : 'Never') . '</td>
                     </tr>';
        }
        
        $html .= '</tbody>
                    </table>
                </div>
                
                <div class="footer">
                    <p>Konstructo - Smart Infrastructure Oversight</p>
                    <p>Report ID: KUS-' . date('Ymd') . '-' . rand(1000, 9999) . ' | Generated: ' . date('Y-m-d H:i:s') . '</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Get position display text
     */
    private function getPositionDisplay($position, $role)
{
    if ($role !== 'staff' || !$position) return '—';
    
    $positionMap = [
        'engineer' => 'Engineer',
        'architect' => 'Architect',
        'BFP' => 'BFP',
        'bfp' => 'BFP',
        'cpdo' => 'CPDO',
        'administrative_aide' => 'Admin Aide',
        'treasurer' => 'Treasurer',
        'assessor' => 'Assessor',
        'mayor' => 'Mayor'
    ];
    
    return $positionMap[$position] ?? ucfirst($position);
}
}