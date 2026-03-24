<?php
// app/Http/Controllers/Staff/BasicRequirementController.php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BasicRequirement;
use App\Models\User;
use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BasicRequirementController extends Controller
{
    protected $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    /**
     * Display list of pending basic requirements
     */
    public function index()
    {
        $requirements = BasicRequirement::with(['user', 'approver'])
            ->where('status', 'pending')
            ->orderBy('submitted_at', 'desc')
            ->paginate(15);
            
        return view('staff.basic-requirements.index', compact('requirements'));
    }

    /**
     * Show details of a specific requirement
     */
    public function show($id)
    {
        $requirement = BasicRequirement::with(['user', 'approver'])->findOrFail($id);
        
        // Mark as viewed in session
        session(['viewed_requirement_' . $id => true]);
        
        return view('staff.basic-requirements.show', compact('requirement'));
    }

    /**
     * Approve basic requirements
     */
    public function approve(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $requirement = BasicRequirement::findOrFail($id);
            $user = $requirement->user;
            
            if ($requirement->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This requirement has already been processed.'
                ], 400);
            }

            $requirement->markAsApproved(Auth::id(), $request->notes);
            
            // Update user's basic_requirements_approved_at field
            $user->update([
                'basic_requirements_approved_at' => now(),
                'basic_requirements_approved_by' => Auth::id()
            ]);

            // Get approver name
            $approver = Auth::user();
            $approverName = $approver->first_name . ' ' . $approver->last_name;

            // Send email notification to applicant
            $emailSent = $this->gmailService->sendBasicRequirementsApprovedEmail(
                $user->email,
                $user->first_name,
                $requirement->id,
                $approverName
            );

            if ($emailSent) {
                Log::info('Basic requirements approval email sent', [
                    'requirement_id' => $requirement->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
            } else {
                Log::warning('Failed to send basic requirements approval email', [
                    'requirement_id' => $requirement->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
            }

            Log::info('Basic requirements approved', [
                'requirement_id' => $requirement->id,
                'user_id' => $user->id,
                'approved_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Basic requirements approved successfully. Applicant has been notified via email.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving basic requirements: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error approving requirements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject basic requirements
     */
   // In app/Http/Controllers/Staff/BasicRequirementController.php

public function reject(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:10',
            'notes' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
        
        $requirement = BasicRequirement::findOrFail($id);
        
        // Only allow rejection of pending requirements
        if ($requirement->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This requirement has already been processed'
            ], 400);
        }
        
        DB::beginTransaction();
        
        $requirement->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'admin_notes' => $request->notes
        ]);
        
        // Create notification for applicant
        Notification::create([
            'user_id' => $requirement->user_id,
            'type' => 'basic_requirements_rejected',
            'title' => 'Basic Requirements Rejected',
            'message' => 'Your basic requirements have been rejected. Reason: ' . $request->rejection_reason,
            'link' => route('applicant.basic-requirements.index'),
            'created_at' => now()
        ]);
        
        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'basic_requirements_rejected',
            'description' => 'Rejected basic requirements for applicant: ' . $requirement->user->email,
            'metadata' => json_encode([
                'requirement_id' => $requirement->id,
                'applicant_id' => $requirement->user_id,
                'reason' => $request->rejection_reason
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success'
        ]);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Requirements rejected successfully'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error rejecting requirements: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to reject requirements: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'pending' => BasicRequirement::where('status', 'pending')->count(),
            'approved_today' => BasicRequirement::whereDate('approved_at', today())->count(),
            'rejected_today' => BasicRequirement::whereDate('updated_at', today())
                ->where('status', 'rejected')
                ->count(),
            'total_submitted' => BasicRequirement::whereNotNull('submitted_at')->count()
        ];
        
        return response()->json($stats);
    }

    
}