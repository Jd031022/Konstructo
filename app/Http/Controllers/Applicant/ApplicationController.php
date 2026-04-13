<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ApplicationReviewActivity;
use App\Models\BasicRequirement;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use setasign\Fpdi\Tcpdf\Fpdi;

class ApplicationController extends Controller
{
    protected $notificationService;

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

            $applications = ApplicationDocument::with('basicRequirement')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $formattedApplications = [];
            foreach ($applications as $app) {
                try {
                    $basicRequirementStatus = $app->basicRequirement ? $app->basicRequirement->status : 'not_submitted';
                    $basicRequirementRejectionReason = $app->basicRequirement ? $app->basicRequirement->rejection_reason : null;
                    
                    // Get project title - check direct column first, then data JSON
                    $projectTitle = $app->project_title ?? null;
                    if (!$projectTitle && $app->data && is_array($app->data)) {
                        $projectTitle = $app->data['project_title'] ?? null;
                    }
                    
                    $formattedApplications[] = [
    'id' => $app->id,
    'application_number' => $app->application_number ?? 'Pending',
    'has_application_number' => !is_null($app->application_number),
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
    'basic_requirements_status' => $basicRequirementStatus,
    'basic_requirements_rejection_reason' => $basicRequirementRejectionReason,
    // Professional fields (optional for index, but good to have)
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
        
        $application = ApplicationDocument::with('basicRequirement')
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $basicRequirement = $application->basicRequirement;
        $basicRequirementsStatus = $basicRequirement ? $basicRequirement->status : 'not_submitted';

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
                'document_links' => $application->document_links,
                'status' => $application->status,
                'status_display' => $this->formatStatus($application->status),
                'rejection_reason' => $application->rejection_reason,
                'admin_notes' => $application->admin_notes,
                'created_at' => $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $application->updated_at ? $application->updated_at->format('Y-m-d H:i:s') : null,
                'submitted_at' => $application->submitted_at ? $application->submitted_at->format('Y-m-d H:i:s') : null,
                'hard_copy_received' => $application->hard_copy_received ?? false,
                'hard_copy_status' => $this->getHardCopyStatus($application),
                'progress' => $this->calculateProgress($application->status),
                'last_updated_by' => $application->last_updated_by,
                'last_updated_by_name' => $lastUpdatedBy ? $lastUpdatedBy->first_name . ' ' . $lastUpdatedBy->last_name : null,
                'basic_requirements_status' => $basicRequirementsStatus,
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
                // Professional Information - ALL 4 professionals
                'architect_name' => $application->architect_name ?? null,
                'architect_license' => $application->architect_license ?? null,
                'engineer_name' => $application->engineer_name ?? null,
                'engineer_license' => $application->engineer_license ?? null,
                'electrical_engineer_name' => $application->electrical_engineer_name ?? null,  // ADD THIS
                'electrical_engineer_license' => $application->electrical_engineer_license ?? null,  // ADD THIS
                'sanitary_engineer_name' => $application->sanitary_engineer_name ?? null,  // ADD THIS
                'sanitary_engineer_license' => $application->sanitary_engineer_license ?? null  // ADD THIS
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

            $basicRequirement = BasicRequirement::where('application_id', $application->id)->first();
            if ($basicRequirement) {
                $basicRequirement->delete();
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
                        'action_display' => $this->getActionDisplay($activity->action),
                        'old_status' => $activity->old_status,
                        'new_status' => $activity->new_status,
                        'remarks' => $activity->remarks,
                        'created_at' => $activity->created_at ? $activity->created_at->format('Y-m-d H:i:s') : null,
                        'time_ago' => $activity->created_at ? $activity->created_at->diffForHumans() : null,
                        'reviewer' => $reviewerInfo
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
            
            // Check application limit
            $existingSubmitted = ApplicationDocument::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'under-review', 'document-verification', 'approved', 'for-release', 'verified'])
                ->count();
                
            if ($existingSubmitted >= 3) {
                return response()->json([
                    'success' => false,
                    'limit_reached' => true,
                    'message' => 'You have reached the maximum limit of 3 submitted applications.'
                ], 403);
            }
            
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

    /**
     * Get application limit info
     */
    public function getLimitInfo(Request $request)
    {
        try {
            $user = Auth::user();
            
            $drafts = ApplicationDocument::where('user_id', $user->id)
                ->where('status', 'draft')
                ->count();
            
            $submitted = ApplicationDocument::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'under-review', 'document-verification', 'approved', 'for-release', 'verified'])
                ->count();
            
            $limit = 3;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'drafts' => $drafts,
                    'submitted' => $submitted,
                    'limit' => $limit,
                    'remaining' => max(0, $limit - $submitted),
                    'can_apply' => $submitted < $limit
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

            $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
            $pdf->SetCreator('Konstructo');
            $pdf->SetAuthor('Konstructo BPO');
            $pdf->SetTitle('Application Letter - ' . $applicationNumber);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);

            $pageCount  = $pdf->setSourceFile($templatePath);
            $templateId = $pdf->importPage(1);
            $size       = $pdf->getTemplateSize($templateId);

            $pdfW = $size['width'];   // in mm (TCPDF always works in mm)
            $pdfH = $size['height'];  // in mm

            $pdf->AddPage('P', [$pdfW, $pdfH]);
            $pdf->useTemplate($templateId, 0, 0, $pdfW, $pdfH);

            // Scale: 1 overlay-pixel = how many mm in the PDF
            $scaleX = $pdfW / $iframeWidth;
            $scaleY = $pdfH / $iframeHeight;

            foreach ($textElements as $text) {
                if (empty(trim($text['content'] ?? ''))) continue;

                // Screen px → PDF pt (screen is 96dpi, PDF is 72dpi)
                $fontSizePx = (int)($text['fontSize'] ?? 12);
                $fontSizePt = $fontSizePx * 0.75;   // px → pt

                $pdf->SetFont('helvetica', '', $fontSizePt);

                // Colour
                $color = $text['color'] ?? '#000000';
                // Accept named colours
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
     * Show step 1 - only if basic requirements are approved
     */
    public function step1(Request $request)
    {
        $user = Auth::user();
        $applicationId = $request->get('id');
        
        if ($applicationId) {
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->first();
                
            if (!$application) {
                return redirect()->route('applicant.basic-requirements.index')
                    ->with('error', 'Application not found.');
            }
            
            $basicRequirement = BasicRequirement::where('application_id', $applicationId)
                ->where('status', 'approved')
                ->first();
                
            if (!$basicRequirement) {
                return redirect()->route('applicant.basic-requirements.index', ['application_id' => $applicationId])
                    ->with('error', 'Please complete and get approval for your basic requirements first.');
            }
            
            return view('applicant.application.step1', compact('application'));
            
        } else {
            return redirect()->route('applicant.basic-requirements.index')
                ->with('error', 'Please complete your basic requirements first.');
        }
    }

    /**
     * Show step 2
     */
    public function step2(Request $request)
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
        
        $basicRequirement = BasicRequirement::where('application_id', $applicationId)
            ->where('status', 'approved')
            ->first();
            
        if (!$basicRequirement) {
            return redirect()->route('applicant.basic-requirements.index', ['application_id' => $applicationId])
                ->with('error', 'Please submit and get approval for basic requirements before proceeding.');
        }
        
        return view('applicant.application.step2', compact('application'));
    }

    /**
     * Show step 3
     */
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
        
        $basicRequirement = BasicRequirement::where('application_id', $applicationId)
            ->where('status', 'approved')
            ->first();
            
        if (!$basicRequirement) {
            return redirect()->route('applicant.basic-requirements.index', ['application_id' => $applicationId])
                ->with('error', 'Please submit and get approval for basic requirements before proceeding.');
        }
        
        return view('applicant.application.step3', compact('application'));
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
            'hard_copy_received' => 'Hard Copy Received',
            'application_created' => 'Application Created',
            'application_deleted' => 'Application Deleted',
            'application_submitted' => 'Application Submitted',
            default => ucfirst(str_replace('_', ' ', $action))
        };
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
        
        // Log before update
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
        
        // Log after update to verify
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
}