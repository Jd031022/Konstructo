<?php

namespace App\Http\Controllers;

use App\Models\ApplicationDocument;
use App\Models\User;
use App\Models\ApplicationReviewActivity;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
        try {
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
                    ['user_id' => $user->id, 'status' => 'draft'],
                    [
                        'application_number' => $applicationNumber,
                        'google_drive_link' => $request->google_drive_link,
                        'status' => 'draft',
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
        } catch (\Exception $e) {
            Log::error('Error in storeLink: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
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
                    'submitted_at' => $applicationDoc->created_at ? $applicationDoc->created_at->format('Y-m-d H:i:s') : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getApplicationDetails: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred'
            ], 500);
        }
    }

    /**
     * Check application status
     */
    public function checkStatus()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $applicationDoc = ApplicationDocument::where('user_id', $user->id)->first();
            
            if (!$applicationDoc) {
                return response()->json([
                    'success' => true,
                    'status' => 'not_submitted',
                    'message' => 'No application documents found'
                ]);
            }

            $statusMessages = [
                'pending' => 'Your documents are pending review by the admin.',
                'verified' => 'Your documents have been verified successfully!',
                'rejected' => 'Your documents were rejected. Please check the reason.',
                'draft' => 'Your application is in draft mode. Please complete and submit.',
                'under-review' => 'Your application is under review.',
                'document-verification' => 'Your documents are being verified.',
                'approved' => 'Your application has been approved.',
                'for-release' => 'Your application is ready for release.'
            ];

            return response()->json([
                'success' => true,
                'status' => $applicationDoc->status,
                'message' => $statusMessages[$applicationDoc->status] ?? 'Status unknown',
                'rejection_reason' => $applicationDoc->rejection_reason,
                'application_number' => $applicationDoc->application_number,
                'submitted_at' => $applicationDoc->created_at ? $applicationDoc->created_at->format('Y-m-d H:i:s') : null,
                'verified_at' => $applicationDoc->verified_at ? $applicationDoc->verified_at->format('Y-m-d H:i:s') : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in checkStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking status'
            ], 500);
        }
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
            
            // Log the activity
            try {
                if (class_exists('App\Models\ApplicationReviewActivity')) {
                    ApplicationReviewActivity::create([
                        'application_id' => $draft->id,
                        'reviewer_id' => $user->id,
                        'action' => 'application_created',
                        'old_status' => null,
                        'new_status' => 'draft',
                        'remarks' => 'Draft application created',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to log activity: ' . $e->getMessage());
            }
            
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
            try {
                $this->notificationService->notifyStaffNewApplication($application);
            } catch (\Exception $e) {
                Log::error('Failed to send notification: ' . $e->getMessage());
                // Don't fail the request if notification fails
            }

            // Also log this activity
            if (class_exists('App\Models\ApplicationReviewActivity')) {
                try {
                    ApplicationReviewActivity::create([
                        'application_id' => $application->id,
                        'reviewer_id' => $user->id,
                        'action' => 'application_submitted',
                        'old_status' => $oldStatus,
                        'new_status' => 'pending',
                        'remarks' => 'Application submitted by applicant',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to log activity: ' . $e->getMessage());
                }
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
            Log::info('getApplicationLimitInfo started');
            
            $user = Auth::user();
            
            if (!$user) {
                Log::warning('User not authenticated in getApplicationLimitInfo');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Check if table exists
            if (!Schema::hasTable('application_documents')) {
                Log::error('application_documents table does not exist');
                return response()->json([
                    'success' => false,
                    'message' => 'Database table not found',
                    'data' => [
                        'submitted' => 0,
                        'drafts' => 0,
                        'total' => 0,
                        'limit' => 3,
                        'remaining' => 3,
                        'can_apply' => true
                    ]
                ]);
            }
            
            // Count ONLY submitted applications (pending, under-review, document-verification, approved, for-release, verified)
            $submittedCount = ApplicationDocument::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'under-review', 'document-verification', 'approved', 'for-release', 'verified'])
                ->count();
                
            // Count drafts separately
            $draftCount = ApplicationDocument::where('user_id', $user->id)
                ->where('status', 'draft')
                ->count();
                
            $limit = 3;
            $remaining = max(0, $limit - $submittedCount);
            
            Log::info('Limit info calculated', [
                'submitted' => $submittedCount,
                'drafts' => $draftCount,
                'remaining' => $remaining
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'submitted' => $submittedCount,
                    'drafts' => $draftCount,
                    'total' => $submittedCount + $draftCount,
                    'limit' => $limit,
                    'remaining' => $remaining,
                    'can_apply' => $submittedCount < $limit
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getApplicationLimitInfo: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking application limit: ' . $e->getMessage(),
                'data' => [
                    'submitted' => 0,
                    'drafts' => 0,
                    'total' => 0,
                    'limit' => 3,
                    'remaining' => 3,
                    'can_apply' => true
                ]
            ], 500);
        }
    }

    /**
     * Check if user has reached the application limit (only count submitted applications)
     */
    private function hasReachedApplicationLimit($user)
    {
        $count = ApplicationDocument::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'under-review', 'document-verification', 'approved', 'for-release', 'verified'])
            ->count();
        return $count >= 3;
    }

    /**
     * Debug method to check what's happening
     */
    public function debug()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not authenticated',
                    'user_id' => null
                ]);
            }
            
            // Check if table exists
            $tableExists = Schema::hasTable('application_documents');
            
            $applications = [];
            $submittedCount = 0;
            $draftCount = 0;
            
            if ($tableExists) {
                $applications = ApplicationDocument::where('user_id', $user->id)->get();
                $submittedCount = ApplicationDocument::where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'under-review', 'document-verification', 'approved', 'for-release', 'verified'])
                    ->count();
                $draftCount = ApplicationDocument::where('user_id', $user->id)
                    ->where('status', 'draft')
                    ->count();
            }
            
            return response()->json([
                'success' => true,
                'authenticated' => true,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'table_exists' => $tableExists,
                'applications' => $applications,
                'submitted_count' => $submittedCount,
                'draft_count' => $draftCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in debug: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate application number (static method for use in other controllers)
     */
    public static function generateApplicationNumber()
    {
        $year = date('Y');
        do {
            $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $applicationNumber = $year . $random;
        } while (ApplicationDocument::where('application_number', $applicationNumber)->exists());

        return $applicationNumber;
    }
}