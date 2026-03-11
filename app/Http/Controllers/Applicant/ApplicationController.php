<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ApplicationReviewActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the user's applications (for API)
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'applications' => []
                ], 401);
            }

            // Get all applications for the user
            $applications = ApplicationDocument::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedApplications = [];
            foreach ($applications as $app) {
                $formattedApplications[] = [
                    'id' => $app->id,
                    'application_number' => $app->application_number,
                    'google_drive_link' => $app->google_drive_link,
                    'status' => $app->status,
                    'status_display' => $this->formatStatus($app->status),
                    'rejection_reason' => $app->rejection_reason,
                    'created_at' => $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : null,
                    'hard_copy_received' => $app->hard_copy_received ?? false,
                    'last_updated_by' => $app->last_updated_by,
                    'project_name' => 'Building Permit Application',
                    'progress' => $this->calculateProgress($app->status)
                ];
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
                    'message' => 'User not authenticated'
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
                    'message' => 'Application not found'
                ], 404);
            }

            // Get review activities with reviewer information
            $activities = ApplicationReviewActivity::where('application_id', $id)
                ->with('reviewer') // Make sure this relationship exists in the model
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($activity) {
                    // Format the activity for the frontend
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
                    
                    // Determine action display text
                    $actionDisplay = $this->getActionDisplay($activity->action);
                    
                    return [
                        'id' => $activity->id,
                        'application_id' => $activity->application_id,
                        'reviewer_id' => $activity->reviewer_id,
                        'action' => $activity->action,
                        'action_display' => $actionDisplay,
                        'old_status' => $activity->old_status,
                        'new_status' => $activity->new_status,
                        'remarks' => $activity->remarks,
                        'created_at' => $activity->created_at,
                        'created_at_formatted' => $activity->created_at->format('Y-m-d H:i:s'),
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
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'total' => 0,
                    'pending' => 0,
                    'under_review' => 0,
                    'document_verification' => 0,
                    'approved' => 0,
                    'for_release' => 0,
                    'verified' => 0,
                    'rejected' => 0,
                    'draft' => 0
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

            return response()->json($stats);
            
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@getStats: ' . $e->getMessage());
            
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
            default => ucfirst(str_replace('_', ' ', $action))
        };
    }
}