<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\User;
use App\Models\ApplicationReviewActivity;
use App\Models\AssessmentFee;
use App\Models\BfpApplicationData;
use App\Models\ActivityLog;
use App\Models\OwnershipVerification;
use App\Services\NotificationService;
use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    protected $notificationService;
    protected $gmailService;

    public function __construct(NotificationService $notificationService, GmailService $gmailService)
    {
        $this->notificationService = $notificationService;
        $this->gmailService = $gmailService;
        
        Log::info('ApplicationController initialized', [
            'notification_service' => get_class($notificationService),
            'gmail_service' => get_class($gmailService)
        ]);
    }

   /**
 * Display a listing of all submitted applications (excluding drafts and archived)
 */
public function index(Request $request)
{
    try {
        Log::info('Fetching all staff applications');
        
        // Get show_archived parameter from request
        $showArchived = $request->query('show_archived', false);
        
        Log::info('Show archived filter', ['show_archived' => $showArchived]);
        
        $query = ApplicationDocument::with(['user', 'lastUpdatedBy']);
        
        if ($showArchived == 'true' || $showArchived === true) {
            // Show archived applications (both active and archived)
            $query->where(function($q) {
                $q->where('is_archived', true)
                  ->orWhereIn('status', [
                      'pending', 
                      'under-review', 
                      'document-verification',
                      'for-assessment',
                      'approved', 
                      'rejected', 
                      'for-release', 
                      'verified'
                  ]);
            });
            Log::info('Showing archived applications as well');
        } else {
            // Show only active applications (not archived and with valid statuses)
            $query->where('is_archived', false)
                  ->whereIn('status', [
                      'pending', 
                      'under-review', 
                      'document-verification',
                      'for-assessment',
                      'approved', 
                      'rejected', 
                      'for-release', 
                      'verified'
                  ]);
            Log::info('Showing only active applications');
        }
        
        $applications = $query->orderBy('created_at', 'desc')->get();

        $formattedApplications = [];
        foreach ($applications as $app) {
            $applicantName = 'Unknown';
            if ($app->user) {
                $firstName = $app->user->first_name ?? '';
                $lastName = $app->user->last_name ?? '';
                $applicantName = trim($firstName . ' ' . $lastName);
                if (empty($applicantName)) {
                    $applicantName = 'Unknown';
                }
            }
            
            $createdAt = $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : null;
            $updatedAt = $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : null;
            
            $lastUpdatedByName = null;
            if ($app->lastUpdatedBy) {
                $firstName = $app->lastUpdatedBy->first_name ?? '';
                $lastName = $app->lastUpdatedBy->last_name ?? '';
                $lastUpdatedByName = trim($firstName . ' ' . $lastName);
                if (empty($lastUpdatedByName)) {
                    $lastUpdatedByName = null;
                }
            }
            
            $formattedApplications[] = [
                'id' => $app->id,
                'application_number' => $app->application_number ?? 'N/A',
                'applicant_name' => $applicantName,
                'email' => $app->user ? ($app->user->email ?? null) : null,
                'phone' => $app->user ? ($app->user->phone_number ?? null) : null,
                'address' => $app->user ? ($app->user->address ?? null) : null,
                'google_drive_link' => $app->google_drive_link,
                'status' => $app->status ?? 'unknown',
                'rejection_reason' => $app->rejection_reason,
                'admin_notes' => $app->admin_notes,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'hard_copy_received' => $app->hard_copy_received ?? false,
                'last_updated_by' => $app->last_updated_by,
                'last_updated_by_name' => $lastUpdatedByName,
                'last_updated_by_role' => $app->lastUpdatedBy ? ($app->lastUpdatedBy->role ?? null) : null,
                'is_archived' => $app->is_archived ?? false,
                'archived_at' => $app->archived_at,
                'archive_reason' => $app->archive_reason,
                'project_title' => $app->project_title ?? null
            ];
        }
        
        return response()->json([
            'success' => true,
            'applications' => $formattedApplications,
            'total' => count($formattedApplications)
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error loading staff applications: ' . $e->getMessage());
        Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error loading applications: ' . $e->getMessage(),
            'applications' => []
        ], 500);
    }
}

    /**
     * Get current user's position
     */
    public function getUserPosition()
    {
        try {
            $user = Auth::user();
            $position = null;
            
            if ($user) {
                $user->load('profile');
                if ($user->profile) {
                    $position = $user->profile->position;
                }
            }
            
            Log::info('Get user position', [
                'user_id' => $user ? $user->id : null,
                'position' => $position
            ]);
            
            return response()->json([
                'position' => $position,
                'needs_position' => !$position && $user && $user->role === 'staff'
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user position: ' . $e->getMessage());
            return response()->json([
                'position' => null,
                'needs_position' => false
            ]);
        }
    }

    /**
     * Check if user is BFP (case-insensitive)
     */
    private function isBFPUser($user)
    {
        if (!$user) return false;
        
        $user->load('profile');
        $position = $user->profile ? $user->profile->position : null;
        
        Log::info('Checking if user is BFP', [
            'user_id' => $user->id,
            'position' => $position,
            'is_bfp' => $position && strtoupper($position) === 'BFP'
        ]);
        
        return $position && strtoupper($position) === 'BFP';
    }

    /**
     * Get BFP data for an application
     */
    public function getBfpData($id)
    {
        try {
            $bfpData = BfpApplicationData::where('application_id', $id)->first();
            
            if (!$bfpData) {
                return response()->json([
                    'success' => true,
                    'data' => null
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'fsec_link' => $bfpData->fsec_link,
                    'fsec_filename' => $bfpData->fsec_filename,
                    'fsec_uploaded_at' => $bfpData->fsec_uploaded_at,
                    'bfp_comments' => $bfpData->bfp_comments,
                    'bfp_comments_updated_at' => $bfpData->bfp_comments_updated_at,
                    'bfp_user_name' => $bfpData->bfpUser ? $bfpData->bfpUser->first_name . ' ' . $bfpData->bfpUser->last_name : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting BFP data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving BFP data'
            ], 500);
        }
    }

    /**
     * Upload FSEC file (BFP only)
     */
    public function uploadFSEC(Request $request, $id)
    {
        Log::info('========== UPLOAD FSEC START ==========');
        Log::info('uploadFSEC called', [
            'application_id' => $id,
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated',
            'user_id' => auth()->id()
        ]);

        $validator = Validator::make($request->all(), [
            'fsec_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $staff = auth()->user();
            
            // Check if user is BFP using case-insensitive comparison
            if (!$this->isBFPUser($staff)) {
                Log::error('Unauthorized: User is not BFP', [
                    'user_id' => $staff->id,
                    'position' => $staff->profile ? $staff->profile->position : 'no profile'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Only BFP staff can upload FSEC documents. Your position: ' . ($staff->profile ? $staff->profile->position : 'not set')
                ], 403);
            }

            $file = $request->file('fsec_file');
            $originalFilename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = 'fsec_' . $application->application_number . '_' . time() . '.' . $extension;
            
            // Store file in storage/app/public/fsec
            $path = $file->storeAs('fsec', $filename, 'public');
            $fullPath = Storage::url($path);
            
            Log::info('File uploaded', [
                'original_name' => $originalFilename,
                'saved_as' => $filename,
                'path' => $fullPath
            ]);

            // Update or create BFP data record
            $bfpData = BfpApplicationData::updateOrCreate(
                ['application_id' => $id],
                [
                    'bfp_user_id' => $staff->id,
                    'fsec_link' => $fullPath,
                    'fsec_filename' => $originalFilename,
                    'fsec_uploaded_at' => now()
                ]
            );

            // Send notifications to applicant and staff about FSEC upload
            try {
                $this->notificationService->notifyFSECUploaded($application, $staff, $fullPath, $originalFilename);
                Log::info('✅ FSEC notifications sent successfully');
            } catch (\Exception $e) {
                Log::error('❌ Failed to send FSEC notifications: ' . $e->getMessage());
            }

            // Log activity
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                'fsec_uploaded',
                null,
                null,
                "FSEC document uploaded: {$originalFilename}",
                $request->ip(),
                $request->userAgent()
            );

            Log::info('========== UPLOAD FSEC END (SUCCESS) ==========');

            return response()->json([
                'success' => true,
                'message' => 'FSEC uploaded successfully',
                'link' => $fullPath,
                'filename' => $originalFilename
            ]);

        } catch (\Exception $e) {
            Log::error('========== UPLOAD FSEC END (ERROR) ==========');
            Log::error('Error uploading FSEC: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error uploading FSEC: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete FSEC file (BFP only)
     */
    public function deleteFSEC(Request $request, $id)
    {
        Log::info('========== DELETE FSEC START ==========');
        Log::info('deleteFSEC called', [
            'application_id' => $id,
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated'
        ]);

        try {
            $application = ApplicationDocument::find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $staff = auth()->user();
            
            // Check if user is BFP using case-insensitive comparison
            if (!$this->isBFPUser($staff)) {
                Log::error('Unauthorized: User is not BFP');
                return response()->json([
                    'success' => false,
                    'message' => 'Only BFP staff can delete FSEC documents'
                ], 403);
            }

            $bfpData = BfpApplicationData::where('application_id', $id)->first();
            
            if (!$bfpData || !$bfpData->fsec_link) {
                return response()->json([
                    'success' => false,
                    'message' => 'No FSEC file found to delete'
                ], 404);
            }

            // Delete the file from storage
            $path = str_replace('/storage', '', $bfpData->fsec_link);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('File deleted from storage', ['path' => $path]);
            }

            // Clear the FSEC fields
            $bfpData->fsec_link = null;
            $bfpData->fsec_filename = null;
            $bfpData->fsec_uploaded_at = null;
            $bfpData->bfp_user_id = $staff->id;
            $bfpData->save();

            // Log activity
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                'fsec_deleted',
                null,
                null,
                "FSEC document deleted",
                $request->ip(),
                $request->userAgent()
            );

            Log::info('========== DELETE FSEC END (SUCCESS) ==========');

            return response()->json([
                'success' => true,
                'message' => 'FSEC deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('========== DELETE FSEC END (ERROR) ==========');
            Log::error('Error deleting FSEC: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting FSEC: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save BFP comments (BFP only)
     */
    public function saveBFPComments(Request $request, $id)
    {
        Log::info('========== SAVE BFP COMMENTS START ==========');
        Log::info('saveBFPComments called', [
            'application_id' => $id,
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated'
        ]);

        $validator = Validator::make($request->all(), [
            'comments' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $staff = auth()->user();
            
            // Check if user is BFP using case-insensitive comparison
            if (!$this->isBFPUser($staff)) {
                Log::error('Unauthorized: User is not BFP');
                return response()->json([
                    'success' => false,
                    'message' => 'Only BFP staff can add comments'
                ], 403);
            }

            // Update or create BFP data record
            $bfpData = BfpApplicationData::updateOrCreate(
                ['application_id' => $id],
                [
                    'bfp_user_id' => $staff->id,
                    'bfp_comments' => $request->comments,
                    'bfp_comments_updated_at' => now()
                ]
            );

            // Send notifications to applicant and staff about BFP comments
            if ($request->filled('comments') && $request->comments !== '') {
                try {
                    $this->notificationService->notifyBFPCommentsAdded($application, $staff, $request->comments);
                    Log::info('✅ BFP comments notifications sent successfully');
                } catch (\Exception $e) {
                    Log::error('❌ Failed to send BFP comments notifications: ' . $e->getMessage());
                }
            }

            // Log activity
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                'bfp_comments_added',
                null,
                null,
                "BFP comments added/updated",
                $request->ip(),
                $request->userAgent()
            );

            Log::info('========== SAVE BFP COMMENTS END (SUCCESS) ==========');

            return response()->json([
                'success' => true,
                'message' => 'BFP comments saved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('========== SAVE BFP COMMENTS END (ERROR) ==========');
            Log::error('Error saving BFP comments: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving BFP comments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single application details
     */
    public function show($id)
    {
        try {
            Log::info('Fetching application details for ID: ' . $id);
            
            $application = ApplicationDocument::with(['user', 'lastUpdatedBy'])
                ->where('is_archived', false)
                ->whereIn('status', [
                    'pending', 
                    'under-review', 
                    'document-verification',
                    'for-assessment',
                    'approved', 
                    'rejected', 
                    'for-release', 
                    'verified'
                ])
                ->find($id);
            
            if (!$application) {
                Log::error('Application not found for ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }
            
            $lastUpdatedBy = null;
            if ($application->last_updated_by) {
                $lastUpdatedBy = User::find($application->last_updated_by);
            }
            
            // Format currency values
            $estimatedCost = $application->estimated_cost ? '₱ ' . number_format($application->estimated_cost, 2) : null;
            $lotArea = $application->lot_area ? number_format($application->lot_area, 2) . ' sqm' : null;
            $floorArea = $application->floor_area ? number_format($application->floor_area, 2) . ' sqm' : null;
            
            // Get CPDO status from application
            $cpdoStatus = $application->cpdo_status ?? 'pending';
            $cpdoRemarks = $application->cpdo_remarks ?? null;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'application_number' => $application->application_number,
                    'applicant_name' => $application->user ? $application->user->first_name . ' ' . $application->user->last_name : 'Unknown',
                    'email' => $application->user ? $application->user->email : null,
                    'phone' => $application->user ? $application->user->phone_number : null,
                    'address' => $application->user ? $application->user->address : null,
                    'google_drive_link' => $application->google_drive_link,
                    'document_links' => $application->document_links,
                    'status' => $application->status,
                    'rejection_reason' => $application->rejection_reason,
                    'admin_notes' => $application->admin_notes,
                    'created_at' => $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $application->updated_at ? $application->updated_at->format('Y-m-d H:i:s') : null,
                    'hard_copy_received' => $application->hard_copy_received ?? false,
                    'last_updated_by' => $application->last_updated_by,
                    'last_updated_by_name' => $lastUpdatedBy ? $lastUpdatedBy->first_name . ' ' . $lastUpdatedBy->last_name : null,
                    'last_updated_by_role' => $lastUpdatedBy ? $lastUpdatedBy->role : null,
                    'last_updated_by_email' => $lastUpdatedBy ? $lastUpdatedBy->email : null,
                    'last_updated_by_initials' => $lastUpdatedBy ? 
                        strtoupper(substr($lastUpdatedBy->first_name, 0, 1) . substr($lastUpdatedBy->last_name, 0, 1)) : 'ST',
                    'is_archived' => $application->is_archived,
                    // Project Information Fields
                    'project_title' => $application->project_title ?? null,
                    'project_location' => $application->project_location ?? null,
                    'project_type' => $application->project_type ?? null,
                    'project_description' => $application->project_description ?? null,
                    'lot_area' => $application->lot_area ?? null,
                    'lot_area_formatted' => $lotArea,
                    'floor_area' => $application->floor_area ?? null,
                    'floor_area_formatted' => $floorArea,
                    'num_floors' => $application->num_floors ?? null,
                    'estimated_cost' => $application->estimated_cost ?? null,
                    'estimated_cost_formatted' => $estimatedCost,
                    // Owner Information
                    'owner_name' => $application->owner_name ?? null,
                    'owner_address' => $application->owner_address ?? null,
                    'contact_number' => $application->contact_number ?? null,
                    'owner_email' => $application->owner_email ?? null,
                    // Professional Information
                    'architect_name' => $application->architect_name ?? null,
                    'architect_license' => $application->architect_license ?? null,
                    'engineer_name' => $application->engineer_name ?? null,
                    'engineer_license' => $application->engineer_license ?? null,
                    'electrical_engineer_name' => $application->electrical_engineer_name ?? null,
                    'electrical_engineer_license' => $application->electrical_engineer_license ?? null,
                    'sanitary_engineer_name' => $application->sanitary_engineer_name ?? null,
                    'sanitary_engineer_license' => $application->sanitary_engineer_license ?? null,
                    // Hard Copy Submission Details
                    'hardcopy_submission_date' => $application->hardcopy_submission_date ?? null,
                    'hardcopy_instructions' => $application->hardcopy_instructions ?? null,
                    // CPDO Status
                    'cpdo_status' => $cpdoStatus,
                    'cpdo_remarks' => $cpdoRemarks
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading application details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading application details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get CPDO status for an application
     */
    public function getCPDOStatus($id)
    {
        try {
            $application = ApplicationDocument::find($id);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $application->cpdo_status ?? 'pending',
                    'remarks' => $application->cpdo_remarks ?? null,
                    'approved_at' => $application->cpdo_approved_at ?? null,
                    'approved_by' => $application->cpdo_approved_by ? User::find($application->cpdo_approved_by)?->name : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting CPDO status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving CPDO status'
            ], 500);
        }
    }

    /**
     * Submit CPDO decision (Approve/Reject)
     */
    public function submitCPDODecision(Request $request, $id)
    {
        Log::info('========== SUBMIT CPDO DECISION START ==========');
        Log::info('submitCPDODecision called', [
            'application_id' => $id,
            'decision' => $request->decision,
            'remarks' => $request->remarks,
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated'
        ]);

        $validator = Validator::make($request->all(), [
            'decision' => 'required|string|in:approved,rejected',
            'remarks' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $staff = auth()->user();
            $staff->load('profile');
            $position = $staff->profile ? $staff->profile->position : null;
            
            // Check if user is CPDO
            if ($position !== 'cpdo') {
                Log::error('Unauthorized: User is not CPDO', [
                    'user_id' => $staff->id,
                    'position' => $position
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Only CPDO staff can make this decision. Your position: ' . ($position ?? 'not set')
                ], 403);
            }
            
            $decision = $request->decision;
            $remarks = $request->remarks;
            $oldCPDOStatus = $application->cpdo_status ?? 'pending';
            
            // Update application with CPDO decision
            $application->cpdo_status = $decision;
            $application->cpdo_remarks = $remarks;
            $application->cpdo_approved_at = $decision === 'approved' ? now() : null;
            $application->cpdo_approved_by = $staff->id;
            $application->last_updated_by = $staff->id;
            $application->save();
            
            Log::info('CPDO decision saved', [
                'application_id' => $id,
                'decision' => $decision,
                'old_status' => $oldCPDOStatus,
                'new_status' => $decision
            ]);
            
            // Log the activity
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                $decision === 'approved' ? 'cpdo_approved' : 'cpdo_rejected',
                $oldCPDOStatus,
                $decision,
                $remarks ?? ($decision === 'approved' ? 'Application approved by CPDO' : 'Application rejected by CPDO'),
                $request->ip(),
                $request->userAgent()
            );
            
            // Send notification to applicant
            try {
                if ($decision === 'approved') {
                    $this->notificationService->notifyCPDOApproved($application, $staff);
                    Log::info('✅ CPDO approval notification sent to applicant');
                } else {
                    $this->notificationService->notifyCPDORejected($application, $staff, $remarks);
                    Log::info('✅ CPDO rejection notification sent to applicant');
                }
            } catch (\Exception $e) {
                Log::error('❌ Failed to send CPDO notification: ' . $e->getMessage());
            }
            
            Log::info('========== SUBMIT CPDO DECISION END (SUCCESS) ==========');
            
            return response()->json([
                'success' => true,
                'message' => $decision === 'approved' 
                    ? 'Application approved by CPDO. Other departments can now proceed with verification.' 
                    : 'Application rejected by CPDO.',
                'data' => [
                    'cpdo_status' => $decision,
                    'cpdo_remarks' => $remarks,
                    'cpdo_approved_at' => $decision === 'approved' ? now()->toISOString() : null
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('========== SUBMIT CPDO DECISION END (ERROR) ==========');
            Log::error('Error in submitCPDODecision: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error submitting CPDO decision: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get review activities for an application - FIXED to remove duplicates
     */
    public function getReviewActivities($id)
    {
        try {
            Log::info('Fetching review activities for application ID: ' . $id);
            
            $application = ApplicationDocument::find($id);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }
            
            $activities = [];
            
            if (class_exists('App\Models\ApplicationReviewActivity')) {
                $rawActivities = ApplicationReviewActivity::with('reviewer')
                    ->where('application_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $uniqueActivities = [];
                foreach ($rawActivities as $activity) {
                    $key = $activity->action . '_' . $activity->created_at->format('Y-m-d H:i:s');
                    if (!isset($uniqueActivities[$key])) {
                        $uniqueActivities[$key] = $activity;
                    }
                }
                
                $activities = collect(array_values($uniqueActivities))->map(function($activity) {
                    return [
                        'id' => $activity->id,
                        'action' => $activity->action,
                        'action_display' => $this->getActionDisplayText($activity),
                        'old_status' => $activity->old_status,
                        'new_status' => $activity->new_status,
                        'remarks' => $activity->remarks,
                        'reviewer_id' => $activity->reviewer_id,
                        'reviewer_name' => $activity->reviewer ? 
                            ($activity->reviewer->first_name . ' ' . $activity->reviewer->last_name) : 
                            'System',
                        'reviewer_role' => $activity->reviewer ? $activity->reviewer->role : null,
                        'created_at' => $activity->created_at,
                        'ip_address' => $activity->ip_address,
                        'user_agent' => $activity->user_agent
                    ];
                });
            }
            
            Log::info('Found ' . count($activities) . ' unique review activities');
            
            return response()->json([
                'success' => true,
                'activities' => $activities
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching review activities: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching activities: ' . $e->getMessage(),
                'activities' => []
            ], 500);
        }
    }

    /**
     * Get human-readable action display text
     */
    private function getActionDisplayText($activity)
    {
        $actionText = '';
        
        switch ($activity->action) {
            case 'application_submitted':
                $actionText = 'Application Submitted';
                break;
            case 'status_updated':
                $old = $activity->old_status ? $this->formatStatusForDisplay($activity->old_status) : 'Unknown';
                $new = $activity->new_status ? $this->formatStatusForDisplay($activity->new_status) : 'Unknown';
                $actionText = "Status changed from {$old} to {$new}";
                break;
            case 'document_verified':
                $actionText = 'Documents Verified';
                break;
            case 'document_rejected':
                $actionText = 'Documents Rejected';
                break;
            case 'hard_copy_received':
                $actionText = 'Hard Copy Received';
                break;
            case 'missing_documents_requested':
                $actionText = 'Missing Documents Requested';
                break;
            case 'note_added':
                $actionText = 'Note Added';
                break;
            case 'application_created':
                $actionText = 'Application Created';
                break;
            case 'application_deleted':
                $actionText = 'Application Deleted';
                break;
            case 'application_archived':
                $actionText = 'Application Archived';
                break;
            case 'application_restored':
                $actionText = 'Application Restored';
                break;
            case 'assessment_saved':
                $actionText = 'Assessment Saved';
                break;
            case 'assessment_completed':
                $actionText = 'Assessment Completed';
                break;
            case 'fsec_uploaded':
                $actionText = 'FSEC Document Uploaded';
                break;
            case 'fsec_deleted':
                $actionText = 'FSEC Document Deleted';
                break;
            case 'bfp_comments_added':
                $actionText = 'BFP Comments Added';
                break;
            case 'ownership_document_verified':
                $actionText = 'Ownership Document Verified';
                break;
            case 'ownership_document_unverified':
                $actionText = 'Ownership Document Unverified';
                break;
            case 'cpdo_approved':
                $actionText = 'CPDO Approved';
                break;
            case 'cpdo_rejected':
                $actionText = 'CPDO Rejected';
                break;
            default:
                $actionText = ucfirst(str_replace('_', ' ', $activity->action));
                break;
        }
        
        return $actionText;
    }

    /**
     * Format status for display (e.g., 'under-review' -> 'Under Review')
     */
    private function formatStatusForDisplay($status)
    {
        if (!$status) return 'Unknown';
        $statusMap = [
            'for-assessment' => 'For Assessment'
        ];
        return $statusMap[$status] ?? ucfirst(str_replace('-', ' ', $status));
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, $id)
    {
        Log::info('========== UPDATE STATUS START ==========');
        Log::info('updateStatus called', [
            'application_id' => $id,
            'status' => $request->status,
            'hardcopy_received' => $request->hardcopy_received,
            'remarks' => $request->remarks,
            'hardcopy_submission_date' => $request->hardcopy_submission_date,
            'hardcopy_instructions' => $request->hardcopy_instructions,
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated',
            'user_role' => auth()->user() ? auth()->user()->role : 'unknown'
        ]);

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,under-review,document-verification,for-assessment,approved,rejected,for-release,verified',
            'remarks' => 'nullable|string',
            'hardcopy_received' => 'sometimes|boolean',
            'hardcopy_submission_date' => 'nullable|string',
            'hardcopy_instructions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Check if CPDO has approved (unless the status is being set to rejected)
            $cpdoStatus = $application->cpdo_status ?? 'pending';
            if ($cpdoStatus !== 'approved' && $request->status !== 'rejected') {
                Log::warning('CPDO approval required before status update', [
                    'application_id' => $id,
                    'cpdo_status' => $cpdoStatus,
                    'requested_status' => $request->status
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'CPDO approval is required before changing application status. Please wait for CPDO to review and approve the application.'
                ], 403);
            }

            Log::info('Application found', [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'current_status' => $application->status,
                'current_hardcopy_status' => $application->hard_copy_received,
                'applicant_id' => $application->user_id,
                'applicant_email' => $application->user ? $application->user->email : 'no user',
                'applicant_name' => $application->user ? $application->user->first_name : 'unknown'
            ]);
            
            $staff = auth()->user();
            $oldStatus = $application->status;
            $newStatus = $request->status;
            $oldHardCopyStatus = $application->hard_copy_received;
            $newHardCopyStatus = $request->has('hardcopy_received') ? $request->hardcopy_received : $oldHardCopyStatus;
            
            $statusChanged = ($oldStatus !== $newStatus);
            $hardCopyChanged = ($oldHardCopyStatus != $newHardCopyStatus);
            
            Log::info('Status change', [
                'old' => $oldStatus,
                'new' => $newStatus,
                'changed' => $statusChanged ? 'YES' : 'NO'
            ]);
            
            Log::info('Hard copy status change', [
                'old' => $oldHardCopyStatus,
                'new' => $newHardCopyStatus,
                'changed' => $hardCopyChanged ? 'YES' : 'NO'
            ]);
            
            $application->status = $newStatus;
            
            if ($request->has('remarks') && $request->remarks) {
                $application->admin_notes = $request->remarks;
            }
            
            $application->last_updated_by = $staff->id;
            
            if ($request->has('hardcopy_received')) {
                $application->hard_copy_received = $newHardCopyStatus;
                
                if ($newHardCopyStatus && !$oldHardCopyStatus) {
                    $application->hard_copy_received_at = now();
                    Log::info('Setting hard_copy_received_at to now');
                }
            }
            
            // Save hard copy submission date and instructions when status is approved
            if ($newStatus === 'approved') {
                if ($request->has('hardcopy_submission_date') && $request->hardcopy_submission_date) {
                    $application->hardcopy_submission_date = $request->hardcopy_submission_date;
                    Log::info('Setting hardcopy_submission_date', ['date' => $request->hardcopy_submission_date]);
                }
                if ($request->has('hardcopy_instructions')) {
                    $application->hardcopy_instructions = $request->hardcopy_instructions;
                    Log::info('Setting hardcopy_instructions', ['instructions' => $request->hardcopy_instructions]);
                }
            }
            
            if ($newStatus === 'verified') {
                $application->verified_at = now();
                $application->verified_by = $staff->id;
                Log::info('Setting verified_at and verified_by');
            }
            
            if ($newStatus === 'rejected' && $request->has('remarks')) {
                $application->rejection_reason = $request->remarks;
                Log::info('Setting rejection_reason');
            }
            
            $application->save();
            
            Log::info('Application saved to database', [
                'new_status' => $application->status,
                'new_hardcopy_status' => $application->hard_copy_received,
                'hardcopy_submission_date' => $application->hardcopy_submission_date
            ]);

            // ========== AUTO-ARCHIVE WHEN STATUS CHANGES TO VERIFIED ==========
            $wasAutoArchived = false;
            if ($statusChanged && $newStatus === 'verified') {
                Log::info('Application status changed to VERIFIED - triggering auto-archive', [
                    'application_id' => $application->id,
                    'application_number' => $application->application_number
                ]);
                
                try {
                    // Archive the application automatically
                    $application->is_archived = true;
                    $application->archived_at = now();
                    $application->archived_by = $staff->id;
                    $application->archive_reason = 'Auto-archived: Application completed (status set to VERIFIED)';
                    $application->save();
                    $wasAutoArchived = true;
                    
                    Log::info('Application auto-archived successfully', [
                        'application_id' => $application->id,
                        'archived_by' => $staff->id,
                        'archived_at' => $application->archived_at
                    ]);
                    
                    // Log the auto-archive activity
                    $this->logReviewActivity(
                        $application->id,
                        $staff->id,
                        'application_archived',
                        $oldStatus,
                        $newStatus,
                        'Application auto-archived upon completion (status set to VERIFIED)',
                        $request->ip(),
                        $request->userAgent()
                    );
                    
                    // Create activity log entry
                    if (class_exists('App\Models\ActivityLog')) {
                        ActivityLog::create([
                            'user_id' => $staff->id,
                            'action' => 'application_auto_archived',
                            'description' => 'Application auto-archived upon completion',
                            'metadata' => json_encode([
                                'application_id' => $application->id,
                                'application_number' => $application->application_number,
                                'old_status' => $oldStatus,
                                'new_status' => $newStatus
                            ]),
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'status' => 'success'
                        ]);
                    }
                    
                } catch (\Exception $e) {
                    Log::error('Failed to auto-archive application on verified status', [
                        'application_id' => $application->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($statusChanged) {
                Log::info('Creating review activity for status change');
                
                $existingDuplicate = ApplicationReviewActivity::where('application_id', $application->id)
                    ->where('action', 'status_updated')
                    ->where('old_status', $oldStatus)
                    ->where('new_status', $newStatus)
                    ->where('created_at', '>=', now()->subSeconds(2))
                    ->exists();
                
                if (!$existingDuplicate) {
                    try {
                        $activity = ApplicationReviewActivity::create([
                            'application_id' => $application->id,
                            'reviewer_id' => $staff->id,
                            'action' => 'status_updated',
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'remarks' => $request->remarks ?? "Status changed from {$oldStatus} to {$newStatus}",
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent()
                        ]);
                        Log::info('Review activity created with ID: ' . ($activity ? $activity->id : 'null'));
                    } catch (\Exception $e) {
                        Log::error('Failed to log activity: ' . $e->getMessage());
                    }
                } else {
                    Log::info('Skipped duplicate activity creation');
                }
                
                try {
                    $this->notificationService->notifyApplicantStatusChange(
                        $application,
                        $oldStatus,
                        $newStatus,
                        $staff
                    );
                    Log::info('✓✓✓ STATUS CHANGE NOTIFICATION SENT ✓✓✓');
                    
                    if ($application->user && $application->user->email) {
                        Log::info("📧 ATTEMPTING TO SEND {$newStatus} EMAIL VIA GMAIL SERVICE TO {$application->user->email}");
                        
                        // Include hard copy submission details in the email for approved status
                        $emailSent = $this->gmailService->sendStatusEmail(
                            $application->user->email,
                            $newStatus,
                            $application->application_number,
                            $application->user->first_name,
                            $application->id,
                            [
                                'hardcopy_submission_date' => $application->hardcopy_submission_date,
                                'hardcopy_instructions' => $application->hardcopy_instructions
                            ]
                        );
                        
                        if ($emailSent) {
                            Log::info("✓✓✓ {$newStatus} EMAIL SENT SUCCESSFULLY TO {$application->user->email}");
                        } else {
                            Log::error("✗✗✗ FAILED TO SEND {$newStatus} EMAIL TO {$application->user->email}");
                        }
                    } else {
                        Log::error('Cannot send email: Applicant email not found');
                    }
                    
                } catch (\Exception $e) {
                    Log::error('✗✗✗ EXCEPTION when sending status email: ' . $e->getMessage());
                }
            }

            if ($hardCopyChanged && $newHardCopyStatus) {
                Log::info('ATTEMPTING TO SEND HARD COPY RECEIVED NOTIFICATION');
                
                $existingHardCopyDuplicate = ApplicationReviewActivity::where('application_id', $application->id)
                    ->where('action', 'hard_copy_received')
                    ->where('created_at', '>=', now()->subSeconds(2))
                    ->exists();
                
                if (!$existingHardCopyDuplicate) {
                    $this->logReviewActivity(
                        $application->id,
                        $staff->id,
                        'hard_copy_received',
                        null,
                        null,
                        'Hard copies marked as received',
                        $request->ip(),
                        $request->userAgent()
                    );
                }
                
                try {
                    $this->notificationService->notifyHardCopyReceived($application, $staff);
                    Log::info('✓✓✓ HARD COPY NOTIFICATION SENT ✓✓✓');
                    
                    if ($application->user && $application->user->email) {
                        Log::info("📧 ATTEMPTING TO SEND HARD COPY RECEIVED EMAIL VIA GMAIL SERVICE");
                        
                        $emailSent = $this->gmailService->sendStatusEmail(
                            $application->user->email,
                            $newStatus,
                            $application->application_number,
                            $application->user->first_name,
                            $application->id
                        );
                        
                        if ($emailSent) {
                            Log::info('✓✓✓ HARD COPY RECEIVED EMAIL SENT SUCCESSFULLY');
                        }
                    }
                    
                } catch (\Exception $e) {
                    Log::error('✗✗✗ EXCEPTION when sending hard copy notification: ' . $e->getMessage());
                }
            }

            Log::info('========== UPDATE STATUS END (SUCCESS) ==========');
            
            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully',
                'data' => [
                    'id' => $application->id,
                    'status' => $application->status,
                    'application_number' => $application->application_number,
                    'hard_copy_received' => $application->hard_copy_received,
                    'hard_copy_received_at' => $application->hard_copy_received_at,
                    'hardcopy_submission_date' => $application->hardcopy_submission_date,
                    'hardcopy_instructions' => $application->hardcopy_instructions,
                    'is_archived' => $application->is_archived,
                    'was_auto_archived' => $wasAutoArchived
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('========== UPDATE STATUS END (ERROR) ==========');
            Log::error('Error in updateStatus: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add note to application without changing status
     */
    public function addNote(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $staff = auth()->user();

            $existingNotes = $application->admin_notes;
            $newNote = "[" . now()->format('Y-m-d H:i') . "] " . $staff->first_name . " " . $staff->last_name . ": " . $request->note;
            $application->admin_notes = $existingNotes 
                ? $existingNotes . "\n\n" . $newNote 
                : $newNote;
            
            $application->last_updated_by = $staff->id;
            $application->save();

            $this->notificationService->notifyApplicantOfNote(
                $application,
                $request->note,
                $staff
            );

            try {
                if ($application->user && $application->user->email) {
                    Log::info("📧 ATTEMPTING TO SEND NOTE ADDED EMAIL TO {$application->user->email}");
                    
                    $emailSent = $this->gmailService->sendStatusEmail(
                        $application->user->email,
                        $application->status,
                        $application->application_number,
                        $application->user->first_name,
                        $application->id
                    );
                    
                    if ($emailSent) {
                        Log::info('✓✓✓ NOTE ADDED EMAIL SENT SUCCESSFULLY');
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send note email: ' . $e->getMessage());
            }

            $this->logReviewActivity(
                $application->id,
                $staff->id,
                'note_added',
                null,
                null,
                $request->note,
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding note: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error adding note: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify documents for an application
     */
    public function verifyDocuments(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'verified' => 'required|boolean',
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Check if CPDO has approved
            $cpdoStatus = $application->cpdo_status ?? 'pending';
            if ($cpdoStatus !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'CPDO approval is required before verifying documents.'
                ], 403);
            }

            $staff = auth()->user();
            
            if ($request->verified) {
                $action = 'document_verified';
                $message = 'Documents verified successfully';
                $newStatus = 'approved';
                
                $application->status = 'approved';
                $application->verified_at = now();
                $application->verified_by = $staff->id;
            } else {
                $action = 'document_rejected';
                $message = 'Documents rejected';
                $newStatus = 'rejected';
                
                $application->status = 'rejected';
                if ($request->has('remarks')) {
                    $application->rejection_reason = $request->remarks;
                }
            }
            
            $application->last_updated_by = $staff->id;
            
            if ($request->has('remarks')) {
                $existingNotes = $application->admin_notes;
                $newNote = "[" . now()->format('Y-m-d H:i') . "] " . $staff->first_name . " " . $staff->last_name . ": " . $request->remarks;
                $application->admin_notes = $existingNotes 
                    ? $existingNotes . "\n\n" . $newNote 
                    : $newNote;
            }
            
            $application->save();
            
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                $action,
                null,
                $newStatus,
                $request->remarks ?? null,
                $request->ip(),
                $request->userAgent()
            );
            
            try {
                $this->notificationService->notifyApplicantStatusChange(
                    $application,
                    $application->status,
                    $newStatus,
                    $staff
                );
                
                if ($application->user && $application->user->email) {
                    $emailSent = $this->gmailService->sendStatusEmail(
                        $application->user->email,
                        $newStatus,
                        $application->application_number,
                        $application->user->first_name,
                        $application->id
                    );
                    
                    if ($emailSent) {
                        Log::info("✓✓✓ DOCUMENT VERIFICATION EMAIL SENT TO {$application->user->email}");
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send document verification email: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'status' => $newStatus,
                    'verified' => $request->verified
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error verifying documents: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error verifying documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark hard copies as received
     */
    public function markHardCopyReceived(Request $request, $id)
    {
        Log::info('========== MARK HARD COPY RECEIVED CALLED ==========');
        Log::info('Parameters:', [
            'application_id' => $id,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown',
            'user_name' => auth()->user() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : 'unknown'
        ]);

        try {
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                Log::error('❌ Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            Log::info('✅ Application found', [
                'id' => $application->id,
                'number' => $application->application_number,
                'applicant_id' => $application->user_id,
                'applicant_email' => $application->user->email ?? 'unknown',
                'applicant_name' => $application->user ? $application->user->first_name . ' ' . $application->user->last_name : 'unknown',
                'current_hard_copy_status' => $application->hard_copy_received
            ]);

            $staff = auth()->user();

            if ($application->hard_copy_received) {
                Log::info('Hard copy already marked as received');
                return response()->json([
                    'success' => true,
                    'message' => 'Hard copies already marked as received',
                    'data' => [
                        'hard_copy_received' => true,
                        'hard_copy_received_at' => $application->hard_copy_received_at
                    ]
                ]);
            }

            $application->hard_copy_received = true;
            $application->hard_copy_received_at = now();
            $application->last_updated_by = $staff->id;
            $application->save();

            Log::info('✅ Application updated', [
                'hard_copy_received' => $application->hard_copy_received,
                'hard_copy_received_at' => $application->hard_copy_received_at,
                'last_updated_by' => $application->last_updated_by
            ]);

            $existingDuplicate = ApplicationReviewActivity::where('application_id', $application->id)
                ->where('action', 'hard_copy_received')
                ->where('created_at', '>=', now()->subSeconds(2))
                ->exists();
            
            if (!$existingDuplicate) {
                $this->logReviewActivity(
                    $application->id,
                    $staff->id,
                    'hard_copy_received',
                    null,
                    null,
                    'Hard copies marked as received',
                    $request->ip(),
                    $request->userAgent()
                );
                Log::info('✅ Review activity logged');
            } else {
                Log::info('Skipped duplicate hard copy activity');
            }

            try {
                $this->notificationService->notifyHardCopyReceived($application, $staff);
                Log::info('✅ Notification service called successfully');
                
                if ($application->user && $application->user->email) {
                    try {
                        Log::info("📧 ATTEMPTING TO SEND HARD COPY RECEIVED EMAIL TO {$application->user->email}");
                        
                        $emailSent = $this->gmailService->sendStatusEmail(
                            $application->user->email,
                            $application->status,
                            $application->application_number,
                            $application->user->first_name,
                            $application->id
                        );
                        
                        if ($emailSent) {
                            Log::info('✓✓✓ HARD COPY RECEIVED EMAIL SENT SUCCESSFULLY');
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send hard copy email: ' . $e->getMessage());
                    }
                }
                
            } catch (\Exception $e) {
                Log::error('❌ Error calling notification service: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
            }

            Log::info('========== MARK HARD COPY RECEIVED COMPLETED ==========');

            return response()->json([
                'success' => true,
                'message' => 'Hard copies marked as received successfully',
                'data' => [
                    'hard_copy_received' => true,
                    'hard_copy_received_at' => now()->toDateTimeString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('========== MARK HARD COPY RECEIVED ERROR ==========');
            Log::error('Error: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error marking hard copies: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request missing documents from applicant
     */
    public function requestMissingDocuments(Request $request, $id)
    {
        Log::info('========== REQUEST MISSING DOCUMENTS START ==========');
        Log::info('requestMissingDocuments called', [
            'application_id' => $id,
            'documents' => $request->documents,
            'remarks' => $request->remarks,
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated'
        ]);

        $validator = Validator::make($request->all(), [
            'documents' => 'required|array|min:1',
            'documents.*' => 'required|string',
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $staff = auth()->user();
            
            $documentList = implode("\n", array_map(function($doc) {
                return "• " . $doc;
            }, $request->documents));
            
            $noteMessage = "Missing documents requested:\n\n" . $documentList;
            
            if ($request->remarks) {
                $noteMessage .= "\n\nRemarks: " . $request->remarks;
            }
            
            $existingNotes = $application->admin_notes;
            $newNote = "[" . now()->format('Y-m-d H:i') . "] " . $staff->first_name . " " . $staff->last_name . " requested missing documents:\n" . $documentList;
            
            if ($request->remarks) {
                $newNote .= "\nRemarks: " . $request->remarks;
            }
            
            $application->admin_notes = $existingNotes 
                ? $existingNotes . "\n\n" . $newNote 
                : $newNote;
            
            $application->last_updated_by = $staff->id;
            $application->save();

            if ($application->user && $application->user->email) {
                Log::info('📧 ATTEMPTING TO SEND MISSING DOCUMENTS EMAIL VIA GMAIL SERVICE TO ' . $application->user->email);
                
                $emailSent = $this->gmailService->sendMissingDocumentsEmail(
                    $application->user->email,
                    $application->application_number,
                    $application->user->first_name,
                    $request->documents,
                    $application->id,
                    $request->remarks
                );
                
                if ($emailSent) {
                    Log::info('✓✓✓ MISSING DOCUMENTS EMAIL SENT SUCCESSFULLY TO ' . $application->user->email);
                } else {
                    Log::error('✗✗✗ FAILED TO SEND MISSING DOCUMENTS EMAIL TO ' . $application->user->email);
                }
            } else {
                Log::error('Cannot send email: Applicant email not found');
            }

            $existingDuplicate = ApplicationReviewActivity::where('application_id', $application->id)
                ->where('action', 'missing_documents_requested')
                ->where('created_at', '>=', now()->subSeconds(2))
                ->exists();
            
            if (!$existingDuplicate) {
                $this->logReviewActivity(
                    $application->id,
                    $staff->id,
                    'missing_documents_requested',
                    $application->status,
                    $application->status,
                    "Requested missing documents: " . implode(", ", $request->documents),
                    $request->ip(),
                    $request->userAgent()
                );
            }

            Log::info('========== REQUEST MISSING DOCUMENTS END (SUCCESS) ==========');

            return response()->json([
                'success' => true,
                'message' => 'Missing documents request sent successfully',
                'data' => [
                    'document_count' => count($request->documents)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('========== REQUEST MISSING DOCUMENTS END (ERROR) ==========');
            Log::error('Error in requestMissingDocuments: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an application
     */
    public function destroy(Request $request, $id)
    {
        try {
            $application = ApplicationDocument::find($id);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }
            
            $staff = auth()->user();
            
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                'application_deleted',
                $application->status,
                null,
                'Application deleted',
                $request->ip(),
                $request->userAgent()
            );
            
            $application->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting application: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting application'
            ], 500);
        }
    }

    /**
     * Export applications (CSV) - excluding archived
     */
    public function export()
    {
        try {
            Log::info('Export applications started');
            
            $applications = ApplicationDocument::with(['user', 'lastUpdatedBy'])
                ->where('is_archived', false)
                ->whereIn('status', [
                    'pending', 
                    'under-review', 
                    'document-verification',
                    'for-assessment',
                    'approved', 
                    'rejected', 
                    'for-release', 
                    'verified'
                ])
                ->get();
            
            Log::info('Found ' . $applications->count() . ' applications to export');
            
            $filename = 'applications_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($applications) {
                $handle = fopen('php://output', 'w');
                
                fputcsv($handle, [
                    'Application Number',
                    'Applicant Name',
                    'Email',
                    'Phone',
                    'Status',
                    'Hard Copy Received',
                    'Date Submitted',
                    'Last Updated By',
                    'Google Drive Link'
                ]);
                
                foreach ($applications as $app) {
                    fputcsv($handle, [
                        $app->application_number,
                        $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                        $app->user ? $app->user->email : '',
                        $app->user ? $app->user->phone_number : '',
                        ucfirst(str_replace('-', ' ', $app->status)),
                        $app->hard_copy_received ? 'Yes' : 'No',
                        $app->created_at ? $app->created_at->format('Y-m-d') : '',
                        $app->lastUpdatedBy ? $app->lastUpdatedBy->first_name . ' ' . $app->lastUpdatedBy->last_name : 'N/A',
                        $app->google_drive_link
                    ]);
                }
                
                fclose($handle);
            };
            
            Log::info('Export completed successfully');
            return response()->streamDownload($callback, $filename, $headers);
            
        } catch (\Exception $e) {
            Log::error('Error exporting applications: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error exporting applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export archived applications (CSV)
     */
    public function exportArchived()
    {
        try {
            Log::info('Export archived applications started');

            $applications = ApplicationDocument::with(['user', 'lastUpdatedBy'])
                ->where('is_archived', true)
                ->get();

            Log::info('Found ' . $applications->count() . ' archived applications to export');

            $filename = 'archived_applications_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($applications) {
                $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    'Application Number',
                    'Applicant Name',
                    'Email',
                    'Phone',
                    'Status',
                    'Hard Copy Received',
                    'Date Submitted',
                    'Archived Date',
                    'Archived By',
                    'Google Drive Link'
                ]);

                foreach ($applications as $app) {
                    fputcsv($handle, [
                        $app->application_number,
                        $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                        $app->user ? $app->user->email : '',
                        $app->user ? $app->user->phone_number : '',
                        ucfirst(str_replace('-', ' ', $app->status)),
                        $app->hard_copy_received ? 'Yes' : 'No',
                        $app->created_at ? $app->created_at->format('Y-m-d') : '',
                        $app->archived_at ? $app->archived_at->format('Y-m-d') : '',
                        $app->archivedBy ? $app->archivedBy->first_name . ' ' . $app->archivedBy->last_name : 'N/A',
                        $app->google_drive_link
                    ]);
                }

                fclose($handle);
            };

            Log::info('Archived export completed successfully');
            return response()->streamDownload($callback, $filename, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting archived applications: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error exporting archived applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive an application
     */
    public function archive($id)
    {
        try {
            $application = ApplicationDocument::findOrFail($id);
            
            if ($application->is_archived) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application is already archived'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $application->update([
                'is_archived' => true,
                'archived_at' => now(),
                'archived_by' => Auth::id(),
                'archive_reason' => request('reason', 'Archived by staff')
            ]);
            
            $this->logReviewActivity(
                $application->id,
                Auth::id(),
                'application_archived',
                $application->status,
                null,
                'Application archived: ' . request('reason', 'Archived by staff'),
                request()->ip(),
                request()->userAgent()
            );
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'application_archived',
                'description' => 'Archived application',
                'metadata' => json_encode([
                    'application_id' => $application->id,
                    'application_number' => $application->application_number,
                    'reason' => request('reason', 'Archived by staff')
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'success'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Application archived successfully',
                'application' => $application
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error archiving application: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore an archived application
     */
    public function restore($id)
    {
        try {
            $application = ApplicationDocument::where('is_archived', true)
                ->where('id', $id)
                ->firstOrFail();
            
            DB::beginTransaction();
            
            $application->update([
                'is_archived' => false,
                'archived_at' => null,
                'archived_by' => null,
                'archive_reason' => null
            ]);
            
            $this->logReviewActivity(
                $application->id,
                Auth::id(),
                'application_restored',
                null,
                $application->status,
                'Application restored from archive',
                request()->ip(),
                request()->userAgent()
            );
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'application_restored',
                'description' => 'Restored archived application',
                'metadata' => json_encode([
                    'application_id' => $application->id,
                    'application_number' => $application->application_number
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'success'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Application restored successfully',
                'application' => $application
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring application: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore multiple archived applications
     */
    public function restoreMultiple(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:application_documents,id'
            ]);
            
            DB::beginTransaction();
            
            $count = ApplicationDocument::whereIn('id', $request->ids)
                ->where('is_archived', true)
                ->update([
                    'is_archived' => false,
                    'archived_at' => null,
                    'archived_by' => null,
                    'archive_reason' => null
                ]);
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'applications_restored_bulk',
                'description' => "Restored {$count} archived applications",
                'metadata' => json_encode([
                    'application_ids' => $request->ids,
                    'count' => $count
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'success'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'restored_count' => $count,
                'message' => "Successfully restored {$count} application(s)"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring multiple applications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archived applications list (for the archive page)
     */
    public function getArchivedApplications(Request $request)
    {
        try {
            $query = ApplicationDocument::with(['user', 'archivedBy'])
                ->where('is_archived', true)
                ->where('archived_at', '!=', null);
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('application_number', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('first_name', 'LIKE', "%{$search}%")
                                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                                    ->orWhere('email', 'LIKE', "%{$search}%");
                      });
                });
            }
            
            if ($request->filled('date')) {
                switch ($request->date) {
                    case 'today':
                        $query->whereDate('archived_at', today());
                        break;
                    case 'week':
                        $query->where('archived_at', '>=', now()->subDays(7));
                        break;
                    case 'month':
                        $query->whereMonth('archived_at', now()->month)
                              ->whereYear('archived_at', now()->year);
                        break;
                    case 'year':
                        $query->whereYear('archived_at', now()->year);
                        break;
                }
            }
            
            $query->orderBy('archived_at', 'desc');
            
            $stats = [
                'total' => ApplicationDocument::where('is_archived', true)->count(),
                'this_month' => ApplicationDocument::where('is_archived', true)
                    ->whereMonth('archived_at', now()->month)
                    ->whereYear('archived_at', now()->year)
                    ->count()
            ];
            
            $perPage = $request->get('per_page', 10);
            $applications = $query->paginate($perPage);
            
            $applications->getCollection()->transform(function($app) {
                return [
                    'id' => $app->id,
                    'application_number' => $app->application_number,
                    'applicant_name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'N/A',
                    'applicant_email' => $app->user ? $app->user->email : 'N/A',
                    'application_type' => $app->application_type,
                    'archived_at' => $app->archived_at,
                    'archived_by_name' => $app->archivedBy ? $app->archivedBy->first_name . ' ' . $app->archivedBy->last_name : null,
                    'archive_reason' => $app->archive_reason
                ];
            });
            
            return response()->json([
                'success' => true,
                'applications' => $applications->items(),
                'pagination' => [
                    'current_page' => $applications->currentPage(),
                    'last_page' => $applications->lastPage(),
                    'per_page' => $applications->perPage(),
                    'total' => $applications->total(),
                    'from' => $applications->firstItem(),
                    'to' => $applications->lastItem()
                ],
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching archived applications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch archived applications'
            ], 500);
        }
    }

    /**
     * Save assessment fees for an application
     */
    public function saveAssessment(Request $request, $id)
    {
        Log::info('========== SAVE ASSESSMENT START ==========');
        Log::info('saveAssessment called', [
            'application_id' => $id,
            'data' => $request->all(),
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated'
        ]);

        $validator = Validator::make($request->all(), [
            'line_grade' => 'nullable|numeric|min:0',
            'building_fee' => 'nullable|numeric|min:0',
            'sanitary_fee' => 'nullable|numeric|min:0',
            'mechanical_fee' => 'nullable|numeric|min:0',
            'electrical_fee' => 'nullable|numeric|min:0',
            'penalties_fines' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'assessment_notes' => 'nullable|string',
            'additional_fees' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $staff = auth()->user();
            
            // Find or create assessment record
            $assessment = AssessmentFee::where('application_id', $id)->first();
            
            if (!$assessment) {
                $assessment = new AssessmentFee();
                $assessment->application_id = $id;
            }
            
            // Prepare assessment data for notification
            $assessmentData = [
                'line_grade' => $request->line_grade,
                'building_fee' => $request->building_fee,
                'sanitary_fee' => $request->sanitary_fee,
                'mechanical_fee' => $request->mechanical_fee,
                'electrical_fee' => $request->electrical_fee,
                'penalties_fines' => $request->penalties_fines,
                'total_amount' => $request->total_amount,
                'assessment_notes' => $request->assessment_notes,
                'additional_fees' => $request->additional_fees
            ];
            
            // Update assessment data
            $assessment->line_grade = $request->line_grade;
            $assessment->building_fee = $request->building_fee;
            $assessment->sanitary_fee = $request->sanitary_fee;
            $assessment->mechanical_fee = $request->mechanical_fee;
            $assessment->electrical_fee = $request->electrical_fee;
            $assessment->penalties_fines = $request->penalties_fines;
            $assessment->total_amount = $request->total_amount;
            $assessment->assessment_notes = $request->assessment_notes;
            $assessment->additional_fees = $request->additional_fees ? json_encode($request->additional_fees) : null;
            $assessment->assessed_by = $staff->id;
            $assessment->assessed_at = now();
            $assessment->save();
            
            Log::info('Assessment saved successfully', [
                'application_id' => $id,
                'assessment_id' => $assessment->id,
                'total_amount' => $assessment->total_amount
            ]);
            
            // Update application status to 'for-assessment' if not already
            $oldStatus = $application->status;
            
            if ($application->status !== 'for-assessment') {
                $application->status = 'for-assessment';
                $application->last_updated_by = $staff->id;
                $application->save();
                
                Log::info('Application status changed to for-assessment', [
                    'old_status' => $oldStatus,
                    'new_status' => 'for-assessment'
                ]);
                
                // Log the status change activity
                $this->logReviewActivity(
                    $application->id,
                    $staff->id,
                    'status_updated',
                    $oldStatus,
                    'for-assessment',
                    "Application marked for assessment. Total fee: ₱" . number_format($assessment->total_amount, 2),
                    $request->ip(),
                    $request->userAgent()
                );
            }
            
            // Log assessment saved activity
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                'assessment_saved',
                null,
                $application->status,
                "Assessment saved with total fee: ₱" . number_format($assessment->total_amount, 2),
                $request->ip(),
                $request->userAgent()
            );
            
            // Send assessment completion notification to applicant
            try {
                $this->notificationService->notifyAssessmentCompleted(
                    $application,
                    $oldStatus,
                    'for-assessment',
                    $staff,
                    $assessmentData
                );
                Log::info('✓✓✓ ASSESSMENT NOTIFICATION SENT TO APPLICANT ✓✓✓');
            } catch (\Exception $e) {
                Log::error('Failed to send assessment notification: ' . $e->getMessage());
            }
            
            Log::info('========== SAVE ASSESSMENT END (SUCCESS) ==========');
            
            return response()->json([
                'success' => true,
                'message' => 'Assessment saved successfully and applicant notified',
                'data' => [
                    'assessment' => $assessment,
                    'status' => $application->status,
                    'notification_sent' => true
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('========== SAVE ASSESSMENT END (ERROR) ==========');
            Log::error('Error in saveAssessment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving assessment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assessment data for an application
     */
    public function getAssessment($id)
    {
        try {
            $assessment = AssessmentFee::where('application_id', $id)->first();
            
            if (!$assessment) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No assessment found for this application'
                ]);
            }
            
            // Parse additional_fees if it's stored as JSON string
            $additionalFees = $assessment->additional_fees;
            if (is_string($additionalFees)) {
                $additionalFees = json_decode($additionalFees, true);
            }
            
            $assessmentData = $assessment->toArray();
            $assessmentData['additional_fees'] = $additionalFees;
            
            return response()->json([
                'success' => true,
                'data' => $assessmentData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting assessment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving assessment'
            ], 500);
        }
    }

    /**
     * Verify or unverify an ownership document (for Assessor and Treasurer)
     * NOTE: This does NOT require CPDO approval - Assessor and Treasurer can verify independently
     */
    public function verifyOwnershipDocument(Request $request, $id)
    {
        try {
            Log::info('========== VERIFY OWNERSHIP DOCUMENT START ==========');
            Log::info('verifyOwnershipDocument called', [
                'application_id' => $id,
                'document_key' => $request->document_key,
                'verified' => $request->verified,
                'user' => auth()->user() ? auth()->user()->email : 'not authenticated',
                'user_id' => auth()->id()
            ]);

            $request->validate([
                'document_key' => 'required|string|in:tct_link,tax_declaration_link,current_tax_receipt_link,spa_link',
                'verified' => 'required|boolean'
            ]);

            $application = ApplicationDocument::find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $staff = auth()->user();
            $staff->load('profile');
            $position = $staff->profile ? $staff->profile->position : null;
            
            Log::info('User position', ['position' => $position]);
            
            $documentKey = $request->document_key;
            $isVerified = $request->verified;
            
            // Get or create ownership verification record
            $ownershipVerification = OwnershipVerification::firstOrCreate(
                ['application_id' => $id],
                [
                    'is_owner' => true,
                    'assessor_status' => 'pending',
                    'treasurer_status' => 'pending'
                ]
            );
            
            $now = now();
            $responseMessage = '';
            
            // Handle verification based on document type and user position
            switch ($documentKey) {
                case 'tct_link':
                    // Only Assessor can verify TCT
                    if ($position !== 'assessor') {
                        Log::error('Unauthorized: User is not Assessor', ['position' => $position]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Only the Assessor can verify TCT/Deed of Sale documents.'
                        ], 403);
                    }
                    
                    $ownershipVerification->assessor_status = $isVerified ? 'approved' : 'pending';
                    $ownershipVerification->assessor_verified_at = $isVerified ? $now : null;
                    $ownershipVerification->assessor_remarks = $isVerified ? null : ($request->remarks ?? 'Verification removed');
                    $responseMessage = $isVerified ? 'TCT/Deed of Sale verified successfully' : 'TCT/Deed of Sale verification removed';
                    Log::info('TCT verification updated', ['verified' => $isVerified, 'assessor_status' => $ownershipVerification->assessor_status]);
                    break;
                    
                case 'tax_declaration_link':
                    // Only Treasurer can verify Tax Declaration
                    if ($position !== 'treasurer') {
                        Log::error('Unauthorized: User is not Treasurer', ['position' => $position]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Only the Treasurer can verify Tax Declaration documents.'
                        ], 403);
                    }
                    
                    $ownershipVerification->treasurer_status = $isVerified ? 'approved' : 'pending';
                    $ownershipVerification->treasurer_verified_at = $isVerified ? $now : null;
                    $ownershipVerification->treasurer_remarks = $isVerified ? null : ($request->remarks ?? 'Verification removed');
                    $responseMessage = $isVerified ? 'Tax Declaration verified successfully' : 'Tax Declaration verification removed';
                    Log::info('Tax Declaration verification updated', ['verified' => $isVerified, 'treasurer_status' => $ownershipVerification->treasurer_status]);
                    break;
                    
                case 'current_tax_receipt_link':
                    // Only Treasurer can verify Current Tax Receipt
                    if ($position !== 'treasurer') {
                        Log::error('Unauthorized: User is not Treasurer', ['position' => $position]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Only the Treasurer can verify Current Tax Receipt documents.'
                        ], 403);
                    }
                    
                    // Store tax receipt verification in treasurer remarks
                    $currentRemarks = $ownershipVerification->treasurer_remarks ?? '';
                    if ($isVerified) {
                        $newRemark = "Tax Receipt verified on " . $now->format('Y-m-d H:i:s');
                        $ownershipVerification->treasurer_remarks = $currentRemarks 
                            ? $currentRemarks . "\n" . $newRemark 
                            : $newRemark;
                        Log::info('Tax Receipt verification added', ['remark' => $newRemark]);
                    } else {
                        // Remove the verification remark
                        $ownershipVerification->treasurer_remarks = preg_replace(
                            '/Tax Receipt verified on \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\n?/', 
                            '', 
                            $currentRemarks
                        );
                        $ownershipVerification->treasurer_remarks = trim($ownershipVerification->treasurer_remarks);
                        if (empty($ownershipVerification->treasurer_remarks)) {
                            $ownershipVerification->treasurer_remarks = null;
                        }
                        Log::info('Tax Receipt verification removed');
                    }
                    $responseMessage = $isVerified ? 'Current Tax Receipt verified successfully' : 'Current Tax Receipt verification removed';
                    break;
                    
                case 'spa_link':
                    // SPA cannot be verified by staff (only admin)
                    Log::error('Unauthorized: SPA verification attempted by staff');
                    return response()->json([
                        'success' => false,
                        'message' => 'Special Power of Attorney (SPA) can only be verified by an administrator.'
                    ], 403);
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid document type'
                    ], 400);
            }
            
            $ownershipVerification->save();
            
            // Log the activity
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                $isVerified ? 'ownership_document_verified' : 'ownership_document_unverified',
                null,
                null,
                ($isVerified ? 'Verified' : 'Unverified') . ' ' . str_replace('_', ' ', $documentKey) . ' as ' . $position,
                $request->ip(),
                $request->userAgent()
            );
            
            Log::info('Ownership document verification updated successfully', [
                'application_id' => $id,
                'document_key' => $documentKey,
                'verified' => $isVerified,
                'verified_by' => $staff->id,
                'position' => $position
            ]);
            
            Log::info('========== VERIFY OWNERSHIP DOCUMENT END (SUCCESS) ==========');
            
            return response()->json([
                'success' => true,
                'message' => $responseMessage,
                'data' => [
                    'document_key' => $documentKey,
                    'verified' => $isVerified,
                    'verified_by' => $staff->first_name . ' ' . $staff->last_name,
                    'verified_at' => $now->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('========== VERIFY OWNERSHIP DOCUMENT END (ERROR) ==========');
            Log::error('Error verifying ownership document: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log review activity for an application
     */
    private function logReviewActivity($applicationId, $reviewerId, $action, $oldStatus = null, $newStatus = null, $remarks = null, $ipAddress = null, $userAgent = null)
    {
        try {
            if (!Schema::hasTable('application_review_activities')) {
                Log::warning('Application review activities table does not exist');
                return null;
            }
            
            // Check for duplicate within last 2 seconds
            $duplicate = ApplicationReviewActivity::where('application_id', $applicationId)
                ->where('action', $action)
                ->where('created_at', '>=', now()->subSeconds(2))
                ->exists();
            
            if ($duplicate) {
                Log::info('Skipping duplicate activity: ' . $action);
                return null;
            }
            
            return ApplicationReviewActivity::create([
                'application_id' => $applicationId,
                'reviewer_id' => $reviewerId,
                'action' => $action,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'remarks' => $remarks,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging review activity: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get ownership data for an application (Staff view)
     */
    public function getOwnershipData($id)
    {
        try {
            $application = ApplicationDocument::with('ownershipVerification')->find($id);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }
            
            $ownership = $application->ownershipVerification;
            
            if (!$ownership) {
                return response()->json([
                    'success' => true,
                    'data' => null
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'is_owner' => $ownership->is_owner,
                    'tct_link' => $ownership->tct_link,
                    'tax_declaration_link' => $ownership->tax_declaration_link,
                    'current_tax_receipt_link' => $ownership->current_tax_receipt_link,
                    'spa_link' => $ownership->spa_link
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load ownership data: ' . $e->getMessage()
            ], 500);
        }
    }
}