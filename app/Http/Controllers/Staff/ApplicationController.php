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
use App\Models\ClientSatisfactionSurvey;
use App\Services\NotificationService;
use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\CPDORating; 

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
     * ONLY CPDO staff can submit. Decision is FINAL and cannot be changed.
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

            // Check if decision has already been made
            if ($application->cpdo_status !== null && $application->cpdo_status !== 'pending') {
                Log::error('CPDO decision already made', [
                    'application_id' => $id,
                    'current_status' => $application->cpdo_status
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'A CPDO decision has already been made for this application. Decisions are final and cannot be changed.'
                ], 403);
            }

            $staff = auth()->user();
            $staff->load('profile');
            $position = $staff->profile ? $staff->profile->position : null;
            
            // Check if user is CPDO - ONLY CPDO can submit decisions
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
            $application->cpdo_approved_at = $decision === 'approved' ? now() : ($decision === 'rejected' ? now() : null);
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
                    'cpdo_approved_at' => now()->toISOString()
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
 * Save CPDO assessment fees after approval
 */
public function saveCPDOAssessment(Request $request, $id)
{
    Log::info('========== SAVE CPDO ASSESSMENT START ==========');
    Log::info('saveCPDOAssessment called', [
        'application_id' => $id,
        'data' => $request->all(),
        'user' => auth()->user() ? auth()->user()->email : 'not authenticated'
    ]);

    $validator = Validator::make($request->all(), [
        'assessment_date' => 'required|date',
        'zonal_location_fee' => 'nullable|numeric|min:0',
        'palc_fee' => 'nullable|numeric|min:0',
        'development_permit_fee' => 'nullable|numeric|min:0',
        'alteration_permit_fee' => 'nullable|numeric|min:0',
        'site_zoning_certificate_fee' => 'nullable|numeric|min:0',
        'total_cpdo_amount' => 'nullable|numeric|min:0',
        'cpdo_assessment_notes' => 'nullable|string',
        'cpdo_additional_fees' => 'nullable|array'
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
        
        // Only CPDO can save this assessment
        if ($position !== 'cpdo') {
            Log::error('Unauthorized: User is not CPDO', ['position' => $position]);
            return response()->json([
                'success' => false,
                'message' => 'Only CPDO staff can save this assessment.'
            ], 403);
        }

        // Calculate total
        $total = ($request->zonal_location_fee ?? 0) +
                 ($request->palc_fee ?? 0) +
                 ($request->development_permit_fee ?? 0) +
                 ($request->alteration_permit_fee ?? 0) +
                 ($request->site_zoning_certificate_fee ?? 0);
        
        // Add additional fees
        $additionalFeesTotal = 0;
        $additionalFees = $request->cpdo_additional_fees ?? [];
        foreach ($additionalFees as $fee) {
            $additionalFeesTotal += $fee['amount'] ?? 0;
        }
        $total += $additionalFeesTotal;
        
        // Save CPDO assessment data to application
        $application->cpdo_assessment_date = $request->assessment_date;
        $application->cpdo_zonal_location_fee = $request->zonal_location_fee;
        $application->cpdo_palc_fee = $request->palc_fee;
        $application->cpdo_development_permit_fee = $request->development_permit_fee;
        $application->cpdo_alteration_permit_fee = $request->alteration_permit_fee;
        $application->cpdo_site_zoning_certificate_fee = $request->site_zoning_certificate_fee;
        $application->cpdo_total_amount = $total;
        $application->cpdo_assessment_notes = $request->cpdo_assessment_notes;
        $application->cpdo_additional_fees = json_encode($additionalFees);
        $application->cpdo_assessed_by = $staff->id;
        $application->cpdo_assessed_at = now();
        $application->save();
        
        Log::info('CPDO Assessment saved successfully', [
            'application_id' => $id,
            'total_amount' => $total
        ]);
        
        // Log the activity
        $this->logReviewActivity(
            $application->id,
            $staff->id,
            'cpdo_assessment_saved',
            null,
            $application->status,
            "CPDO assessment saved with total fee: ₱" . number_format($total, 2),
            $request->ip(),
            $request->userAgent()
        );
        
        // ========== SEND EMAIL NOTIFICATION TO APPLICANT ==========
        try {
            // Prepare assessment data for email
            $assessmentData = [
                'assessment_date' => $request->assessment_date,
                'zonal_location_fee' => $request->zonal_location_fee,
                'palc_fee' => $request->palc_fee,
                'development_permit_fee' => $request->development_permit_fee,
                'alteration_permit_fee' => $request->alteration_permit_fee,
                'site_zoning_certificate_fee' => $request->site_zoning_certificate_fee,
                'total_cpdo_amount' => $total,
                'cpdo_assessment_notes' => $request->cpdo_assessment_notes,
                'cpdo_additional_fees' => $additionalFees,
                'cpdo_assessed_by' => $staff->first_name . ' ' . $staff->last_name
            ];
            
            $applicantEmail = $application->user->email;
            $applicantName = $application->user->first_name . ' ' . $application->user->last_name;
            $applicationNumber = $application->application_number;
            
            Log::info('📧 Attempting to send CPDO assessment email to applicant', [
                'to' => $applicantEmail,
                'application_number' => $applicationNumber,
                'total_amount' => $total
            ]);
            
            $emailSent = $this->gmailService->sendCPDOAssessmentEmail(
                $applicantEmail,
                $applicantName,
                $applicationNumber,
                $assessmentData,
                $application->id
            );
            
            if ($emailSent) {
                Log::info('✅ CPDO assessment email sent successfully to ' . $applicantEmail);
            } else {
                Log::error('❌ Failed to send CPDO assessment email to ' . $applicantEmail);
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Exception sending CPDO assessment email: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
        
        return response()->json([
            'success' => true,
            'message' => 'CPDO assessment saved successfully and applicant notified',
            'data' => [
                'total_amount' => $total
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('========== SAVE CPDO ASSESSMENT END (ERROR) ==========');
        Log::error('Error in saveCPDOAssessment: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error saving CPDO assessment: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get CPDO assessment data for an application
     */
    public function getCPDOAssessment($id)
    {
        try {
            $application = ApplicationDocument::find($id);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }
            
            $additionalFees = [];
            if ($application->cpdo_additional_fees) {
                $additionalFees = json_decode($application->cpdo_additional_fees, true) ?: [];
            }
            
            $assessedByName = null;
            if ($application->cpdo_assessed_by) {
                $assessor = User::find($application->cpdo_assessed_by);
                if ($assessor) {
                    $assessedByName = $assessor->first_name . ' ' . $assessor->last_name;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'assessment_date' => $application->cpdo_assessment_date,
                    'zonal_location_fee' => $application->cpdo_zonal_location_fee,
                    'palc_fee' => $application->cpdo_palc_fee,
                    'development_permit_fee' => $application->cpdo_development_permit_fee,
                    'alteration_permit_fee' => $application->cpdo_alteration_permit_fee,
                    'site_zoning_certificate_fee' => $application->cpdo_site_zoning_certificate_fee,
                    'total_cpdo_amount' => $application->cpdo_total_amount,
                    'cpdo_assessment_notes' => $application->cpdo_assessment_notes,
                    'cpdo_additional_fees' => $additionalFees,
                    'cpdo_assessed_by' => $assessedByName,
                    'cpdo_assessed_at' => $application->cpdo_assessed_at
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting CPDO assessment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving CPDO assessment'
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
            case 'cpdo_assessment_saved':
                $actionText = 'CPDO Assessment Saved';
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
            
            // Check if survey should be triggered
            $showSurvey = false;
            if ($statusChanged && $newStatus === 'verified') {
                // Check if user has already completed the survey for this application
                $existingSurvey = ClientSatisfactionSurvey::where('application_id', $application->id)
                    ->where('user_id', $application->user_id)
                    ->exists();
                
                if (!$existingSurvey) {
                    $showSurvey = true;
                    Log::info('Survey will be triggered for completed application', [
                        'application_id' => $application->id,
                        'user_id' => $application->user_id
                    ]);
                } else {
                    Log::info('Survey already completed for this application, skipping', [
                        'application_id' => $application->id,
                        'user_id' => $application->user_id
                    ]);
                }
            }
            
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
                    'was_auto_archived' => $wasAutoArchived,
                    'show_survey' => $showSurvey
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
     * 
     * PERMISSIONS:
     * - Assessor can verify: tct_link (TCT/Deed of Sale) and tax_declaration_link (Tax Declaration)
     * - Treasurer can verify: current_tax_receipt_link (Current Tax Receipt) ONLY
     * - SPA cannot be verified by staff (only admin)
     * 
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
                    // Only Assessor can verify Tax Declaration
                    if ($position !== 'assessor') {
                        Log::error('Unauthorized: User is not Assessor', ['position' => $position]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Only the Assessor can verify Tax Declaration documents.'
                        ], 403);
                    }
                    
                    $ownershipVerification->assessor_status = $isVerified ? 'approved' : 'pending';
                    $ownershipVerification->assessor_verified_at = $isVerified ? $now : null;
                    $ownershipVerification->assessor_remarks = $isVerified ? null : ($request->remarks ?? 'Verification removed');
                    $responseMessage = $isVerified ? 'Tax Declaration verified successfully' : 'Tax Declaration verification removed';
                    Log::info('Tax Declaration verification updated', ['verified' => $isVerified, 'assessor_status' => $ownershipVerification->assessor_status]);
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
   
    /**
 * Get all submitted client satisfaction surveys
 */
public function getSurveys(Request $request)
{
    try {
        $query = ClientSatisfactionSurvey::with(['user', 'application']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'LIKE', "%{$search}%")
                              ->orWhere('last_name', 'LIKE', "%{$search}%")
                              ->orWhere('email', 'LIKE', "%{$search}%");
                })
                  ->orWhereHas('application', function($appQuery) use ($search) {
                      $appQuery->where('application_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        if ($request->filled('sex')) {
            $query->where('sex', $request->sex);
        }

        // Get period for trend data
        $period = $request->get('period', 'this_month');
        
        // Calculate statistics
        $stats = $this->calculateSurveyStatistics($query, $period);
        
        // Get paginated results
        $perPage = $request->get('per_page', 15);
        $surveys = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $formattedSurveys = $surveys->getCollection()->map(function($survey) {
            return [
                'id' => $survey->id,
                'application_number' => $survey->application ? $survey->application->application_number : 'N/A',
                'applicant_name' => $survey->user ? $survey->user->first_name . ' ' . $survey->user->last_name : 'Unknown',
                'email' => $survey->user ? $survey->user->email : null,
                'client_type' => $survey->client_type,
                'sex' => $survey->sex,
                'age' => $survey->age,
                'survey_date' => $survey->survey_date,
                'cc1_awareness' => $survey->cc1_awareness,
                'cc2_helpfulness' => $survey->cc2_helpfulness,
                'cc3_help_level' => $survey->cc3_help_level,
                'sqd0_satisfied' => $survey->sqd0_satisfied,
                'sqd1_reasonable_time' => $survey->sqd1_reasonable_time,
                'sqd2_requirements_followed' => $survey->sqd2_requirements_followed,
                'sqd3_steps_easy' => $survey->sqd3_steps_easy,
                'sqd4_info_easy_find' => $survey->sqd4_info_easy_find,
                'sqd5_reasonable_fees' => $survey->sqd5_reasonable_fees,
                'sqd6_fair_treatment' => $survey->sqd6_fair_treatment,
                'sqd7_courteous_staff' => $survey->sqd7_courteous_staff,
                'sqd8_got_what_needed' => $survey->sqd8_got_what_needed,
                'suggestions' => $survey->suggestions,
                'email_contact' => $survey->email,
                'created_at' => $survey->created_at
            ];
        });

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'surveys' => $formattedSurveys,
            'pagination' => [
                'current_page' => $surveys->currentPage(),
                'last_page' => $surveys->lastPage(),
                'per_page' => $surveys->perPage(),
                'total' => $surveys->total(),
                'from' => $surveys->firstItem(),
                'to' => $surveys->lastItem()
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error fetching surveys: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch surveys: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Calculate survey statistics for charts
 */
private function calculateSurveyStatistics($query, $period = 'this_month')
{
    try {
        // Get all surveys for stats (without pagination)
        $allSurveys = clone $query;
        $surveys = $allSurveys->get();
        
        $total = $surveys->count();
        
        // Calculate average rating from SQD questions
        $allRatings = [];
        $ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $sqdSums = [0, 0, 0, 0, 0, 0, 0, 0, 0];
        $sqdCounts = 0;
        
        foreach ($surveys as $survey) {
            $ratings = [];
            for ($i = 0; $i <= 8; $i++) {
                $sqdField = 'sqd' . $i . '_satisfied';
                if ($i == 0) $sqdField = 'sqd0_satisfied';
                elseif ($i == 1) $sqdField = 'sqd1_reasonable_time';
                elseif ($i == 2) $sqdField = 'sqd2_requirements_followed';
                elseif ($i == 3) $sqdField = 'sqd3_steps_easy';
                elseif ($i == 4) $sqdField = 'sqd4_info_easy_find';
                elseif ($i == 5) $sqdField = 'sqd5_reasonable_fees';
                elseif ($i == 6) $sqdField = 'sqd6_fair_treatment';
                elseif ($i == 7) $sqdField = 'sqd7_courteous_staff';
                elseif ($i == 8) $sqdField = 'sqd8_got_what_needed';
                
                $value = $survey->$sqdField;
                if ($value && is_numeric($value)) {
                    $ratings[] = (int)$value;
                    $sqdSums[$i] += (int)$value;
                }
            }
            
            $sqdCounts++;
            $avgRating = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : 0;
            $allRatings[] = $avgRating;
            
            // Round to nearest integer for distribution
            $roundedRating = round($avgRating);
            if ($roundedRating >= 1 && $roundedRating <= 5) {
                $ratingDistribution[$roundedRating]++;
            }
        }
        
        $avgOverallRating = count($allRatings) > 0 ? array_sum($allRatings) / count($allRatings) : 0;
        $highestRating = count($allRatings) > 0 ? max($allRatings) : 0;
        $lowestRating = count($allRatings) > 0 ? min($allRatings) : 0;
        
        // Calculate response rate (surveys vs total applications)
        $totalApplications = ApplicationDocument::whereIn('status', ['verified', 'completed'])->count();
        $responseRate = $totalApplications > 0 ? ($total / $totalApplications) * 100 : 0;
        
        // Get client type distribution
        $clientTypes = [
            'citizen' => $surveys->where('client_type', 'citizen')->count(),
            'business' => $surveys->where('client_type', 'business')->count(),
            'government' => $surveys->where('client_type', 'government')->count()
        ];
        
        // Calculate SQD averages
        $sqdScores = [];
        for ($i = 0; $i < 9; $i++) {
            $sqdScores[] = $sqdCounts > 0 ? round($sqdSums[$i] / $sqdCounts, 1) : 0;
        }
        
        // Get trend data based on period
        $trendData = $this->getTrendData($period);
        
        // Get this month count
        $thisMonthCount = (clone $query)->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return [
            'total' => $total,
            'avg_rating' => round($avgOverallRating, 1),
            'highest_rating' => round($highestRating, 1),
            'lowest_rating' => round($lowestRating, 1),
            'response_rate' => round($responseRate, 1),
            'this_month' => $thisMonthCount,
            'trend_labels' => $trendData['labels'],
            'trend_values' => $trendData['values'],
            'rating_distribution' => $ratingDistribution,
            'client_types' => $clientTypes,
            'sqd_scores' => $sqdScores
        ];
        
    } catch (\Exception $e) {
        Log::error('Error calculating survey statistics: ' . $e->getMessage());
        return [
            'total' => 0,
            'avg_rating' => 0,
            'highest_rating' => 0,
            'lowest_rating' => 0,
            'response_rate' => 0,
            'this_month' => 0,
            'trend_labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            'trend_values' => [0, 0, 0, 0],
            'rating_distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            'client_types' => ['citizen' => 0, 'business' => 0, 'government' => 0],
            'sqd_scores' => [0, 0, 0, 0, 0, 0, 0, 0, 0]
        ];
    }
}

/**
 * Get trend data based on period
 */
private function getTrendData($period)
{
    $query = ClientSatisfactionSurvey::query();
    
    switch ($period) {
        case 'this_month':
            // Get weekly data for current month
            $weeks = [];
            $values = [];
            $currentMonth = now()->month;
            $currentYear = now()->year;
            
            for ($week = 1; $week <= 4; $week++) {
                $startDate = now()->setMonth($currentMonth)->setYear($currentYear)->startOfMonth()->addWeeks($week - 1);
                $endDate = (clone $startDate)->addDays(6);
                
                if ($week == 4) {
                    $endDate = now()->setMonth($currentMonth)->setYear($currentYear)->endOfMonth();
                }
                
                $count = ClientSatisfactionSurvey::whereBetween('created_at', [$startDate, $endDate])->count();
                $avgRating = $this->getAverageRatingForPeriod($startDate, $endDate);
                
                $weeks[] = "Week {$week}";
                $values[] = round($avgRating, 1);
            }
            return ['labels' => $weeks, 'values' => $values];
            
        case 'last_month':
            // Get weekly data for last month
            $lastMonth = now()->subMonth();
            $weeks = [];
            $values = [];
            
            for ($week = 1; $week <= 4; $week++) {
                $startDate = $lastMonth->copy()->startOfMonth()->addWeeks($week - 1);
                $endDate = (clone $startDate)->addDays(6);
                
                if ($week == 4) {
                    $endDate = $lastMonth->copy()->endOfMonth();
                }
                
                $avgRating = $this->getAverageRatingForPeriod($startDate, $endDate);
                $weeks[] = "Week {$week}";
                $values[] = round($avgRating, 1);
            }
            return ['labels' => $weeks, 'values' => $values];
            
        case 'this_year':
        default:
            // Get monthly data for current year
            $months = [];
            $values = [];
            
            for ($month = 1; $month <= 12; $month++) {
                $startDate = now()->setMonth($month)->startOfMonth();
                $endDate = now()->setMonth($month)->endOfMonth();
                $avgRating = $this->getAverageRatingForPeriod($startDate, $endDate);
                
                $months[] = date('M', mktime(0, 0, 0, $month, 1));
                $values[] = round($avgRating, 1);
            }
            return ['labels' => $months, 'values' => $values];
    }
}

/**
 * Get average rating for a date period
 */
private function getAverageRatingForPeriod($startDate, $endDate)
{
    $surveys = ClientSatisfactionSurvey::whereBetween('created_at', [$startDate, $endDate])->get();
    
    if ($surveys->isEmpty()) {
        return 0;
    }
    
    $allRatings = [];
    foreach ($surveys as $survey) {
        $ratings = [];
        for ($i = 0; $i <= 8; $i++) {
            $sqdField = $this->getSqdFieldName($i);
            $value = $survey->$sqdField;
            if ($value && is_numeric($value)) {
                $ratings[] = (int)$value;
            }
        }
        if (count($ratings) > 0) {
            $allRatings[] = array_sum($ratings) / count($ratings);
        }
    }
    
    return count($allRatings) > 0 ? array_sum($allRatings) / count($allRatings) : 0;
}

/**
 * Get SQD field name by index
 */
private function getSqdFieldName($index)
{
    $fields = [
        0 => 'sqd0_satisfied',
        1 => 'sqd1_reasonable_time',
        2 => 'sqd2_requirements_followed',
        3 => 'sqd3_steps_easy',
        4 => 'sqd4_info_easy_find',
        5 => 'sqd5_reasonable_fees',
        6 => 'sqd6_fair_treatment',
        7 => 'sqd7_courteous_staff',
        8 => 'sqd8_got_what_needed'
    ];
    return $fields[$index];
}

    /**
     * Export surveys to CSV
     */
    public function exportSurveys(Request $request)
    {
        try {
            $query = ClientSatisfactionSurvey::with(['user', 'application']);

            // Apply filters if provided
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('first_name', 'LIKE', "%{$search}%")
                                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                                  ->orWhere('email', 'LIKE', "%{$search}%");
                    })
                      ->orWhereHas('application', function($appQuery) use ($search) {
                          $appQuery->where('application_number', 'LIKE', "%{$search}%");
                      });
                });
            }

            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
            }

            if ($request->filled('client_type')) {
                $query->where('client_type', $request->client_type);
            }

            if ($request->filled('sex')) {
                $query->where('sex', $request->sex);
            }

            $surveys = $query->orderBy('created_at', 'desc')->get();

            $filename = 'client_satisfaction_surveys_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($surveys) {
                $handle = fopen('php://output', 'w');

                // Write headers
                fputcsv($handle, [
                    'Application Number',
                    'Applicant Name',
                    'Email',
                    'Client Type',
                    'Sex',
                    'Age',
                    'Survey Date',
                    'CC1 Awareness',
                    'CC2 Helpfulness',
                    'CC3 Help Level',
                    'SQD0 Satisfied',
                    'SQD1 Reasonable Time',
                    'SQD2 Requirements Followed',
                    'SQD3 Steps Easy',
                    'SQD4 Info Easy Find',
                    'SQD5 Reasonable Fees',
                    'SQD6 Fair Treatment',
                    'SQD7 Courteous Staff',
                    'SQD8 Got What Needed',
                    'Suggestions',
                    'Contact Email',
                    'Submitted At'
                ]);

                foreach ($surveys as $survey) {
                    fputcsv($handle, [
                        $survey->application ? $survey->application->application_number : 'N/A',
                        $survey->user ? $survey->user->first_name . ' ' . $survey->user->last_name : 'Unknown',
                        $survey->user ? $survey->user->email : '',
                        $survey->client_type,
                        $survey->sex,
                        $survey->age,
                        $survey->survey_date,
                        $survey->cc1_awareness,
                        $survey->cc2_helpfulness,
                        $survey->cc3_help_level,
                        $survey->sqd0_satisfied,
                        $survey->sqd1_reasonable_time,
                        $survey->sqd2_requirements_followed,
                        $survey->sqd3_steps_easy,
                        $survey->sqd4_info_easy_find,
                        $survey->sqd5_reasonable_fees,
                        $survey->sqd6_fair_treatment,
                        $survey->sqd7_courteous_staff,
                        $survey->sqd8_got_what_needed,
                        $survey->suggestions,
                        $survey->email,
                        $survey->created_at ? $survey->created_at->format('Y-m-d H:i:s') : ''
                    ]);
                }

                fclose($handle);
            };

            return response()->streamDownload($callback, $filename, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting surveys: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting surveys: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Dashboard PDF HTML content
     */
    private function generateDashboardPDFHTML($stats, $trendData, $recentActivities, $deadlines)
    {
        $total = $stats['total'];
        $pending = $stats['pending'];
        $underReview = $stats['under_review'];
        $approved = $stats['approved'];
        $forRelease = $stats['for_release'];
        $verified = $stats['verified'];
        $rejected = $stats['rejected'];
        $completionRate = $stats['completion_rate'];
        $trendChange = $stats['trend_change'] ?? 0;
        $avgProcessingTime = $stats['avg_processing_time'] ?? 0;
        $pendingAging = $stats['pending_aging'] ?? 0;
        
        $statusColors = [
            'pending' => '#F59E0B',
            'under-review' => '#8B5CF6',
            'approved' => '#10B981',
            'rejected' => '#EF4444',
            'for-release' => '#3B82F6',
            'verified' => '#22C55E'
        ];
        
        $maxTrendValue = !empty($trendData) ? max(array_column($trendData, 'count')) : 1;
        $totalTrend = array_sum(array_column($trendData, 'count'));
        $avgTrend = !empty($trendData) ? round($totalTrend / count($trendData)) : 0;
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Konstructo Dashboard Export - ' . date('Y-m-d H:i:s') . '</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: "Poppins", -apple-system, Arial, sans-serif; background: #f0f2f5; padding: 30px 20px; color: #1a1a2e; }
                .container { max-width: 1300px; margin: 0 auto; background: white; border-radius: 20px; padding: 30px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); }
                .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e9ecef; flex-wrap: wrap; gap: 15px; }
                .header h1 { color: #155386; font-size: 28px; font-weight: 600; }
                .header-date { color: #6c757d; font-size: 14px; }
                .print-btn { background: #155386; color: white; border: none; padding: 8px 18px; border-radius: 8px; cursor: pointer; font-size: 13px; font-family: "Poppins", sans-serif; font-weight: 500; }
                .print-btn:hover { background: #1F363D; }
                .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
                .stat-card { background: white; border-radius: 16px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
                .stat-label { font-size: 13px; color: #6c757d; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; }
                .stat-value { font-size: 32px; font-weight: 700; color: #155386; }
                .stat-trend { font-size: 12px; margin-top: 8px; color: #10b981; font-weight: 500; }
                .stat-trend.negative { color: #ef4444; }
                .section { margin-bottom: 30px; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden; }
                .section-header { background: linear-gradient(135deg, #155386 0%, #1F363D 100%); padding: 15px 20px; color: white; font-weight: 600; font-size: 16px; }
                .section-content { padding: 20px; }
                .two-columns { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px; }
                .chart-container { display: flex; align-items: flex-end; justify-content: center; gap: 6px; padding: 20px 0; min-height: 280px; width: 100%; }
                .chart-bar-wrapper { flex: 1; text-align: center; min-width: 35px; max-width: 60px; }
                .chart-bar { background: linear-gradient(180deg, #155386 0%, #40798C 100%); border-radius: 8px 8px 4px 4px; margin: 0 auto; width: 100%; max-width: 45px; transition: height 0.3s ease; }
                .chart-label { font-size: 9px; color: #6c757d; margin-top: 8px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                .chart-value { font-size: 10px; font-weight: 600; color: #155386; margin-top: 4px; }
                .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 500; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e9ecef; }
                th { background: #f8f9fa; font-weight: 600; font-size: 12px; color: #495057; text-transform: uppercase; letter-spacing: 0.5px; }
                td { font-size: 13px; font-weight: 400; }
                tr:hover { background: #f8f9fa; }
                .summary-stats { display: flex; justify-content: space-around; background: #f8f9fa; border-radius: 12px; padding: 15px; margin-top: 20px; text-align: center; flex-wrap: wrap; gap: 15px; }
                .summary-stats div { font-size: 13px; font-weight: 500; }
                .summary-stats strong { font-weight: 700; color: #155386; }
                .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 11px; border-top: 1px solid #e9ecef; margin-top: 20px; }
                .progress-bar-container { width: 100%; background: #e5e7eb; border-radius: 10px; overflow: hidden; margin-top: 8px; }
                .progress-bar-fill { background: linear-gradient(90deg, #155386 0%, #40798C 100%); height: 6px; border-radius: 10px; transition: width 0.3s ease; }
                
                @media print {
                    body { background: white; padding: 0; margin: 0; }
                    .container { box-shadow: none; padding: 15px; max-width: 100%; border-radius: 0; }
                    .print-btn { display: none; }
                    .section-header { background: #155386 !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
                    .chart-bar { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
                    .stats-grid { gap: 12px; }
                    .stat-card { padding: 12px; }
                    .stat-value { font-size: 24px; }
                    .section { page-break-inside: avoid; break-inside: avoid; }
                    .stats-grid { page-break-inside: avoid; break-inside: avoid; }
                }
                
                @media (max-width: 768px) {
                    .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
                    .two-columns { grid-template-columns: 1fr; gap: 20px; }
                    .chart-container { overflow-x: auto; justify-content: flex-start; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div>
                        <h1>Konstructo Dashboard Export</h1>
                        <div class="header-date">Generated: ' . date('F d, Y g:i:s A') . '</div>
                    </div>
                    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>
                </div>
                
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total Applications</div>
                        <div class="stat-value">' . number_format($total) . '</div>
                        <div class="stat-trend ' . ($trendChange >= 0 ? '' : 'negative') . '">' . ($trendChange >= 0 ? '↑' : '↓') . ' ' . abs($trendChange) . '% from last month</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Pending Review</div>
                        <div class="stat-value">' . number_format($pending) . '</div>
                        <div class="stat-trend">⏰ ' . $pendingAging . ' pending over 7 days</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Completed</div>
                        <div class="stat-value">' . number_format($verified) . '</div>
                        <div class="stat-trend">✓ ' . $completionRate . '% completion rate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Avg Processing Time</div>
                        <div class="stat-value">' . $avgProcessingTime . ' days</div>
                        <div class="stat-trend">Average time to complete</div>
                    </div>
                </div>
                
                <div class="two-columns">
                    <!-- Application Trend Chart -->
                    <div class="section">
                        <div class="section-header">Application Trend (Last 30 Days)</div>
                        <div class="section-content">
                            <div class="chart-container">';
        
        foreach ($trendData as $item) {
            $height = $maxTrendValue > 0 ? ($item['count'] / $maxTrendValue) * 180 : 20;
            $height = max($height, 25);
            $html .= '
                                <div class="chart-bar-wrapper">
                                    <div class="chart-bar" style="height: ' . $height . 'px;"></div>
                                    <div class="chart-label">' . $item['label'] . '</div>
                                    <div class="chart-value">' . $item['count'] . '</div>
                                </div>';
        }
        
        $html .= '
                            </div>
                            <div class="summary-stats">
                                <div><strong>Total:</strong> ' . number_format($totalTrend) . '</div>
                                <div><strong>Average:</strong> ' . number_format($avgTrend) . '</div>
                                <div><strong>Peak:</strong> ' . number_format($maxTrendValue) . '</div>
                                <div><strong>Period:</strong> 30 days</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Distribution -->
                    <div class="section">
                        <div class="section-header">Application Status</div>
                        <div class="section-content">
                            <table>
                                <thead>
                                    <tr><th>Status</th><th>Count</th><th>Percentage</th></tr>
                                </thead>
                                <tbody>';
        
        $statusList = [
            ['key' => 'pending', 'label' => 'Pending', 'count' => $pending],
            ['key' => 'under-review', 'label' => 'Under Review', 'count' => $underReview],
            ['key' => 'approved', 'label' => 'Approved', 'count' => $approved],
            ['key' => 'for-release', 'label' => 'For Release', 'count' => $forRelease],
            ['key' => 'verified', 'label' => 'Completed', 'count' => $verified],
            ['key' => 'rejected', 'label' => 'Rejected', 'count' => $rejected],
        ];
        
        foreach ($statusList as $status) {
            $count = $status['count'];
            $percent = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $color = $statusColors[$status['key']] ?? '#6c757d';
            $html .= '<tr>
                                <td><span class="status-badge" style="background:' . $color . '20; color:' . $color . ';">' . $status['label'] . '</span></td>
                                <td>' . number_format($count) . '</span></td>
                                <td>' . $percent . '%</span></td>
                              </tr>';
        }
        
        $html .= '
                                </tbody>
                            </table>
                            <div class="progress-bar-container" style="margin-top: 15px;">
                                <div class="progress-bar-fill" style="width: ' . $completionRate . '%;"></div>
                            </div>
                            <div class="summary-stats" style="margin-top: 10px;">
                                <div><strong>Completion Rate:</strong> ' . $completionRate . '%</div>
                                <div><strong>In Progress:</strong> ' . number_format($pending + $underReview) . '</div>
                                <div><strong>Completed:</strong> ' . number_format($verified + $approved + $forRelease) . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="section">
                    <div class="section-header">Recent Activities</div>
                    <div class="section-content">
                        <table>
                            <thead>
                                <tr><th>Date & Time</th><th>Action</th><th>Reviewer</th><th>Remarks</th></tr>
                            </thead>
                            <tbody>';
        
        foreach ($recentActivities as $activity) {
            $actionDisplay = $activity->action_display ?? $activity->action ?? 'Activity';
            $actionLower = strtolower($actionDisplay);
            
            $badgeColor = '#6c757d';
            if (strpos($actionLower, 'approve') !== false) $badgeColor = '#10b981';
            elseif (strpos($actionLower, 'reject') !== false) $badgeColor = '#ef4444';
            elseif (strpos($actionLower, 'pending') !== false) $badgeColor = '#f59e0b';
            elseif (strpos($actionLower, 'review') !== false) $badgeColor = '#8b5cf6';
            
            $html .= '<tr>
                                <td>' . ($activity->created_at ? $activity->created_at->format('Y-m-d H:i') : '') . '</span></td>
                                <td><span class="status-badge" style="background:' . $badgeColor . '20; color:' . $badgeColor . ';">' . htmlspecialchars($actionDisplay) . '</span></span></td>
                                <td>' . htmlspecialchars($activity->reviewer_name ?? 'System') . '</span></td>
                                <td>' . htmlspecialchars(substr($activity->remarks ?? '', 0, 60)) . (strlen($activity->remarks ?? '') > 60 ? '...' : '') . '</span></td>
                              </tr>';
        }
        
        $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Upcoming Deadlines -->
                <div class="section">
                    <div class="section-header">Upcoming Deadlines</div>
                    <div class="section-content">
                        <table>
                            <thead>
                                <tr><th>Application #</th><th>Applicant</th><th>Days Left</th><th>Due Date</th><th>Status</th></tr>
                            </thead>
                            <tbody>';
        
        // Convert to array if it's a collection
        $deadlinesArray = $deadlines instanceof \Illuminate\Support\Collection ? $deadlines->toArray() : $deadlines;
        
        foreach ($deadlinesArray as $deadline) {
            $daysLeft = $deadline['days_left'];
            $dueClass = '';
            $dueText = '';
            
            if ($daysLeft <= 1) {
                $dueClass = 'style="background:#ef444420; color:#dc2626;"';
                $dueText = 'Urgent';
            } elseif ($daysLeft <= 3) {
                $dueClass = 'style="background:#f59e0b20; color:#d97706;"';
                $dueText = 'Warning';
            } else {
                $dueClass = 'style="background:#10b98120; color:#16a34a;"';
                $dueText = 'On Track';
            }
            
            $html .= '<tr>
                                <td><strong>' . htmlspecialchars($deadline['application_name']) . '</strong></span></td>
                                <td>' . htmlspecialchars($deadline['applicant_name']) . '</span></td>
                                <td><span ' . $dueClass . ' class="status-badge">' . $deadline['days_left'] . ' days</span></span></td>
                                <td>' . $deadline['due_date'] . '</span></td>
                                <td><span ' . $dueClass . ' class="status-badge">' . $dueText . '</span></span></td>
                              </tr>';
        }
        
        $html .= '
                            </tbody>
                        </table>
                        <div class="summary-stats" style="margin-top: 15px;">
                            <div><strong>⚠️ Urgent (0-1 days):</strong> ' . count(array_filter($deadlinesArray, fn($d) => $d['days_left'] <= 1)) . '</div>
                            <div><strong>⚠️ Warning (2-3 days):</strong> ' . count(array_filter($deadlinesArray, fn($d) => $d['days_left'] >= 2 && $d['days_left'] <= 3)) . '</div>
                            <div><strong>✅ On Track (4+ days):</strong> ' . count(array_filter($deadlinesArray, fn($d) => $d['days_left'] >= 4)) . '</div>
                        </div>
                    </div>
                </div>
                
                <!-- Executive Summary -->
                <div class="section">
                    <div class="section-header">Executive Summary</div>
                    <div class="section-content">
                        <div class="summary-stats" style="flex-wrap: wrap; gap: 20px;">
                            <div><strong>Total Applications:</strong> ' . number_format($total) . '</div>
                            <div><strong>Pending Review:</strong> ' . number_format($pending) . ' (' . ($total > 0 ? round(($pending / $total) * 100) : 0) . '%)</div>
                            <div><strong>Under Review:</strong> ' . number_format($underReview) . ' (' . ($total > 0 ? round(($underReview / $total) * 100) : 0) . '%)</div>
                            <div><strong>Completed:</strong> ' . number_format($verified + $approved + $forRelease) . ' (' . ($total > 0 ? round((($verified + $approved + $forRelease) / $total) * 100) : 0) . '%)</div>
                            <div><strong>Rejected:</strong> ' . number_format($rejected) . ' (' . ($total > 0 ? round(($rejected / $total) * 100) : 0) . '%)</div>
                            <div><strong>Average Processing Time:</strong> ' . $avgProcessingTime . ' days</div>
                            <div><strong>Aging Applications (&gt;7 days):</strong> ' . $pendingAging . '</div>
                        </div>
                    </div>
                </div>
                
                <div class="footer">
                    <p>Konstructo - Smart Infrastructure Oversight</p>
                    <p>This report was generated automatically. For questions, contact your system administrator.</p>
                    <p>Report ID: DASH-' . date('Ymd') . '-' . rand(1000, 9999) . ' | Generated: ' . date('Y-m-d H:i:s') . '</p>
                </div>
            </div>
            
            <script>
                // Auto-trigger print dialog when page loads (optional)
                // window.onload = function() { setTimeout(function() { window.print(); }, 500); };
            </script>
        </body>
        </html>';
        
        return $html;
    }
    /**
 * Get CPDO ratings data for staff view
 */
public function getCPDORatings(Request $request)
{
    try {
        $query = CPDORating::with(['user', 'application']);
        
        $perPage = $request->get('per_page', 15);
        $ratings = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        $formattedRatings = $ratings->getCollection()->map(function($rating) {
            return [
                'id' => $rating->id,
                'application_number' => $rating->application ? $rating->application->application_number : 'N/A',
                'applicant_name' => $rating->user ? $rating->user->first_name . ' ' . $rating->user->last_name : 'Unknown',
                'email' => $rating->user ? $rating->user->email : null,
                'rating' => $rating->rating,
                'processing_time' => $rating->processing_time,
                'responsiveness' => $rating->responsiveness,
                'clarity' => $rating->clarity,
                'fairness' => $rating->fairness,
                'overall_satisfaction' => $rating->overall_satisfaction,
                'comments' => $rating->comments,
                'created_at' => $rating->created_at
            ];
        });
        
        return response()->json([
            'success' => true,
            'ratings' => $formattedRatings,
            'pagination' => [
                'current_page' => $ratings->currentPage(),
                'last_page' => $ratings->lastPage(),
                'per_page' => $ratings->perPage(),
                'total' => $ratings->total(),
                'from' => $ratings->firstItem(),
                'to' => $ratings->lastItem()
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error fetching CPDO ratings: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch CPDO ratings'
        ], 500);
    }
}

/**
 * Get CPDO ratings statistics for charts
 */
public function getCPDORatingsStats(Request $request)
{
    try {
        $ratings = CPDORating::all();
        $total = $ratings->count();
        
        // Rating distribution
        $ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $avgRating = 0;
        $sumRatings = 0;
        
        // Metrics averages
        $metricsScores = [
            'processing_time' => ['sum' => 0, 'count' => 0],
            'responsiveness' => ['sum' => 0, 'count' => 0],
            'clarity' => ['sum' => 0, 'count' => 0],
            'fairness' => ['sum' => 0, 'count' => 0],
            'overall' => ['sum' => 0, 'count' => 0]
        ];
        
        $ratingMap = [
            'Excellent' => 5, 'Very Satisfied' => 5,
            'Good' => 4, 'Satisfied' => 4,
            'Average' => 3, 'Neutral' => 3,
            'Poor' => 2, 'Dissatisfied' => 2,
            'Very Poor' => 1, 'Very Dissatisfied' => 1
        ];
        
        foreach ($ratings as $rating) {
            $ratingDistribution[$rating->rating]++;
            $sumRatings += $rating->rating;
            
            // Map text ratings to numeric scores
            if ($rating->processing_time && isset($ratingMap[$rating->processing_time])) {
                $metricsScores['processing_time']['sum'] += $ratingMap[$rating->processing_time];
                $metricsScores['processing_time']['count']++;
            }
            if ($rating->responsiveness && isset($ratingMap[$rating->responsiveness])) {
                $metricsScores['responsiveness']['sum'] += $ratingMap[$rating->responsiveness];
                $metricsScores['responsiveness']['count']++;
            }
            if ($rating->clarity && isset($ratingMap[$rating->clarity])) {
                $metricsScores['clarity']['sum'] += $ratingMap[$rating->clarity];
                $metricsScores['clarity']['count']++;
            }
            if ($rating->fairness && isset($ratingMap[$rating->fairness])) {
                $metricsScores['fairness']['sum'] += $ratingMap[$rating->fairness];
                $metricsScores['fairness']['count']++;
            }
            if ($rating->overall_satisfaction && isset($ratingMap[$rating->overall_satisfaction])) {
                $metricsScores['overall']['sum'] += $ratingMap[$rating->overall_satisfaction];
                $metricsScores['overall']['count']++;
            }
        }
        
        $avgRating = $total > 0 ? $sumRatings / $total : 0;
        $fiveStarPercent = $total > 0 ? round(($ratingDistribution[5] / $total) * 100, 1) : 0;
        
        // Calculate average metrics scores
        $metricsAverages = [
            'processing_time' => $metricsScores['processing_time']['count'] > 0 ? round($metricsScores['processing_time']['sum'] / $metricsScores['processing_time']['count'], 1) : 0,
            'responsiveness' => $metricsScores['responsiveness']['count'] > 0 ? round($metricsScores['responsiveness']['sum'] / $metricsScores['responsiveness']['count'], 1) : 0,
            'clarity' => $metricsScores['clarity']['count'] > 0 ? round($metricsScores['clarity']['sum'] / $metricsScores['clarity']['count'], 1) : 0,
            'fairness' => $metricsScores['fairness']['count'] > 0 ? round($metricsScores['fairness']['sum'] / $metricsScores['fairness']['count'], 1) : 0,
            'overall' => $metricsScores['overall']['count'] > 0 ? round($metricsScores['overall']['sum'] / $metricsScores['overall']['count'], 1) : 0
        ];
        
        // Calculate response rate (percentage of applications that have CPDO ratings)
        $totalApplications = ApplicationDocument::where('cpdo_status', 'approved')->count();
        $responseRate = $totalApplications > 0 ? round(($total / $totalApplications) * 100, 1) : 0;
        
        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'avg_rating' => round($avgRating, 1),
                'five_star_percent' => $fiveStarPercent,
                'response_rate' => $responseRate,
                'rating_distribution' => $ratingDistribution,
                'metrics_scores' => $metricsAverages
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error fetching CPDO ratings stats: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch CPDO ratings statistics'
        ], 500);
    }
}

/**
 * Export CPDO ratings to CSV
 */
public function exportCPDORatings(Request $request)
{
    try {
        $ratings = CPDORating::with(['user', 'application'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $filename = 'cpdo_experience_ratings_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($ratings) {
            $handle = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($handle, [
                'Application Number',
                'Applicant Name',
                'Email',
                'Rating (1-5)',
                'Processing Time',
                'Staff Responsiveness',
                'Clarity of Instructions',
                'Fairness of Assessment',
                'Overall Satisfaction',
                'Comments',
                'Submitted At'
            ]);
            
            foreach ($ratings as $rating) {
                fputcsv($handle, [
                    $rating->application ? $rating->application->application_number : 'N/A',
                    $rating->user ? $rating->user->first_name . ' ' . $rating->user->last_name : 'Unknown',
                    $rating->user ? $rating->user->email : '',
                    $rating->rating,
                    $rating->processing_time,
                    $rating->responsiveness,
                    $rating->clarity,
                    $rating->fairness,
                    $rating->overall_satisfaction,
                    $rating->comments,
                    $rating->created_at ? $rating->created_at->format('Y-m-d H:i:s') : ''
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->streamDownload($callback, $filename, $headers);
        
    } catch (\Exception $e) {
        Log::error('Error exporting CPDO ratings: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error exporting CPDO ratings: ' . $e->getMessage()
        ], 500);
    }
}
}
   