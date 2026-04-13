<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\BasicRequirement;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BasicRequirementController extends Controller
{
    /**
     * Show the basic requirements submission form
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $applicationId = $request->get('application_id');
        $application = null;
        $basicRequirement = null;
        
        if ($applicationId) {
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->first();
            
            if ($application) {
                $basicRequirement = BasicRequirement::where('application_id', $applicationId)->first();
            }
        }
        
        // If no application found, create a new draft application
        if (!$application && !$applicationId) {
            // Check if user has reached the application limit
            $submittedCount = ApplicationDocument::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'under-review', 'document-verification', 'approved', 'for-release', 'verified'])
                ->count();
                
            if ($submittedCount >= 3) {
                return redirect()->route('applicant.applications')
                    ->with('error', 'You have reached the maximum limit of 3 applications.');
            }
            
            // Create a new draft application WITHOUT application number
            $application = ApplicationDocument::create([
                'user_id' => $user->id,
                'application_number' => null,
                'status' => 'draft',
                'google_drive_link' => null
            ]);
            
            Log::info('New draft application created for basic requirements', [
                'application_id' => $application->id,
                'user_id' => $user->id
            ]);
            
            return redirect()->route('applicant.basic-requirements.index', ['application_id' => $application->id]);
        }
        
        // If application not found for the given ID, redirect to applications list
        if (!$application && $applicationId) {
            return redirect()->route('applicant.applications')
                ->with('error', 'Application not found.');
        }
        
        return view('applicant.basic-requirements.index', compact('basicRequirement', 'application'));
    }

    /**
     * Store or update basic requirements
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:application_documents,id',
            'tct_link' => 'required|url',
            'tax_declaration_link' => 'required|url',
            'current_tax_receipt_link' => 'required|url',
            'spa_link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $request->application_id)
                ->first();
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 404);
            }
            
            // Check if already has approved requirements for this application
            $existing = BasicRequirement::where('application_id', $application->id)
                ->where('status', 'approved')
                ->first();
                
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Basic requirements for this application have already been approved. You cannot submit again.'
                ], 403);
            }

            $existingRequirement = BasicRequirement::where('application_id', $application->id)->first();
            
            $data = [
                'user_id' => $user->id,
                'application_id' => $application->id,
                'tct_link' => $request->tct_link,
                'tax_declaration_link' => $request->tax_declaration_link,
                'current_tax_receipt_link' => $request->current_tax_receipt_link,
                'spa_link' => $request->spa_link,
                'status' => 'pending',
                'submitted_at' => now(),
            ];

            if ($existingRequirement && $existingRequirement->status === 'rejected') {
                $data['rejection_reason'] = null;
            }

            $basicRequirement = BasicRequirement::updateOrCreate(
                ['application_id' => $application->id],
                $data
            );

            Log::info('Basic requirements submitted for application', [
                'user_id' => $user->id,
                'application_id' => $application->id,
                'requirement_id' => $basicRequirement->id,
                'status' => $basicRequirement->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Basic requirements submitted successfully. Please wait for staff approval.',
                'application_id' => $application->id,
                'requirement_id' => $basicRequirement->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error submitting basic requirements: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error submitting requirements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check status of basic requirements for an application
     */
    public function checkStatus(Request $request)
    {
        try {
            $user = Auth::user();
            $applicationId = $request->get('application_id');
            
            if (!$applicationId) {
                return response()->json([
                    'has_submitted' => false,
                    'status' => 'no_application',
                    'message' => 'No application specified.'
                ]);
            }
            
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->first();
                
            if (!$application) {
                return response()->json([
                    'has_submitted' => false,
                    'status' => 'application_not_found',
                    'message' => 'Application not found.'
                ]);
            }
            
            $requirement = BasicRequirement::where('application_id', $applicationId)->first();
            
            if (!$requirement) {
                return response()->json([
                    'has_submitted' => false,
                    'status' => 'not_submitted',
                    'message' => 'You have not submitted any requirements for this application yet.'
                ]);
            }
            
            $statusMessages = [
                'pending' => 'Your requirements are pending review by staff.',
                'approved' => 'Your requirements have been approved! You may now proceed to Step 1.',
                'rejected' => 'Your requirements were rejected. Please check the reason and resubmit.'
            ];
            
            return response()->json([
                'has_submitted' => true,
                'status' => $requirement->status,
                'message' => $statusMessages[$requirement->status] ?? 'Status unknown',
                'rejection_reason' => $requirement->rejection_reason,
                'submitted_at' => $requirement->submitted_at?->format('Y-m-d H:i:s'),
                'approved_at' => $requirement->approved_at?->format('Y-m-d H:i:s'),
                'application_status' => $application->status,
                'has_application_number' => !is_null($application->application_number)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking basic requirements status: ' . $e->getMessage());
            return response()->json([
                'has_submitted' => false,
                'status' => 'error',
                'message' => 'Error checking status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can proceed to step 1 for a specific application
     */
    public function canProceed(Request $request)
    {
        try {
            $user = Auth::user();
            $applicationId = $request->get('application_id');
            
            if (!$applicationId) {
                return response()->json([
                    'can_proceed' => false,
                    'status' => 'no_application',
                    'message' => 'No application specified.'
                ]);
            }
            
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->first();
                
            if (!$application) {
                return response()->json([
                    'can_proceed' => false,
                    'status' => 'application_not_found',
                    'message' => 'Application not found.'
                ]);
            }
            
            $requirement = BasicRequirement::where('application_id', $applicationId)
                ->where('status', 'approved')
                ->first();
            
            $canProceed = !is_null($requirement);
            
            return response()->json([
                'can_proceed' => $canProceed,
                'status' => $requirement ? 'approved' : 'not_approved',
                'application_id' => $applicationId,
                'application_number' => $application->application_number,
                'message' => $canProceed ? 'You can proceed to Step 1.' : 'Basic requirements must be approved first.',
                'redirect_url' => $canProceed ? route('applicant.application.step1', ['id' => $applicationId]) : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking if user can proceed: ' . $e->getMessage());
            return response()->json([
                'can_proceed' => false,
                'status' => 'error',
                'message' => 'Error checking status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the basic requirements details for an application
     */
    public function getDetails($applicationId)
    {
        try {
            $user = Auth::user();
            
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->first();
                
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 404);
            }
            
            $requirement = BasicRequirement::where('application_id', $applicationId)->first();
            
            if (!$requirement) {
                return response()->json([
                    'success' => true,
                    'has_requirements' => false,
                    'message' => 'No basic requirements found for this application.'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'has_requirements' => true,
                'data' => [
                    'id' => $requirement->id,
                    'application_id' => $requirement->application_id,
                    'application_number' => $application->application_number,
                    'status' => $requirement->status,
                    'status_display' => $this->getStatusDisplay($requirement->status),
                    'status_color' => $this->getStatusColor($requirement->status),
                    'submitted_at' => $requirement->submitted_at,
                    'approved_at' => $requirement->approved_at,
                    'rejection_reason' => $requirement->rejection_reason,
                    'tct_link' => $requirement->tct_link,
                    'tax_declaration_link' => $requirement->tax_declaration_link,
                    'current_tax_receipt_link' => $requirement->current_tax_receipt_link,
                    'spa_link' => $requirement->spa_link
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting basic requirements details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status display text
     */
    private function getStatusDisplay($status)
    {
        return match($status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst($status)
        };
    }

    /**
     * Get status color for UI
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }
}