<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ApplicationReviewActivity;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ApplicationController extends Controller
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
     * Display a listing of the user's applications (for API)
     */
    public function index()
    {
        try {
            Log::info('ApplicationController@index started');
            
            $user = Auth::user();
            Log::info('User authenticated', ['user_id' => $user ? $user->id : null]);

            if (!$user) {
                Log::error('User not authenticated in index method');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'applications' => []
                ], 401);
            }

            // Check if table exists
            if (!Schema::hasTable('application_documents')) {
                Log::error('application_documents table does not exist');
                return response()->json([
                    'success' => false,
                    'message' => 'Database table not found',
                    'applications' => []
                ], 500);
            }

            // Get all applications for the user
            $applications = ApplicationDocument::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            Log::info('Applications retrieved', ['count' => $applications->count()]);

            $formattedApplications = [];
            foreach ($applications as $app) {
                try {
                    $formattedApplications[] = [
                        'id' => $app->id,
                        'application_number' => $app->application_number,
                        'google_drive_link' => $app->google_drive_link,
                        'status' => $app->status,
                        'status_display' => $this->formatStatus($app->status),
                        'rejection_reason' => $app->rejection_reason,
                        'admin_notes' => $app->admin_notes,
                        'created_at' => $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : null,
                        'updated_at' => $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : null,
                        'hard_copy_received' => $app->hard_copy_received ?? false,
                        'hard_copy_received_at' => $app->hard_copy_received_at ? $app->hard_copy_received_at->format('Y-m-d H:i:s') : null,
                        'last_updated_by' => $app->last_updated_by,
                        'project_name' => 'Building Permit Application',
                        'progress' => $this->calculateProgress($app->status)
                    ];
                } catch (\Exception $e) {
                    Log::error('Error formatting application', [
                        'application_id' => $app->id,
                        'error' => $e->getMessage()
                    ]);
                    // Skip this application but continue
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'applications' => $formattedApplications,
                'total' => count($formattedApplications)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading applications: ' . $e->getMessage(),
                'applications' => []
            ], 500);
        }
    }

    /**
     * Store a newly created application
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Validate request
            $request->validate([
                'google_drive_link' => 'required|url',
            ]);

            // Check if user has reached the application limit
            if ($this->hasReachedApplicationLimit($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum limit of 3 applications.'
                ], 403);
            }

            // Generate application number
            $applicationNumber = $this->generateApplicationNumber();

            // Create application
            $application = ApplicationDocument::create([
                'user_id' => $user->id,
                'application_number' => $applicationNumber,
                'google_drive_link' => $request->google_drive_link,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Log the activity
            try {
                if (class_exists('App\Models\ApplicationReviewActivity')) {
                    ApplicationReviewActivity::create([
                        'application_id' => $application->id,
                        'reviewer_id' => $user->id,
                        'action' => 'application_created',
                        'old_status' => null,
                        'new_status' => 'pending',
                        'remarks' => 'Application created via API',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to log activity: ' . $e->getMessage());
            }

            // TRIGGER NOTIFICATION: Notify staff about new application
            try {
                $this->notificationService->notifyStaffNewApplication($application);
            } catch (\Exception $e) {
                Log::error('Failed to send notification: ' . $e->getMessage());
            }

            Log::info('Application created successfully', [
                'application_id' => $application->id,
                'application_number' => $application->application_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => [
                    'id' => $application->id,
                    'application_number' => $application->application_number,
                    'status' => $application->status
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating application: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error submitting application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application details for a specific application
     */
    public function show($id)
    {
        try {
            Log::info('Fetching application details for ID: ' . $id);
            
            $user = Auth::user();

            if (!$user) {
                Log::error('User not authenticated');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            Log::info('User ID: ' . $user->id);
            
            // Try to find the application
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$application) {
                Log::error('Application not found for ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            Log::info('Application found: ' . $application->application_number);

            // Get last updated by user if exists
            $lastUpdatedBy = null;
            if ($application->last_updated_by) {
                $lastUpdatedBy = User::find($application->last_updated_by);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'application_number' => $application->application_number,
                    'google_drive_link' => $application->google_drive_link,
                    'status' => $application->status,
                    'status_display' => $this->formatStatus($application->status),
                    'rejection_reason' => $application->rejection_reason,
                    'admin_notes' => $application->admin_notes,
                    'created_at' => $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $application->updated_at ? $application->updated_at->format('Y-m-d H:i:s') : null,
                    'hard_copy_received' => $application->hard_copy_received ?? false,
                    'hard_copy_status' => $this->getHardCopyStatus($application),
                    'progress' => $this->calculateProgress($application->status),
                    'last_updated_by' => $application->last_updated_by,
                    'last_updated_by_name' => $lastUpdatedBy ? $lastUpdatedBy->first_name . ' ' . $lastUpdatedBy->last_name : null,
                    'last_updated_by_role' => $lastUpdatedBy ? $lastUpdatedBy->role : null,
                    'last_updated_by_email' => $lastUpdatedBy ? $lastUpdatedBy->email : null,
                    'last_updated_by_initials' => $lastUpdatedBy ? 
                        strtoupper(substr($lastUpdatedBy->first_name, 0, 1) . substr($lastUpdatedBy->last_name, 0, 1)) : null
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@show: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading application details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get review activities for an application (for applicant view)
     */
    public function getReviewActivities($id)
    {
        try {
            Log::info('Fetching review activities for application ID: ' . $id);
            
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'activities' => []
                ], 401);
            }

            // Verify the application belongs to the user
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$application) {
                Log::error('Application not found for ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found',
                    'activities' => []
                ], 404);
            }

            // Check if table exists
            if (!Schema::hasTable('application_review_activities')) {
                return response()->json([
                    'success' => true,
                    'activities' => [],
                    'message' => 'Review activities table not found'
                ]);
            }

            // Get review activities with reviewer information
            $activities = ApplicationReviewActivity::where('application_id', $id)
                ->with('reviewer')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($activity) {
                    $reviewerInfo = null;
                    
                    if ($activity->reviewer) {
                        $reviewerInfo = [
                            'id' => $activity->reviewer->id,
                            'name' => $activity->reviewer->first_name . ' ' . $activity->reviewer->last_name,
                            'role' => $activity->reviewer->role,
                            'email' => $activity->reviewer->email,
                            'initials' => strtoupper(substr($activity->reviewer->first_name, 0, 1) . substr($activity->reviewer->last_name, 0, 1))
                        ];
                    } else {
                        // If reviewer not found, try to get from reviewer_id
                        $reviewer = User::find($activity->reviewer_id);
                        if ($reviewer) {
                            $reviewerInfo = [
                                'id' => $reviewer->id,
                                'name' => $reviewer->first_name . ' ' . $reviewer->last_name,
                                'role' => $reviewer->role,
                                'email' => $reviewer->email,
                                'initials' => strtoupper(substr($reviewer->first_name, 0, 1) . substr($reviewer->last_name, 0, 1))
                            ];
                        }
                    }
                    
                    return [
                        'id' => $activity->id,
                        'application_id' => $activity->application_id,
                        'reviewer_id' => $activity->reviewer_id,
                        'action' => $activity->action,
                        'action_display' => $this->getActionDisplay($activity->action),
                        'old_status' => $activity->old_status,
                        'new_status' => $activity->new_status,
                        'remarks' => $activity->remarks,
                        'created_at' => $activity->created_at ? $activity->created_at->format('Y-m-d H:i:s') : null,
                        'created_at_formatted' => $activity->created_at ? $activity->created_at->format('M d, Y h:i A') : null,
                        'time_ago' => $activity->created_at ? $activity->created_at->diffForHumans() : null,
                        'reviewer' => $reviewerInfo
                    ];
                });

            Log::info('Found ' . count($activities) . ' review activities');

            return response()->json([
                'success' => true,
                'activities' => $activities
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading review activities: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading review activities',
                'activities' => []
            ], 500);
        }
    }

    /**
     * Delete a draft application
     */
    public function destroy($id)
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
                ->where('id', $id)
                ->where('status', 'draft')
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Draft application not found'
                ], 404);
            }

            // Log the deletion activity
            try {
                if (class_exists('App\Models\ApplicationReviewActivity')) {
                    ApplicationReviewActivity::create([
                        'application_id' => $application->id,
                        'reviewer_id' => $user->id,
                        'action' => 'application_deleted',
                        'old_status' => 'draft',
                        'new_status' => null,
                        'remarks' => 'Draft application deleted by applicant',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to log deletion activity: ' . $e->getMessage());
            }

            $application->delete();

            return response()->json([
                'success' => true,
                'message' => 'Draft application deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@destroy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application statistics
     */
    public function getStats()
    {
        try {
            Log::info('ApplicationController@getStats started');
            
            $user = Auth::user();
            
            if (!$user) {
                Log::warning('User not authenticated in getStats');
                return response()->json([
                    'total' => 0,
                    'draft' => 0,
                    'pending' => 0,
                    'under_review' => 0,
                    'document_verification' => 0,
                    'approved' => 0,
                    'for_release' => 0,
                    'verified' => 0,
                    'rejected' => 0
                ]);
            }

            // Check if table exists
            if (!Schema::hasTable('application_documents')) {
                Log::error('application_documents table does not exist');
                return response()->json([
                    'total' => 0,
                    'draft' => 0,
                    'pending' => 0,
                    'under_review' => 0,
                    'document_verification' => 0,
                    'approved' => 0,
                    'for_release' => 0,
                    'verified' => 0,
                    'rejected' => 0
                ]);
            }

            $stats = [
                'total' => ApplicationDocument::where('user_id', $user->id)->count(),
                'draft' => ApplicationDocument::where('user_id', $user->id)->where('status', 'draft')->count(),
                'pending' => ApplicationDocument::where('user_id', $user->id)->where('status', 'pending')->count(),
                'under_review' => ApplicationDocument::where('user_id', $user->id)->where('status', 'under-review')->count(),
                'document_verification' => ApplicationDocument::where('user_id', $user->id)->where('status', 'document-verification')->count(),
                'approved' => ApplicationDocument::where('user_id', $user->id)->where('status', 'approved')->count(),
                'for_release' => ApplicationDocument::where('user_id', $user->id)->where('status', 'for-release')->count(),
                'verified' => ApplicationDocument::where('user_id', $user->id)->where('status', 'verified')->count(),
                'rejected' => ApplicationDocument::where('user_id', $user->id)->where('status', 'rejected')->count()
            ];

            Log::info('Stats retrieved', $stats);
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@getStats: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'total' => 0,
                'draft' => 0,
                'pending' => 0,
                'under_review' => 0,
                'document_verification' => 0,
                'approved' => 0,
                'for_release' => 0,
                'verified' => 0,
                'rejected' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug review activities
     */
    public function debugReviewActivities($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ]);
            }

            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ]);
            }

            $tableExists = Schema::hasTable('application_review_activities');
            $activities = [];
            
            if ($tableExists) {
                $activities = ApplicationReviewActivity::where('application_id', $id)->get();
            }
            
            return response()->json([
                'success' => true,
                'table_exists' => $tableExists,
                'application_id' => $id,
                'activities_count' => count($activities),
                'activities' => $activities,
                'application' => $application
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate a unique application number
     */
    private function generateApplicationNumber()
    {
        $year = date('Y');
        do {
            $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $applicationNumber = $year . $random;
        } while (ApplicationDocument::where('application_number', $applicationNumber)->exists());

        return $applicationNumber;
    }

    /**
     * Calculate application progress based on status
     */
    private function calculateProgress($status)
    {
        $progressMap = [
            'draft' => 0,
            'pending' => 20,
            'under-review' => 40,
            'document-verification' => 60,
            'approved' => 80,
            'for-release' => 90,
            'verified' => 100,
            'rejected' => 100
        ];

        return $progressMap[$status] ?? 0;
    }

    /**
     * Get hard copy status text
     */
    private function getHardCopyStatus($application)
    {
        if ($application->hard_copy_received) {
            return [
                'text' => 'Received',
                'color' => 'green',
                'message' => 'Hard copies received by OBO'
            ];
        } elseif ($application->status === 'verified') {
            return [
                'text' => 'Verified',
                'color' => 'green',
                'message' => 'Verified by OBO'
            ];
        } elseif ($application->status === 'pending') {
            return [
                'text' => 'Pending',
                'color' => 'yellow',
                'message' => 'Awaiting hard copy submission'
            ];
        } elseif ($application->status === 'rejected') {
            return [
                'text' => 'N/A',
                'color' => 'gray',
                'message' => 'Application rejected'
            ];
        } else {
            return [
                'text' => 'Not Submitted',
                'color' => 'gray',
                'message' => 'Submit hard copies to OBO'
            ];
        }
    }

    /**
     * Format status for display
     */
    private function formatStatus($status)
    {
        if (!$status) return 'Unknown';
        return ucfirst(str_replace('-', ' ', $status));
    }

    /**
     * Get action display text
     */
    private function getActionDisplay($action)
    {
        return match($action) {
            'status_updated' => 'Status Updated',
            'note_added' => 'Note Added',
            'document_verified' => 'Documents Verified',
            'hard_copy_received' => 'Hard Copy Received',
            'application_created' => 'Application Created',
            'application_deleted' => 'Application Deleted',
            'application_submitted' => 'Application Submitted',
            'document_rejected' => 'Documents Rejected',
            'review_started' => 'Review Started',
            'review_completed' => 'Review Completed',
            'assigned_to_staff' => 'Assigned to Staff',
            'returned_for_revision' => 'Returned for Revision',
            'forwarded_to_engineer' => 'Forwarded to Engineer',
            'forwarded_to_building_official' => 'Forwarded to Building Official',
            'payment_verified' => 'Payment Verified',
            'payment_rejected' => 'Payment Rejected',
            'certificate_generated' => 'Certificate Generated',
            'certificate_released' => 'Certificate Released',
            default => ucfirst(str_replace('_', ' ', $action))
        };
    }

    /**
     * Check if user has reached application limit
     */
    private function hasReachedApplicationLimit($user)
    {
        $count = ApplicationDocument::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'under-review', 'document-verification', 'approved', 'for-release', 'verified'])
            ->count();
        return $count >= 3;
    }
}