<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\User;
use App\Models\ApplicationReviewActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ApplicationController extends Controller
{
    /**
     * Display a listing of all submitted applications (excluding drafts)
     */
    public function index()
    {
        try {
            Log::info('Fetching all staff applications');
            
            // Get all applications EXCLUDING drafts
            $applications = ApplicationDocument::with(['user', 'lastUpdatedBy'])
                ->whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedApplications = [];
            foreach ($applications as $app) {
                $formattedApplications[] = [
                    'id' => $app->id,
                    'application_number' => $app->application_number,
                    'applicant_name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                    'email' => $app->user ? $app->user->email : null,
                    'phone' => $app->user ? $app->user->phone_number : null,
                    'address' => $app->user ? $app->user->address : null,
                    'google_drive_link' => $app->google_drive_link,
                    'status' => $app->status,
                    'rejection_reason' => $app->rejection_reason,
                    'admin_notes' => $app->admin_notes,
                    'created_at' => $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : null,
                    'hard_copy_received' => $app->hard_copy_received ?? false,
                    'last_updated_by' => $app->last_updated_by,
                    'last_updated_by_name' => $app->lastUpdatedBy ? $app->lastUpdatedBy->first_name . ' ' . $app->lastUpdatedBy->last_name : null,
                    'last_updated_by_role' => $app->lastUpdatedBy ? $app->lastUpdatedBy->role : null
                ];
            }
            
            return response()->json([
                'success' => true,
                'applications' => $formattedApplications,
                'total' => count($formattedApplications)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading staff applications: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading applications: ' . $e->getMessage(),
                'applications' => []
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
                ->whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
                ->find($id);
            
            if (!$application) {
                Log::error('Application not found for ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }
            
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
                    'applicant_name' => $application->user ? $application->user->first_name . ' ' . $application->user->last_name : 'Unknown',
                    'email' => $application->user ? $application->user->email : null,
                    'phone' => $application->user ? $application->user->phone_number : null,
                    'address' => $application->user ? $application->user->address : null,
                    'google_drive_link' => $application->google_drive_link,
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
                        strtoupper(substr($lastUpdatedBy->first_name, 0, 1) . substr($lastUpdatedBy->last_name, 0, 1)) : 'ST'
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
     * Store a new application (created by staff)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:11',
            'address' => 'required|string',
            'google_drive_link' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user first
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone,
                'address' => $request->address,
                'username' => $this->generateUsername($request->first_name, $request->last_name),
                'password' => bcrypt('defaultpassword123'),
                'role' => 'applicant',
                'email_verified_at' => now(),
            ]);
            
            // Generate application number
            $year = date('Y');
            do {
                $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $applicationNumber = $year . $random;
            } while (ApplicationDocument::where('application_number', $applicationNumber)->exists());
            
            // Create application
            $application = ApplicationDocument::create([
                'user_id' => $user->id,
                'application_number' => $applicationNumber,
                'google_drive_link' => $request->google_drive_link,
                'status' => 'pending',
                'rejection_reason' => null,
                'verified_at' => null,
                'verified_by' => null,
                'hard_copy_received' => false
            ]);
            
            // Log the creation
            $this->logReviewActivity(
                $application->id,
                auth()->id(),
                'application_created',
                null,
                'pending',
                "Application #{$applicationNumber} created for {$user->first_name} {$user->last_name}",
                $request->ip(),
                $request->userAgent()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Application created successfully',
                'data' => [
                    'application_number' => $applicationNumber,
                    'status' => 'pending'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating application: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, $id)
    {
        // Log the incoming request for debugging
        Log::info('Updating application status', [
            'application_id' => $id,
            'request_data' => $request->all()
        ]);

        // Validate the request including hardcopy_received
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,under-review,approved,rejected,for-release,verified',
            'remarks' => 'nullable|string',
            'hardcopy_received' => 'sometimes|boolean'
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
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }
            
            // Get the current staff user
            $staff = auth()->user();
            
            // Store old status for logging
            $oldStatus = $application->status;
            
            // Update status - make sure it's a string
            $application->status = $request->status;
            
            // Update admin notes if provided
            if ($request->has('remarks')) {
                $application->admin_notes = $request->remarks;
            }
            
            // Check if hard_copy_received column exists before using it
            if ($request->has('hardcopy_received') && Schema::hasColumn('application_documents', 'hard_copy_received')) {
                $application->hard_copy_received = $request->hardcopy_received;
            }
            
            // Track who updated the application
            $application->last_updated_by = $staff->id;
            
            // If status is verified, set verified_at and verified_by
            if ($request->status === 'verified') {
                $application->verified_at = now();
                $application->verified_by = $staff->id;
            }
            
            // If status is rejected, store rejection reason
            if ($request->status === 'rejected' && $request->has('remarks')) {
                $application->rejection_reason = $request->remarks;
            }
            
            // Save the application
            $application->save();
            
            // Create activity log entry
            $description = $request->remarks ?: "Status changed from {$oldStatus} to {$request->status}";
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                'status_updated',
                $oldStatus,
                $request->status,
                $description,
                $request->ip(),
                $request->userAgent()
            );
            
            Log::info('Application status updated successfully', [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'new_status' => $application->status,
                'updated_by' => $staff->id,
                'updated_by_name' => $staff->first_name . ' ' . $staff->last_name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully',
                'data' => [
                    'id' => $application->id,
                    'status' => $application->status,
                    'hard_copy_received' => $application->hard_copy_received ?? false,
                    'updated_at' => $application->updated_at,
                    'updated_by' => [
                        'id' => $staff->id,
                        'name' => $staff->first_name . ' ' . $staff->last_name,
                        'role' => $staff->role,
                        'email' => $staff->email,
                        'initials' => strtoupper(substr($staff->first_name, 0, 1) . substr($staff->last_name, 0, 1))
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating application status: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating application status: ' . $e->getMessage()
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
            
            // Log before deleting
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
     * Export applications (CSV)
     */
    public function export()
    {
        try {
            $applications = ApplicationDocument::with(['user', 'lastUpdatedBy'])
                ->whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
                ->get();
            
            $filename = 'applications_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($applications) {
                $handle = fopen('php://output', 'w');
                
                // Add CSV headers
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
                
                // Add data rows
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
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Error exporting applications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error exporting applications'
            ], 500);
        }
    }

    /**
     * Log review activity for an application
     */
    private function logReviewActivity($applicationId, $reviewerId, $action, $oldStatus = null, $newStatus = null, $remarks = null, $ipAddress = null, $userAgent = null)
    {
        try {
            // Check if ApplicationReviewActivity table exists
            if (!Schema::hasTable('application_review_activities')) {
                Log::warning('Application review activities table does not exist');
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
     * Generate username from first and last name
     */
    private function generateUsername($firstName, $lastName)
    {
        $base = strtolower($firstName . '.' . $lastName);
        $username = $base;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }
}