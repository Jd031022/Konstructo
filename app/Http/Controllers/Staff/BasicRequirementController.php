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
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:10|max:1000',
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

            $fullReason = $request->rejection_reason;
            if ($request->notes) {
                $fullReason .= "\n\nAdditional Notes: " . $request->notes;
            }

            $requirement->markAsRejected($fullReason, Auth::id());

            // Get rejector name
            $rejector = Auth::user();
            $rejectorName = $rejector->first_name . ' ' . $rejector->last_name;

            // Send email notification to applicant
            $emailSent = $this->gmailService->sendBasicRequirementsRejectedEmail(
                $user->email,
                $user->first_name,
                $fullReason,
                $requirement->id,
                $rejectorName
            );

            if ($emailSent) {
                Log::info('Basic requirements rejection email sent', [
                    'requirement_id' => $requirement->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
            } else {
                Log::warning('Failed to send basic requirements rejection email', [
                    'requirement_id' => $requirement->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
            }

            Log::info('Basic requirements rejected', [
                'requirement_id' => $requirement->id,
                'user_id' => $user->id,
                'rejected_by' => Auth::id(),
                'reason' => $fullReason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Basic requirements rejected. Applicant has been notified via email.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting basic requirements: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting requirements: ' . $e->getMessage()
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