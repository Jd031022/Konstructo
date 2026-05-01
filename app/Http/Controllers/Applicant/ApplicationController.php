<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ApplicationReviewActivity;
use App\Models\User;
use App\Models\ClientSatisfactionSurvey;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\OwnershipVerification;
use App\Models\CPDORating; 

class ApplicationController extends Controller
{
    protected $notificationService;
    protected $maxApplicationsPerDay = 3; 

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
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
                'applications' => []
            ], 401);
        }

        if (!Schema::hasTable('application_documents')) {
            return response()->json([
                'success' => false,
                'message' => 'Database table not found',
                'applications' => []
            ], 500);
        }

        $applications = ApplicationDocument::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $formattedApplications = [];
        foreach ($applications as $app) {
            try {
                // Get project title
                $projectTitle = $app->project_title ?? null;
                if (!$projectTitle && $app->data && is_array($app->data)) {
                    $projectTitle = $app->data['project_title'] ?? null;
                }
                
                $formattedApplications[] = [
                    'id' => $app->id,
                    'application_number' => $app->application_number ?? 'Pending',
                    'building_permit_number' => $app->building_permit_number ?? null, // ADD THIS
                    'has_application_number' => !is_null($app->application_number),
                    'has_building_permit_number' => !is_null($app->building_permit_number), // ADD THIS
                    'google_drive_link' => $app->google_drive_link,
                    'document_links' => $app->document_links,
                    'status' => $app->status,
                    'status_display' => $this->formatStatus($app->status),
                    'rejection_reason' => $app->rejection_reason,
                    'admin_notes' => $app->admin_notes,
                    'created_at' => $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : null,
                    'submitted_at' => $app->submitted_at ? $app->submitted_at->format('Y-m-d H:i:s') : null,
                    'hard_copy_received' => $app->hard_copy_received ?? false,
                    'hard_copy_received_at' => $app->hard_copy_received_at ? $app->hard_copy_received_at->format('Y-m-d H:i:s') : null,
                    'last_updated_by' => $app->last_updated_by,
                    'project_title' => $projectTitle ?? 'Untitled Project',
                    'progress' => $this->calculateProgress($app->status),
                    'architect_name' => $app->architect_name ?? null,
                    'engineer_name' => $app->engineer_name ?? null,
                    'electrical_engineer_name' => $app->electrical_engineer_name ?? null,
                    'sanitary_engineer_name' => $app->sanitary_engineer_name ?? null
                ];
            } catch (\Exception $e) {
                Log::error('Error formatting application', [
                    'application_id' => $app->id,
                    'error' => $e->getMessage()
                ]);
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
        
        return response()->json([
            'success' => false,
            'message' => 'Error loading applications: ' . $e->getMessage(),
            'applications' => []
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

            if (!Schema::hasTable('application_documents')) {
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

        $lastUpdatedBy = null;
        if ($application->last_updated_by) {
            $lastUpdatedBy = User::find($application->last_updated_by);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'building_permit_number' => $application->building_permit_number, // ADD THIS
                'permit_remarks' => $application->permit_remarks, // ADD THIS
                'google_drive_link' => $application->google_drive_link,
                'document_links' => $application->document_links,
                'status' => $application->status,
                'status_display' => $this->formatStatus($application->status),
                'rejection_reason' => $application->rejection_reason,
                'admin_notes' => $application->admin_notes,
                'created_at' => $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $application->updated_at ? $application->updated_at->format('Y-m-d H:i:s') : null,
                'submitted_at' => $application->submitted_at ? $application->submitted_at->format('Y-m-d H:i:s') : null,
                'hard_copy_received' => $application->hard_copy_received ?? false,
                'hardcopy_submission_date' => $application->hardcopy_submission_date ?? null,
                'hardcopy_instructions' => $application->hardcopy_instructions ?? null,
                'hard_copy_status' => $this->getHardCopyStatus($application),
                'progress' => $this->calculateProgress($application->status),
                'last_updated_by' => $application->last_updated_by,
                'last_updated_by_name' => $lastUpdatedBy ? $lastUpdatedBy->first_name . ' ' . $lastUpdatedBy->last_name : null,
                // Project information from direct columns
                'project_title' => $application->project_title ?? null,
                'project_location' => $application->project_location ?? null,
                'project_type' => $application->project_type ?? null,
                'lot_area' => $application->lot_area ?? null,
                'floor_area' => $application->floor_area ?? null,
                'num_floors' => $application->num_floors ?? null,
                'estimated_cost' => $application->estimated_cost ?? null,
                'project_description' => $application->project_description ?? null,
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
                // CPDO Status
                'cpdo_status' => $application->cpdo_status ?? 'pending',
                'cpdo_remarks' => $application->cpdo_remarks ?? null,
                'cpdo_approved_at' => $application->cpdo_approved_at ?? null,
                'cpdo_approved_by' => $application->cpdo_approved_by ?? null,
                // Step completions
                'step1_completed' => $application->step1_completed ?? false,
                'step2_completed' => $application->step2_completed ?? false,
                'step3_completed' => $application->step3_completed ?? false,
                'step1_completed_at' => $application->step1_completed_at ?? null,
                'step2_completed_at' => $application->step2_completed_at ?? null,
                'step3_completed_at' => $application->step3_completed_at ?? null
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
     * Check if application number exists
     */
    public function checkApplicationNumber(Request $request)
    {
        try {
            $number = $request->query('number');
            
            if (!$number) {
                return response()->json([
                    'exists' => false
                ]);
            }
            
            $exists = ApplicationDocument::where('application_number', $number)->exists();
            
            return response()->json([
                'exists' => $exists
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking application number: ' . $e->getMessage());
            return response()->json([
                'exists' => false,
                'error' => $e->getMessage()
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
     * Get review activities for an application
     */
    public function getReviewActivities($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'activities' => []
                ], 401);
            }

            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found',
                    'activities' => []
                ], 404);
            }

            if (!Schema::hasTable('application_review_activities')) {
                return response()->json([
                    'success' => true,
                    'activities' => []
                ]);
            }

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
                    }
                    
                    return [
                        'id' => $activity->id,
                        'application_id' => $activity->application_id,
                        'reviewer_id' => $activity->reviewer_id,
                        'action' => $activity->action,
                        'action_type' => $activity->action,
                        'action_display' => $this->getActionDisplay($activity->action),
                        'old_status' => $activity->old_status,
                        'new_status' => $activity->new_status,
                        'remarks' => $activity->remarks,
                        'created_at' => $activity->created_at ? $activity->created_at->format('Y-m-d H:i:s') : null,
                        'time_ago' => $activity->created_at ? $activity->created_at->diffForHumans() : null,
                        'reviewer' => $reviewerInfo,
                        'reviewer_name' => $reviewerInfo['name'] ?? 'System',
                        'reviewer_position' => $reviewerInfo['role'] ?? null
                    ];
                });

            return response()->json([
                'success' => true,
                'activities' => $activities
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading review activities: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading review activities',
                'activities' => []
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
     * Create draft application
     */
   public function createDraft(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Create new draft
            $application = ApplicationDocument::create([
                'user_id' => $user->id,
                'status' => 'draft',
                'application_number' => null,
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'status' => $application->status
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating draft: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate application number (10-digit sequential: YYYY + 6-digit sequence)
     */
    public function generateApplicationNumber(Request $request)
    {
        try {
            $request->validate([
                'application_id' => 'required|exists:application_documents,id'
            ]);
            
            $application = ApplicationDocument::find($request->application_id);
            
            // Check if user owns this application
            if ($application->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            // Check if application already has a number
            if (!is_null($application->application_number)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'application_number' => $application->application_number,
                        'application_id' => $application->id,
                        'already_exists' => true
                    ]
                ]);
            }
            
            // Generate 10-digit sequential number: Year + 6-digit sequence
            $year = date('Y');
            
            // Get the latest application number for this year
            $lastApplication = ApplicationDocument::whereYear('created_at', $year)
                ->whereNotNull('application_number')
                ->orderBy('id', 'desc')
                ->first();
            
            $sequence = 1;
            
            if ($lastApplication && $lastApplication->application_number) {
                // Extract the sequence part (last 6 digits)
                $lastNumber = $lastApplication->application_number;
                $lastSequence = (int) substr($lastNumber, -6);
                $sequence = $lastSequence + 1;
            }
            
            // Format as 6-digit with leading zeros
            $sequenceFormatted = str_pad($sequence, 6, '0', STR_PAD_LEFT);
            $applicationNumber = $year . $sequenceFormatted;
            
            // Ensure uniqueness (just in case)
            while (ApplicationDocument::where('application_number', $applicationNumber)->exists()) {
                $sequence++;
                $sequenceFormatted = str_pad($sequence, 6, '0', STR_PAD_LEFT);
                $applicationNumber = $year . $sequenceFormatted;
            }
            
            $application->application_number = $applicationNumber;
            $application->save();
            
            session(['current_application_number' => $applicationNumber]);
            session(['current_application_id' => $application->id]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'application_number' => $applicationNumber,
                    'application_id' => $application->id,
                    'already_exists' => false
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating application number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate application number: ' . $e->getMessage()
            ], 500);
        }
    }
 public function submitApplication(Request $request)
    {
        try {
            $user = Auth::user();
            $applicationId = $request->application_id;
            
            $application = ApplicationDocument::findOrFail($applicationId);
            
            // Check ownership
            if ($application->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            // Check if already submitted
            if ($application->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Application already submitted'
                ], 400);
            }
            
            // CHECK DAILY LIMIT BEFORE SUBMISSION
            if (!ApplicationDocument::canSubmitToday($user->id, $this->maxApplicationsPerDay)) {
                $usedToday = ApplicationDocument::getTodaySubmittedCount($user->id);
                $resetDate = today()->addDay()->toDateString();
                
                return response()->json([
                    'success' => false,
                    'limit_reached' => true,
                    'message' => "You have reached the limit of {$this->maxApplicationsPerDay} application(s) per day. Today's usage: {$usedToday}/{$this->maxApplicationsPerDay}. Your limit will reset on {$resetDate}."
                ], 403);
            }
            
            // Proceed with submission
            $applicationNumber = $this->generateApplicationNumberInternal(); // Create internal method
            
            $application->update([
                'status' => 'pending',
                'application_number' => $applicationNumber,
                'submitted_at' => now(),
                'submission_date' => today(),  // IMPORTANT: Set submission date for daily counting
            ]);
            
            // Trigger notification
            $this->notificationService->applicationSubmitted($application);
            
            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => [
                    'application_number' => $applicationNumber,
                    'submission_date' => today()->toDateString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error submitting application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get application limit info
     */
    public function getLimitInfo(Request $request)
    {
        try {
            $user = Auth::user();
            
            $todaySubmitted = ApplicationDocument::getTodaySubmittedCount($user->id);
            $remaining = ApplicationDocument::getRemainingToday($user->id, $this->maxApplicationsPerDay);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'today_submitted' => $todaySubmitted,
                    'remaining' => $remaining,
                    'max_per_day' => $this->maxApplicationsPerDay,
                    'can_submit_today' => ApplicationDocument::canSubmitToday($user->id, $this->maxApplicationsPerDay),
                    'next_reset' => today()->addDay()->toDateString(),
                    'reset_time' => '12:00 AM',
                    'note' => 'You can submit up to ' . $this->maxApplicationsPerDay . ' applications per day.'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save edited PDF with text elements
     */
    public function saveEditedPdf(Request $request)
    {
        try {
            $textElements     = $request->input('text_elements', []);
            $applicationNumber= $request->input('application_number', '');
            $iframeWidth      = (float)$request->input('iframe_width',  800);
            $iframeHeight     = (float)$request->input('iframe_height', 1100);

            $templatePath = public_path('downloads/building-permit-application.pdf');
            if (!file_exists($templatePath)) {
                return response()->json(['error' => 'PDF template not found'], 404);
            }

            $pdf = new Fpdi();
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);

            $pageCount  = $pdf->setSourceFile($templatePath);
            $templateId = $pdf->importPage(1);
            $size       = $pdf->getTemplateSize($templateId);

            $pdfW = $size['width'];
            $pdfH = $size['height'];

            $pdf->AddPage('P', [$pdfW, $pdfH]);
            $pdf->useTemplate($templateId, 0, 0, $pdfW, $pdfH);

            // Scale: 1 overlay-pixel = how many mm in the PDF
            $scaleX = $pdfW / $iframeWidth;
            $scaleY = $pdfH / $iframeHeight;

            foreach ($textElements as $text) {
                if (empty(trim($text['content'] ?? ''))) continue;

                // Screen px → PDF pt (screen is 96dpi, PDF is 72dpi)
                $fontSizePx = (int)($text['fontSize'] ?? 12);
                $fontSizePt = $fontSizePx * 0.75;

                $pdf->SetFont('helvetica', '', $fontSizePt);

                // Colour
                $color = $text['color'] ?? '#000000';
                $namedColors = ['black'=>'#000000','blue'=>'#0000FF','red'=>'#FF0000'];
                if (isset($namedColors[$color])) $color = $namedColors[$color];
                $hex = ltrim($color, '#');
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
                $pdf->SetTextColor($r, $g, $b);

                // Convert overlay pixel coords → PDF mm coords
                $overlayX = (float)($text['x'] ?? 0);
                $overlayY = (float)($text['y'] ?? 0);

                $pdfX = $overlayX * $scaleX;
                $pdfY = $overlayY * $scaleY;

                // Clamp to page
                $pdfX = max(0, min($pdfX, $pdfW - 1));
                $pdfY = max(0, min($pdfY, $pdfH - 1));

                $cellH = $fontSizePt * 0.3528;

                $pdf->SetXY($pdfX, $pdfY);
                $pdf->Cell(0, $cellH, $text['content'], 0, 0, 'L');
            }

            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);

            $outputPath = $tempDir . '/application-letter-' . time() . '.pdf';
            $pdf->Output($outputPath, 'F');

            return response()->download($outputPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Error saving edited PDF: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Save project information from Step 1
     */
    public function saveProjectInfo(Request $request)
    {
        Log::info('saveProjectInfo called', $request->all());
        
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:application_documents,id',
            'project_title' => 'required|string|max:255',
            'project_location' => 'required|string',
            'project_type' => 'required|string',
            'lot_area' => 'required|numeric|min:0',
            'floor_area' => 'required|numeric|min:0',
            'num_floors' => 'required|integer|min:1',
            'estimated_cost' => 'required|numeric|min:0',
            'project_description' => 'required|string',
            'owner_name' => 'required|string',
            'owner_address' => 'required|string',
            'contact_number' => 'required|string',
            'owner_email' => 'required|email',
            'architect_name' => 'required|string',
            'architect_license' => 'required|string',
            'engineer_name' => 'required|string',
            'engineer_license' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::findOrFail($request->application_id);
            
            if ($application->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            Log::info('Updating application', ['id' => $application->id]);
            
            $application->update([
                // Project Information
                'project_title' => $request->project_title,
                'project_location' => $request->project_location,
                'project_type' => $request->project_type,
                'lot_area' => $request->lot_area,
                'floor_area' => $request->floor_area,
                'num_floors' => $request->num_floors,
                'estimated_cost' => $request->estimated_cost,
                'project_description' => $request->project_description,
                
                // Owner Information
                'owner_name' => $request->owner_name,
                'owner_address' => $request->owner_address,
                'contact_number' => $request->contact_number,
                'owner_email' => $request->owner_email,
                
                // Professional Information
                'architect_name' => $request->architect_name,
                'architect_license' => $request->architect_license,
                'engineer_name' => $request->engineer_name,
                'engineer_license' => $request->engineer_license,
                'electrical_engineer_name' => $request->electrical_engineer_name,
                'electrical_engineer_license' => $request->electrical_engineer_license,
                'sanitary_engineer_name' => $request->sanitary_engineer_name,
                'sanitary_engineer_license' => $request->sanitary_engineer_license,
                
                // Step completion
                'step1_completed' => true,
                'step1_completed_at' => now(),
            ]);
            
            $updated = $application->fresh();
            Log::info('After update', [
                'project_title' => $updated->project_title,
                'engineer_name' => $updated->engineer_name,
                'engineer_license' => $updated->engineer_license,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Project information saved successfully',
                'data' => $application
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving project info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save project information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete Step 2 (Download Forms)
     */
    public function completeStep2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:application_documents,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::findOrFail($request->application_id);
            
            if ($application->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            $application->update([
                'step2_completed' => true,
                'step2_completed_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Step 2 completed successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error completing step 2: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete step 2: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete Step 3 (Upload Documents)
     */
    public function completeStep3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:application_documents,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::findOrFail($request->application_id);
            
            if ($application->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            $application->update([
                'step3_completed' => true,
                'step3_completed_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Step 3 completed successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error completing step 3: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete step 3: ' . $e->getMessage()
            ], 500);
        }
    }

    public function step1(Request $request)
{
    $user = Auth::user();
    $applicationId = $request->get('id');
    
    if ($applicationId) {
        $application = ApplicationDocument::where('user_id', $user->id)
            ->where('id', $applicationId)
            ->with('ownershipVerification')  // Load existing ownership data
            ->first();
            
        if (!$application) {
            return redirect()->route('applicant.applications')
                ->with('error', 'Application not found.');
        }
        
        return view('applicant.application.step1', compact('application'));
    } else {
        return redirect()->route('applicant.applications')
            ->with('error', 'Application ID is required.');
    }
}
   public function step2(Request $request)
{
    $user = Auth::user();
    $applicationId = $request->get('id');
    
    if (!$applicationId) {
        return redirect()->route('applicant.applications')
            ->with('error', 'Application ID is required.');
    }
    
    // Load application with ownership verification relationship
    $application = ApplicationDocument::where('user_id', $user->id)
        ->where('id', $applicationId)
        ->with('ownershipVerification')  // IMPORTANT: Load the relationship
        ->first();
        
    if (!$application) {
        return redirect()->route('applicant.applications')
            ->with('error', 'Application not found.');
    }
    
    // Check if ownership verification exists
    $ownership = $application->ownershipVerification;
    
    // If no ownership record exists, redirect to step 1
    if (!$ownership) {
        return redirect()->route('applicant.application.step1', ['id' => $applicationId])
            ->with('error', 'Please complete ownership verification first.');
    }
    
    // Check if required documents are submitted
    if (empty($ownership->tct_link) || empty($ownership->tax_declaration_link) || empty($ownership->current_tax_receipt_link)) {
        return redirect()->route('applicant.application.step1', ['id' => $applicationId])
            ->with('error', 'Please complete all required ownership documents.');
    }
    
    return view('applicant.application.step2', compact('application'));
}

public function step3(Request $request)
    {
        $user = Auth::user();
        $applicationId = $request->get('id');
        
        if (!$applicationId) {
            return redirect()->route('applicant.applications')
                ->with('error', 'Application ID is required.');
        }
        
        $application = ApplicationDocument::where('user_id', $user->id)
            ->where('id', $applicationId)
            ->first();
            
        if (!$application) {
            return redirect()->route('applicant.applications')
                ->with('error', 'Application not found.');
        }
        
        return view('applicant.application.step3', compact('application'));
    }

    /**
 * Submit client satisfaction survey
 */
public function submitSurvey(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:application_documents,id',
            'client_type' => 'required|in:citizen,business,government',
            'survey_date' => 'required|date',
            'sex' => 'required|in:male,female',
            'age' => 'required|integer|min:1|max:120',
            'cc1_awareness' => 'required|in:1,2,3,4',
            'cc2_helpfulness' => 'nullable|in:1,2,3,4,5',
            'cc3_help_level' => 'nullable|in:1,2,3,4',
            'sqd0_satisfied' => 'required|in:1,2,3,4,5',
            'sqd1_reasonable_time' => 'required|in:1,2,3,4,5',
            'sqd2_requirements_followed' => 'required|in:1,2,3,4,5',
            'sqd3_steps_easy' => 'required|in:1,2,3,4,5',
            'sqd4_info_easy_find' => 'required|in:1,2,3,4,5',
            'sqd5_reasonable_fees' => 'required|in:1,2,3,4,5',
            'sqd6_fair_treatment' => 'required|in:1,2,3,4,5',
            'sqd7_courteous_staff' => 'required|in:1,2,3,4,5',
            'sqd8_got_what_needed' => 'required|in:1,2,3,4,5',
            'suggestions' => 'nullable|string',
            'email' => 'nullable|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $application = ApplicationDocument::findOrFail($request->application_id);

        // Check if user owns this application
        if ($application->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if survey already exists for this application
        $existingSurvey = ClientSatisfactionSurvey::where('application_id', $request->application_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSurvey) {
            return response()->json([
                'success' => false,
                'message' => 'Survey already submitted for this application'
            ], 409);
        }

        // Create the survey - ADD service_availed field
        $survey = ClientSatisfactionSurvey::create([
            'application_id' => $request->application_id,
            'user_id' => $user->id,
            'service_availed' => 'Building Permit Application', // ADD THIS LINE - Provide a default service
            'client_type' => $request->client_type,
            'survey_date' => $request->survey_date,
            'sex' => $request->sex,
            'age' => $request->age,
            'cc1_awareness' => $request->cc1_awareness,
            'cc2_helpfulness' => $request->cc2_helpfulness,
            'cc3_help_level' => $request->cc3_help_level,
            'sqd0_satisfied' => $request->sqd0_satisfied,
            'sqd1_reasonable_time' => $request->sqd1_reasonable_time,
            'sqd2_requirements_followed' => $request->sqd2_requirements_followed,
            'sqd3_steps_easy' => $request->sqd3_steps_easy,
            'sqd4_info_easy_find' => $request->sqd4_info_easy_find,
            'sqd5_reasonable_fees' => $request->sqd5_reasonable_fees,
            'sqd6_fair_treatment' => $request->sqd6_fair_treatment,
            'sqd7_courteous_staff' => $request->sqd7_courteous_staff,
            'sqd8_got_what_needed' => $request->sqd8_got_what_needed,
            'suggestions' => $request->suggestions,
            'email' => $request->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback! Your survey has been submitted successfully.',
            'data' => $survey
        ]);

    } catch (\Exception $e) {
        Log::error('Error submitting survey: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to submit survey: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Check if there's a pending survey for an application
     */
    public function checkPendingSurvey(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $applicationId = $request->query('application_id');

            if (!$applicationId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application ID is required'
                ], 400);
            }

            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $existingSurvey = ClientSatisfactionSurvey::where('application_id', $applicationId)
                ->where('user_id', $user->id)
                ->exists();

            return response()->json([
                'success' => true,
                'has_pending_survey' => !$existingSurvey,
                'survey_submitted' => $existingSurvey
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking pending survey: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check survey status'
            ], 500);
        }
    }

    /**
     * Check if an application already has a number
     */
    public function checkApplicationHasNumber($id)
    {
        try {
            $user = Auth::user();
            
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
                'has_number' => !is_null($application->application_number),
                'application_number' => $application->application_number
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking application number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking application number'
            ], 500);
        }
    }

    /**
     * Calculate progress percentage based on status
     */
    private function calculateProgress($status)
    {
        $progressMap = [
            'draft' => 25,
            'pending' => 40,
            'under-review' => 55,
            'document-verification' => 70,
            'approved' => 85,
            'for-release' => 95,
            'verified' => 100,
            'rejected' => 100
        ];

        return $progressMap[$status] ?? 0;
    }

    /**
     * Get hard copy status display
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
            'document_reset' => 'Document Verification Reset',
            'batch_reset_all' => 'Batch Reset All Documents',
            'ownership_document_verified' => 'Ownership Document Verified',
            'ownership_document_unverified' => 'Ownership Document Unverified',
            'document_rejected' => 'Documents Rejected',
            'hard_copy_received' => 'Hard Copy Received',
            'application_created' => 'Application Created',
            'application_deleted' => 'Application Deleted',
            'application_submitted' => 'Application Submitted',
            default => ucfirst(str_replace('_', ' ', $action))
        };
    }
    /**
 * Get ownership document remarks for applicant
 */
public function getOwnershipRemarks($id)
{
    try {
        $application = ApplicationDocument::with('user')->find($id);
        
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }
        
        // Check if the authenticated user owns this application
        if ($application->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Get admin notes that contain ownership document remarks
        $adminNotes = $application->admin_notes;
        $remarks = [];
        
        if ($adminNotes) {
            // Parse admin notes to extract remarks
            $lines = explode("\n", $adminNotes);
            $currentRemark = null;
            
            foreach ($lines as $line) {
                // Pattern: [2024-01-01 10:00] John Doe (Ownership Document Remark - TCT / Deed of Sale): remark text
                if (preg_match('/\[(.*?)\]\s+(.*?)\s+\(Ownership Document Remark\s*-\s*(.*?)\):\s*(.*)/', $line, $matches)) {
                    if ($currentRemark) {
                        $remarks[] = $currentRemark;
                    }
                    $currentRemark = [
                        'created_at' => $matches[1],
                        'created_by' => $matches[2],
                        'document_name' => trim($matches[3]),
                        'remark' => trim($matches[4]),
                        'status' => 'pending_response',
                        'response' => null,
                        'responded_at' => null
                    ];
                } elseif ($currentRemark && preg_match('/Response:\s*(.*)/', $line, $responseMatch)) {
                    $currentRemark['response'] = trim($responseMatch[1]);
                    $currentRemark['status'] = 'resolved';
                    $currentRemark['responded_at'] = now()->toISOString();
                    $remarks[] = $currentRemark;
                    $currentRemark = null;
                }
            }
            
            if ($currentRemark) {
                $remarks[] = $currentRemark;
            }
        }
        
        // Group remarks by document key (approximate matching)
        $groupedRemarks = [];
        foreach ($remarks as $remark) {
            // Try to find matching document key based on document name
            $documentKey = $this->getDocumentKeyFromName($remark['document_name']);
            if (!isset($groupedRemarks[$documentKey])) {
                $groupedRemarks[$documentKey] = [];
            }
            $groupedRemarks[$documentKey][] = $remark;
        }
        
        return response()->json([
            'success' => true,
            'remarks' => $groupedRemarks
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error fetching ownership remarks: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error fetching remarks'
        ], 500);
    }
}

/**
 * Get document key from document name
 */
private function getDocumentKeyFromName($documentName)
{
    $mapping = [
        'TCT / Deed of Sale' => 'tct_link',
        'Tax Declaration' => 'tax_declaration_link',
        'Current Tax Receipt' => 'current_tax_receipt_link',
        'Special Power of Attorney (SPA)' => 'spa_link'
    ];
    
    foreach ($mapping as $name => $key) {
        if (strpos($documentName, $name) !== false) {
            return $key;
        }
    }
    
    // Try to extract from the string
    if (strpos($documentName, 'TCT') !== false) return 'tct_link';
    if (strpos($documentName, 'Tax Declaration') !== false) return 'tax_declaration_link';
    if (strpos($documentName, 'Tax Receipt') !== false) return 'current_tax_receipt_link';
    if (strpos($documentName, 'SPA') !== false || strpos($documentName, 'Power of Attorney') !== false) return 'spa_link';
    
    return 'unknown';
}
    /**
 * Save ownership verification documents (New Step 1)
 */
public function saveOwnership(Request $request)
{
    Log::info('saveOwnership called', $request->all());
    
    $validator = Validator::make($request->all(), [
        'application_id' => 'required|exists:application_documents,id',
        'is_owner' => 'required|in:0,1',
        'tct_link' => 'required|url',
        'tax_declaration_link' => 'required|url',
        'current_tax_receipt_link' => 'required|url',
        'spa_link' => 'nullable|url',
    ]);

    if ($validator->fails()) {
        Log::error('Validation failed', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed: ' . json_encode($validator->errors()->toArray()),
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $application = ApplicationDocument::findOrFail($request->application_id);
        
        // Check authorization
        if ($application->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Find or create ownership verification record
        $ownership = \App\Models\OwnershipVerification::firstOrNew([
            'application_id' => $application->id
        ]);
        
        $ownership->is_owner = $request->is_owner == '1';
        $ownership->tct_link = $request->tct_link;
        $ownership->tax_declaration_link = $request->tax_declaration_link;
        $ownership->current_tax_receipt_link = $request->current_tax_receipt_link;
        
        // Only set SPA if not owner
        if ($request->is_owner == '0') {
            $ownership->spa_link = $request->spa_link;
        } else {
            $ownership->spa_link = null;
        }
        
        // Set initial statuses (pending) only for new records
        if (!$ownership->exists) {
            $ownership->assessor_status = 'pending';
            $ownership->treasurer_status = 'pending';
        }
        
        $ownership->save();
        
        // Update application step completion - Use step1_completed
        $application->step1_completed = true;
        $application->step1_completed_at = now();
        $application->save();
        
        Log::info('Ownership verification saved successfully', [
            'application_id' => $application->id,
            'is_owner' => $ownership->is_owner,
            'step1_completed' => $application->step1_completed
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ownership documents saved successfully',
            'data' => $ownership
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error saving ownership verification: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to save ownership documents: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Get ownership data for an application
 */
public function getOwnershipData($id)
{
    try {
        $user = Auth::user();
        
        $application = ApplicationDocument::where('user_id', $user->id)
            ->where('id', $id)
            ->with('ownershipVerification')
            ->first();
        
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
 * Print CPDO Assessment (standalone printable page)
 */
public function printCPDOAssessment($id)
{
    try {
        $user = Auth::user();
        
        $application = ApplicationDocument::with('user')
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        
        if (!$application) {
            abort(404, 'Application not found');
        }
        
        // Get CPDO assessment data
        $assessmentData = [
            'assessment_date' => $application->cpdo_assessment_date,
            'zonal_location_fee' => $application->cpdo_zonal_location_fee,
            'palc_fee' => $application->cpdo_palc_fee,
            'development_permit_fee' => $application->cpdo_development_permit_fee,
            'alteration_permit_fee' => $application->cpdo_alteration_permit_fee,
            'site_zoning_certificate_fee' => $application->cpdo_site_zoning_certificate_fee,
            'total_cpdo_amount' => $application->cpdo_total_amount,
            'cpdo_assessment_notes' => $application->cpdo_assessment_notes,
            'cpdo_additional_fees' => json_decode($application->cpdo_additional_fees, true) ?? [],
            'cpdo_assessed_by' => $application->cpdo_assessed_by ? 
                (\App\Models\User::find($application->cpdo_assessed_by)?->first_name . ' ' . \App\Models\User::find($application->cpdo_assessed_by)?->last_name) : 
                'CPDO Staff'
        ];
        
        $applicantName = $application->user->first_name . ' ' . $application->user->last_name;
        
        $formatAmount = function($amount) {
            if (!$amount || $amount == 0) return '₱0.00';
            return '₱' . number_format($amount, 2);
        };
        
        return view('applicant.print-cpdo-receipt', [
            'application' => $application,
            'applicantName' => $applicantName,
            'assessmentData' => $assessmentData,
            'formatAmount' => $formatAmount
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error printing CPDO assessment: ' . $e->getMessage());
        abort(500, 'Unable to print assessment');
    }
}

    /**
     * Get all pending surveys for the user
     */
    public function getPendingSurveys()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Find applications that are completed (verified) but don't have a survey yet
            // Find applications that are for release but don't have a survey yet
$pendingSurveys = ApplicationDocument::where('user_id', $user->id)
    ->where('status', 'for-release')
    ->whereDoesntHave('clientSatisfactionSurveys')
                ->with(['user'])
                ->get()
                ->map(function ($application) {
                    return [
                        'id' => $application->id,
                        'application_number' => $application->application_number,
                        'service_availed' => 'Building Permit Application', // Default service
                        'completed_at' => $application->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'pending_surveys' => $pendingSurveys
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking pending surveys: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking for pending surveys'
            ], 500);
        }
    }

    /**
 * Submit CPDO Experience Rating
 */
public function submitCPDORating(Request $request)
{
    try {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:application_documents,id',
            'rating' => 'required|integer|min:1|max:5',
            'processing_time' => 'nullable|string',
            'responsiveness' => 'nullable|string',
            'clarity' => 'nullable|string',
            'fairness' => 'nullable|string',
            'overall_satisfaction' => 'nullable|string',
            'comments' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $application = ApplicationDocument::findOrFail($request->application_id);
        
        if ($application->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Check if rating already exists
        $existingRating = CPDORating::where('application_id', $request->application_id)
            ->where('user_id', $user->id)
            ->first();
            
        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => 'You have already rated this application'
            ], 409);
        }
        
        $rating = CPDORating::create([
            'application_id' => $request->application_id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'processing_time' => $request->processing_time,
            'responsiveness' => $request->responsiveness,
            'clarity' => $request->clarity,
            'fairness' => $request->fairness,
            'overall_satisfaction' => $request->overall_satisfaction,
            'comments' => $request->comments
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'data' => $rating
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error submitting CPDO rating: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to submit rating: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Check if user has already rated the CPDO for this application
 */
public function checkCPDORating($id)
{
    try {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'has_rated' => false
            ]);
        }
        
        $hasRated = CPDORating::where('application_id', $id)
            ->where('user_id', $user->id)
            ->exists();
            
        return response()->json([
            'success' => true,
            'has_rated' => $hasRated
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error checking CPDO rating: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'has_rated' => false
        ]);
    }
}
/**
 * Get certificates for applicant view
 */
public function getCertificates($id)
{
    try {
        $user = Auth::user();
        
        $application = ApplicationDocument::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }
        
        // Get payment proof record which contains the certificates
        $paymentProof = \App\Models\PaymentProof::where('application_id', $id)->first();
        
        if (!$paymentProof) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'zoning_cert_link' => $paymentProof->zoning_cert_link ?? null,
                'zoning_cert_uploaded_at' => $paymentProof->zoning_cert_uploaded_at ?? null,
                'locational_clearance_link' => $paymentProof->locational_clearance_link ?? null,
                'locational_clearance_uploaded_at' => $paymentProof->locational_clearance_uploaded_at ?? null,
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error getting certificates: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading certificates'
        ], 500);
    }
}
/**
 * Get payment orders for an application (for applicant view)
 */
public function getPaymentOrders($applicationId)
{
    try {
        $user = auth()->user();
        
        // Verify the application belongs to the user
        $application = ApplicationDocument::where('id', $applicationId)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }
        
        // Get payment orders from the payment_orders table
        $paymentOrders = \App\Models\PaymentOrder::where('application_id', $applicationId)
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $formattedOrders = $paymentOrders->map(function($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'payment_date' => $order->payment_date ? date('Y-m-d', strtotime($order->payment_date)) : null,
                'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
                'created_by' => $order->creator ? $order->creator->first_name . ' ' . $order->creator->last_name : 'System'
            ];
        });
        
        return response()->json([
            'success' => true,
            'payment_orders' => $formattedOrders,
            'has_order' => $paymentOrders->count() > 0,
            'latest_order' => $formattedOrders->first()
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error fetching payment orders for applicant: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error fetching payment orders'
        ], 500);
    }
}
 private function generateApplicationNumberInternal()
    {
        $year = date('Y');
        $lastApplication = ApplicationDocument::whereYear('created_at', $year)
            ->whereNotNull('application_number')
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = 1;
        if ($lastApplication && $lastApplication->application_number) {
            $lastSequence = (int) substr($lastApplication->application_number, -6);
            $sequence = $lastSequence + 1;
        }
        
        $sequenceFormatted = str_pad($sequence, 6, '0', STR_PAD_LEFT);
        $applicationNumber = $year . $sequenceFormatted;
        
        while (ApplicationDocument::where('application_number', $applicationNumber)->exists()) {
            $sequence++;
            $sequenceFormatted = str_pad($sequence, 6, '0', STR_PAD_LEFT);
            $applicationNumber = $year . $sequenceFormatted;
        }
        
        return $applicationNumber;
    }

}