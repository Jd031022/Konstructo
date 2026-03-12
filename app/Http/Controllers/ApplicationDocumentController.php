<?php

namespace App\Http\Controllers;

use App\Models\ApplicationDocument;
use App\Models\User;
use App\Services\NotificationService; // ADD THIS IMPORT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ApplicationDocumentController extends Controller
{
    /**
     * The notification service instance.
     */
    protected $notificationService;

    /**
     * Constructor - Inject NotificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Store or update Google Drive link
     */
    public function storeLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_drive_link' => 'required|string',
            'hardcopy_confirmed' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // Get or generate application number
        $applicationNumber = $this->getApplicationNumber($user);

        // Check if link is a valid Google Drive link
        if (!$this->isValidGoogleDriveLink($request->google_drive_link)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid Google Drive link'
            ], 422);
        }

        try {
            // Update or create application document
            $applicationDoc = ApplicationDocument::updateOrCreate(
                ['user_id' => $user->id, 'status' => 'draft'], // Only update if it's a draft
                [
                    'application_number' => $applicationNumber,
                    'google_drive_link' => $request->google_drive_link,
                    'status' => 'draft', // Keep as draft until submitted
                    'rejection_reason' => null,
                    'verified_at' => null,
                    'verified_by' => null
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Google Drive link saved successfully',
                'data' => [
                    'application_number' => $applicationNumber,
                    'status' => $applicationDoc->status,
                    'google_drive_link' => $applicationDoc->google_drive_link
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save application document: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save link. Please try again.'
            ], 500);
        }
    }

    /**
     * Get application document details - get the most recent draft
     */
    public function getApplicationDetails()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Get the most recent application (could be draft or pending)
            $applicationDoc = ApplicationDocument::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$applicationDoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'No application documents found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $applicationDoc->id,
                    'application_number' => $applicationDoc->application_number,
                    'google_drive_link' => $applicationDoc->google_drive_link,
                    'status' => $applicationDoc->status,
                    'rejection_reason' => $applicationDoc->rejection_reason,
                    'submitted_at' => $applicationDoc->created_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getApplicationDetails: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check application status
     */
    public function checkStatus()
    {
        $user = Auth::user();
        
        $applicationDoc = ApplicationDocument::where('user_id', $user->id)->first();
        
        if (!$applicationDoc) {
            return response()->json([
                'status' => 'not_submitted',
                'message' => 'No application documents found'
            ]);
        }

        $statusMessages = [
            'pending' => 'Your documents are pending review by the admin.',
            'verified' => 'Your documents have been verified successfully!',
            'rejected' => 'Your documents were rejected. Please check the reason.',
            'draft' => 'Your application is in draft mode. Please complete and submit.'
        ];

        return response()->json([
            'status' => $applicationDoc->status,
            'message' => $statusMessages[$applicationDoc->status] ?? 'Status unknown',
            'rejection_reason' => $applicationDoc->rejection_reason,
            'application_number' => $applicationDoc->application_number,
            'submitted_at' => $applicationDoc->created_at->format('Y-m-d H:i:s'),
            'verified_at' => $applicationDoc->verified_at ? $applicationDoc->verified_at->format('Y-m-d H:i:s') : null
        ]);
    }

    /**
     * Generate or get existing application number
     */
    private function getApplicationNumber($user)
    {
        // Generate new application number: year + 6 random digits
        $year = date('Y');
        do {
            $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $applicationNumber = $year . $random;
        } while (ApplicationDocument::where('application_number', $applicationNumber)->exists());

        return $applicationNumber;
    }

    /**
     * Validate Google Drive link
     */
    private function isValidGoogleDriveLink($link)
    {
        $patterns = [
            '/drive\.google\.com\/file\/d\//',
            '/drive\.google\.com\/drive\/folders\//',
            '/drive\.google\.com\/open\?id=/',
            '/docs\.google\.com\/document\/d\//',
            '/docs\.google\.com\/spreadsheets\/d\//',
            '/docs\.google\.com\/presentation\/d\//',
            '/drive\.google\.com\/folderview\?id=/',
            '/drive\.google\.com\/uc\?export=download&id=/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $link)) {
                return true;
            }
        }

        // Also accept any link that contains drive.google.com or docs.google.com
        if (strpos($link, 'drive.google.com') !== false || strpos($link, 'docs.google.com') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Create a draft application when application number is generated
     */
    public function createDraft(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Check if user has reached the application limit (counts ONLY pending/verified)
            if ($this->hasReachedApplicationLimit($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum limit of 3 submitted applications. Please complete or delete existing applications before creating a new one.',
                    'limit_reached' => true
                ], 403);
            }
            
            // Generate new application number
            $applicationNumber = $this->getApplicationNumber($user);
            
            // Create draft
            $draft = ApplicationDocument::create([
                'user_id' => $user->id,
                'application_number' => $applicationNumber,
                'status' => 'draft',
                'google_drive_link' => null
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $draft->id,
                    'application_number' => $applicationNumber,
                    'status' => 'draft'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating draft: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit application (change status from draft to pending)
     * THIS IS THE MOST IMPORTANT METHOD FOR NOTIFICATIONS
     */
    public function submitApplication(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('status', 'draft')
                ->first();
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'No draft application found'
                ], 404);
            }
            
            // Check if they've reached the limit for submitted applications
            if ($this->hasReachedApplicationLimit($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum limit of 3 applications.'
                ], 403);
            }
            
            // Store old status for logging
            $oldStatus = $application->status;
            
            // Update status to pending
            $application->status = 'pending';
            $application->save();

            // TRIGGER NOTIFICATION: Notify staff about new application submission
            $this->notificationService->notifyStaffNewApplication($application);

            // Also log this activity
            if (class_exists('App\Models\ApplicationReviewActivity')) {
                \App\Models\ApplicationReviewActivity::create([
                    'application_id' => $application->id,
                    'reviewer_id' => $user->id,
                    'action' => 'application_submitted',
                    'old_status' => $oldStatus,
                    'new_status' => 'pending',
                    'remarks' => 'Application submitted by applicant',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }
            
            Log::info('Application submitted successfully', [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => [
                    'application_number' => $application->application_number,
                    'status' => 'pending'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error submitting application: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error submitting application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's application limit info
     */
    public function getApplicationLimitInfo()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Count ONLY submitted applications (pending or verified)
            $submittedCount = ApplicationDocument::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'verified'])
                ->count();
                
            // Count drafts separately
            $draftCount = ApplicationDocument::where('user_id', $user->id)
                ->where('status', 'draft')
                ->count();
                
            $limit = 3;
            $remaining = max(0, $limit - $submittedCount);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'submitted' => $submittedCount,
                    'drafts' => $draftCount,
                    'total' => $submittedCount + $draftCount,
                    'limit' => $limit,
                    'remaining' => $remaining,
                    'can_apply' => $submittedCount < $limit // Can apply if submitted apps are less than limit
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getApplicationLimitInfo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking application limit'
            ], 500);
        }
    }

    /**
     * Check if user has reached the application limit (only count submitted applications)
     */
    private function hasReachedApplicationLimit($user)
    {
        $count = ApplicationDocument::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'verified'])
            ->count();
        return $count >= 3;
    }

    /**
     * Debug method to check what's happening
     */
    public function debug()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Not authenticated',
                'user_id' => null
            ]);
        }
        
        $applications = ApplicationDocument::where('user_id', $user->id)->get();
        $submittedCount = ApplicationDocument::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'verified'])
            ->count();
        $draftCount = ApplicationDocument::where('user_id', $user->id)
            ->where('status', 'draft')
            ->count();
        
        return response()->json([
            'success' => true,
            'authenticated' => true,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'applications' => $applications,
            'submitted_count' => $submittedCount,
            'draft_count' => $draftCount,
            'table_exists' => \Illuminate\Support\Facades\Schema::hasTable('application_documents')
        ]);
    }
}