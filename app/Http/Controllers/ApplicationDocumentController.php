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
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a draft application
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
            
            // Check if user has reached the application limit
            if ($this->hasReachedApplicationLimit($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum limit of 3 submitted applications.',
                    'limit_reached' => true
                ], 403);
            }
            
            // Check if there's already a draft for this user that doesn't have a number yet
            $existingDraft = ApplicationDocument::where('user_id', $user->id)
                ->where('status', 'draft')
                ->whereNull('application_number')
                ->first();
            
            if ($existingDraft) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $existingDraft->id,
                        'application_number' => null,
                        'status' => 'draft'
                    ]
                ]);
            }
            
            // Create new draft WITHOUT application number
            $draft = ApplicationDocument::create([
                'user_id' => $user->id,
                'application_number' => null,
                'status' => 'draft',
                'google_drive_link' => null
            ]);
            
            Log::info('Draft application created', [
                'application_id' => $draft->id,
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $draft->id,
                    'application_number' => null,
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
     * Store all document links (for step 2 - multiple documents)
     */
    public function storeLinks(Request $request)
    {
        try {
            Log::info('storeLinks called', ['request_data' => $request->all()]);
            
            $validator = Validator::make($request->all(), [
                'document_links' => 'required|array',
                'application_id' => 'required|exists:application_documents,id'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Please provide valid Google Drive links for all required documents.'
                ], 422);
            }

            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $documentLinks = $request->document_links;
            $applicationId = $request->application_id;
            
            // Check if we have an existing draft
            $applicationDoc = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->where('status', 'draft')
                ->first();
            
            if (!$applicationDoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'No draft application found. Please start from Step 1 first.'
                ], 404);
            }
            
            // Store document links in the document_links JSON column
            $applicationDoc->document_links = $documentLinks;
            $applicationDoc->save();
            
            Log::info('Document links saved successfully', [
                'application_id' => $applicationDoc->id,
                'user_id' => $user->id,
                'documents_count' => count($documentLinks)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'All documents saved successfully!',
                'data' => [
                    'id' => $applicationDoc->id,
                    'application_number' => $applicationDoc->application_number,
                    'status' => $applicationDoc->status
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in storeLinks: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application document details for a specific application
     */
    public function getApplicationDetails(Request $request)
    {
        try {
            $user = Auth::user();
            $applicationId = $request->application_id;
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            if (!$applicationId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application ID is required'
                ], 422);
            }
            
            $applicationDoc = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->first();
            
            if (!$applicationDoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $documentLinks = $applicationDoc->document_links;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $applicationDoc->id,
                    'application_number' => $applicationDoc->application_number,
                    'google_drive_link' => $applicationDoc->google_drive_link,
                    'document_links' => $documentLinks,
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
     * Store or update Google Drive link (single link)
     */
    public function storeLink(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'google_drive_link' => 'required|string',
                'hardcopy_confirmed' => 'required|boolean',
                'application_id' => 'required|exists:application_documents,id'
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
            
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $request->application_id)
                ->where('status', 'draft')
                ->first();
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found or not in draft status'
                ], 404);
            }

            if (!$this->isValidGoogleDriveLink($request->google_drive_link)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a valid Google Drive link'
                ], 422);
            }

            try {
                $application->google_drive_link = $request->google_drive_link;
                $application->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Google Drive link saved successfully',
                    'data' => [
                        'id' => $application->id,
                        'application_number' => $application->application_number,
                        'status' => $application->status,
                        'google_drive_link' => $application->google_drive_link
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
     * Check application status
     */
    public function checkStatus(Request $request)
    {
        try {
            $user = Auth::user();
            $applicationId = $request->application_id;
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $query = ApplicationDocument::where('user_id', $user->id);
            
            if ($applicationId) {
                $query->where('id', $applicationId);
            }
            
            $applicationDoc = $query->first();
            
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

        if (strpos($link, 'drive.google.com') !== false || strpos($link, 'docs.google.com') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Generate a unique application number
     * Format: YY + ZIPCODE + SEQUENCE (10 digits total)
     */
    private function generateApplicationNumber($user)
    {
        $year = date('y');
        $zipcode = $user->zip_code ?? '0000';
        $zipcode = str_pad(substr($zipcode, 0, 4), 4, '0', STR_PAD_LEFT);
        $prefix = $year . $zipcode;
        
        $lastApplication = ApplicationDocument::where('application_number', 'LIKE', $prefix . '%')
            ->whereNotNull('application_number')
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = 1;
        
        if ($lastApplication && $lastApplication->application_number) {
            $lastNumber = $lastApplication->application_number;
            $lastSequence = (int) substr($lastNumber, -4);
            $sequence = $lastSequence + 1;
        }
        
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        $applicationNumber = $prefix . $sequenceFormatted;
        
        while (ApplicationDocument::where('application_number', $applicationNumber)->exists()) {
            $sequence++;
            $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);
            $applicationNumber = $prefix . $sequenceFormatted;
        }
        
        return $applicationNumber;
    }

    /**
     * Submit application (change status from draft to pending)
     * Generate application number here, not earlier
     */
    public function submitApplication(Request $request)
    {
        try {
            $user = Auth::user();
            $applicationId = $request->application_id;
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->where('status', 'draft')
                ->first();
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'No draft application found'
                ], 404);
            }
            
            // Check if document links are provided (from Step 2)
            $documentLinks = $application->document_links;
            $hasDocuments = $documentLinks && is_array($documentLinks) && count($documentLinks) > 0;
            
            // Also check if the older google_drive_link is provided
            $hasGoogleDriveLink = !empty($application->google_drive_link);
            
            if (!$hasDocuments && !$hasGoogleDriveLink) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please upload your documents in Step 2 before submitting.'
                ], 403);
            }
            
            // Check application limit before submitting
            if ($this->hasReachedApplicationLimit($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum limit of 3 applications.'
                ], 403);
            }
            
            // GENERATE APPLICATION NUMBER HERE (on submission)
            $applicationNumber = $this->generateApplicationNumber($user);
            $oldStatus = $application->status;
            
            $application->application_number = $applicationNumber;
            $application->status = 'pending';
            $application->submitted_at = now();
            $application->save();

            Log::info('Application submitted and number generated', [
                'application_id' => $application->id,
                'application_number' => $applicationNumber,
                'user_id' => $user->id
            ]);

            // Send email notification with application number
            try {
                $this->notificationService->sendApplicationSubmittedEmail($application, $user);
                Log::info('Application submission email sent to: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Failed to send submission email: ' . $e->getMessage());
            }

            // Send notification to staff
            try {
                $this->notificationService->notifyStaffNewApplication($application);
            } catch (\Exception $e) {
                Log::error('Failed to send staff notification: ' . $e->getMessage());
            }

            // Log the activity
            if (class_exists('App\Models\ApplicationReviewActivity')) {
                try {
                    ApplicationReviewActivity::create([
                        'application_id' => $application->id,
                        'reviewer_id' => $user->id,
                        'action' => 'application_submitted',
                        'old_status' => $oldStatus,
                        'new_status' => 'pending',
                        'remarks' => "Application submitted. Number: {$applicationNumber}",
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to log activity: ' . $e->getMessage());
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => [
                    'application_number' => $applicationNumber,
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
            
            $submittedCount = ApplicationDocument::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'under-review', 'document-verification', 'approved', 'for-release', 'verified'])
                ->count();
                
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
     * Check if user has reached the application limit
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
                'user_zipcode' => $user->zip_code,
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
}