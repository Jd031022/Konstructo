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
                        'hard_copy_received' => $app->hard_copy_received ?? false,
                        'hard_copy_received_at' => $app->hard_copy_received_at ? $app->hard_copy_received_at->format('Y-m-d H:i:s') : null,
                        'last_updated_by' => $app->last_updated_by,
                        'project_name' => 'Building Permit Application',
                        'progress' => $this->calculateProgress($app->status),
                        'basic_requirements_status' => $basicRequirementStatus,
                        'basic_requirements_rejection_reason' => $basicRequirementRejectionReason
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
                    'hard_copy_received' => $application->hard_copy_received ?? false,
                    'hard_copy_status' => $this->getHardCopyStatus($application),
                    'progress' => $this->calculateProgress($application->status),
                    'last_updated_by' => $application->last_updated_by,
                    'last_updated_by_name' => $lastUpdatedBy ? $lastUpdatedBy->first_name . ' ' . $lastUpdatedBy->last_name : null,
                    'basic_requirements_status' => $basicRequirementsStatus
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
     * Generate application number
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
            
            // Generate application number (format: YYYY-XXXXX)
            $year = date('Y');
            $lastNumber = ApplicationDocument::whereYear('created_at', $year)
                ->whereNotNull('application_number')
                ->count();
            
            $sequentialNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            $applicationNumber = $year . '-' . $sequentialNumber;
            
            $application->application_number = $applicationNumber;
            $application->save();
            
            session(['current_application_number' => $applicationNumber]);
            session(['current_application_id' => $application->id]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'application_number' => $applicationNumber,
                    'application_id' => $application->id
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
public function saveEditedPdf(Request $request)
{
    try {
        $textElements     = $request->input('text_elements', []);
        $applicationNumber= $request->input('application_number', '');
        $iframeWidth      = (float)$request->input('iframe_width',  800);
        $iframeHeight     = (float)$request->input('iframe_height', 1100);

        $templatePath = public_path('downloads/application-letter.pdf');
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

            /*
             * Cell height in mm = font size in pt × 0.3528 (1pt = 0.3528mm).
             * This makes the cell exactly as tall as the text, so SetXY($x,$y)
             * + Cell(...) renders text whose TOP aligns with $pdfY —
             * matching where the browser div's top-left sits on the overlay.
             */
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
}