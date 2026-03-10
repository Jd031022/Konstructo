<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
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
                    'rejection_reason' => $app->rejection_reason,
                    'created_at' => $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : null,
                    'project_name' => 'Building Permit Application',
                    'location' => null,
                    'project_type' => null
                ];
            }

            return response()->json([
                'success' => true,
                'applications' => $formattedApplications,
                'total' => count($formattedApplications)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@index: ' . $e->getMessage());
            
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
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'application_number' => $application->application_number,
                    'google_drive_link' => $application->google_drive_link,
                    'status' => $application->status,
                    'rejection_reason' => $application->rejection_reason,
                    'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $application->updated_at->format('Y-m-d H:i:s'),
                    'hard_copy_status' => $this->getHardCopyStatus($application),
                    'progress' => $this->calculateProgress($application)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@show: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading application details: ' . $e->getMessage()
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
                    'verified' => 0,
                    'rejected' => 0,
                    'draft' => 0
                ]);
            }

            $stats = [
                'total' => ApplicationDocument::where('user_id', $user->id)->count(),
                'pending' => ApplicationDocument::where('user_id', $user->id)->where('status', 'pending')->count(),
                'verified' => ApplicationDocument::where('user_id', $user->id)->where('status', 'verified')->count(),
                'rejected' => ApplicationDocument::where('user_id', $user->id)->where('status', 'rejected')->count(),
                'draft' => ApplicationDocument::where('user_id', $user->id)->where('status', 'draft')->count()
            ];

            return response()->json($stats);
            
        } catch (\Exception $e) {
            Log::error('Error in ApplicationController@getStats: ' . $e->getMessage());
            
            return response()->json([
                'total' => 0,
                'pending' => 0,
                'verified' => 0,
                'rejected' => 0,
                'draft' => 0
            ]);
        }
    }

    /**
     * Calculate application progress based on status
     */
    private function calculateProgress($application)
    {
        $progressMap = [
            'draft' => 25,
            'pending' => 65,
            'verified' => 100,
            'rejected' => 100
        ];

        return $progressMap[$application->status] ?? 0;
    }

    /**
     * Get hard copy status text
     */
    private function getHardCopyStatus($application)
    {
        if ($application->status === 'verified') {
            return [
                'text' => 'Received',
                'color' => 'green',
                'message' => 'Verified by OBO'
            ];
        } elseif ($application->status === 'pending') {
            return [
                'text' => 'Pending',
                'color' => 'yellow',
                'message' => 'Awaiting verification'
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
}