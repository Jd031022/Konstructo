<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BasicRequirement;
use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class BasicRequirementController extends Controller
{
    protected $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    /**
     * Display list of basic requirements
     */
    public function index(Request $request)
    {
        try {
            $query = BasicRequirement::with(['user', 'application'])
                ->orderBy('submitted_at', 'desc');
            
            // Apply status filter
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            } else {
                $query->where('status', 'pending');
            }
            
            // Apply search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            $requirements = $query->paginate(15);
                
            return view('staff.basic-requirements.index', compact('requirements'));
            
        } catch (\Exception $e) {
            Log::error('Error loading basic requirements: ' . $e->getMessage());
            return back()->with('error', 'Failed to load requirements');
        }
    }

    /**
     * Display the specified basic requirement details
     */
    public function show($id)
    {
        try {
            $requirement = BasicRequirement::with(['user', 'application', 'reviewer'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $requirement->id,
                    'application_id' => $requirement->application_id,
                    'application_number' => $requirement->application ? $requirement->application->application_number : null,
                    'user' => [
                        'id' => $requirement->user->id,
                        'first_name' => $requirement->user->first_name,
                        'last_name' => $requirement->user->last_name,
                        'email' => $requirement->user->email,
                        'phone_number' => $requirement->user->phone_number,
                    ],
                    'submitted_at' => $requirement->submitted_at,
                    'tct_link' => $requirement->tct_link,
                    'tax_declaration_link' => $requirement->tax_declaration_link,
                    'current_tax_receipt_link' => $requirement->current_tax_receipt_link,
                    'spa_link' => $requirement->spa_link,
                    'status' => $requirement->status,
                    'rejection_reason' => $requirement->rejection_reason,
                    'admin_notes' => $requirement->admin_notes,
                    'reviewed_at' => $requirement->reviewed_at,
                    'reviewed_by' => $requirement->reviewed_by,
                    'approved_at' => $requirement->approved_at,
                    'approved_by' => $requirement->approved_by,
                    'tct_checked' => $requirement->tct_checked ?? false,
                    'tax_declaration_checked' => $requirement->tax_declaration_checked ?? false,
                    'tax_receipt_checked' => $requirement->tax_receipt_checked ?? false,
                    'auto_approved_at' => $requirement->auto_approved_at,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching basic requirement details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load requirement details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update document check status
     */
    public function updateCheck(Request $request, $id)
    {
        try {
            Log::info('Update check request received', [
                'id' => $id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user() ? Auth::user()->email : 'unknown',
                'request_data' => $request->all()
            ]);
            
            $validator = Validator::make($request->all(), [
                'document_type' => 'required|string|in:tct,tax_declaration,tax_receipt',
                'checked' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $requirement = BasicRequirement::findOrFail($id);
            
            // Check if already approved
            if ($requirement->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Requirements already approved. Cannot modify check status.'
                ], 400);
            }
            
            // Check if already rejected
            if ($requirement->status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Requirements already rejected. Cannot modify check status.'
                ], 400);
            }
            
            $user = Auth::user();
            
            // Get user position from user_profile
            $userProfile = DB::table('user_profiles')->where('user_id', $user->id)->first();
            $userPosition = $userProfile ? $userProfile->position : null;
            
            // If no position found, try to get from users table
            if (!$userPosition && Schema::hasColumn('users', 'position')) {
                $userPosition = DB::table('users')->where('id', $user->id)->value('position');
            }
            
            Log::info('User position retrieved', [
                'user_id' => $user->id,
                'position' => $userPosition,
                'has_profile' => $userProfile ? true : false
            ]);
            
            // Update based on document type with role validation
            switch ($request->document_type) {
                case 'tct':
                    // Only Assessor can check TCT
                    if ($userPosition !== 'assessor') {
                        Log::warning('Unauthorized TCT check attempt', [
                            'user_id' => $user->id,
                            'user_position' => $userPosition,
                            'required_position' => 'assessor'
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Only Assessor can verify TCT / Deed of Sale documents. Your position: ' . ($userPosition ?: 'Not set')
                        ], 403);
                    }
                    $requirement->tct_checked = $request->checked;
                    break;
                    
                case 'tax_declaration':
                    // Only Treasurer can check Tax Declaration
                    if ($userPosition !== 'treasurer') {
                        Log::warning('Unauthorized Tax Declaration check attempt', [
                            'user_id' => $user->id,
                            'user_position' => $userPosition,
                            'required_position' => 'treasurer'
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Only Treasurer can verify Tax Declaration documents. Your position: ' . ($userPosition ?: 'Not set')
                        ], 403);
                    }
                    $requirement->tax_declaration_checked = $request->checked;
                    break;
                    
                case 'tax_receipt':
                    // Only Treasurer can check Current Tax Receipt
                    if ($userPosition !== 'treasurer') {
                        Log::warning('Unauthorized Tax Receipt check attempt', [
                            'user_id' => $user->id,
                            'user_position' => $userPosition,
                            'required_position' => 'treasurer'
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Only Treasurer can verify Current Tax Receipt documents. Your position: ' . ($userPosition ?: 'Not set')
                        ], 403);
                    }
                    $requirement->tax_receipt_checked = $request->checked;
                    break;
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid document type.'
                    ], 400);
            }
            
            $requirement->save();
            
            // Check if all documents are verified
            $allVerified = $requirement->tct_checked && 
                           $requirement->tax_declaration_checked && 
                           $requirement->tax_receipt_checked;
            
            $message = 'Document verification status updated.';
            $autoApproved = false;
            
            // If all documents are verified and status is pending, trigger auto-approval
            if ($allVerified && $requirement->status === 'pending') {
                $autoApproved = true;
                $message = 'All documents verified. Auto-approving requirements...';
                
                // Auto-approve the requirements
                $requirement->status = 'approved';
                $requirement->approved_at = now();
                $requirement->approved_by = $user->id;
                $requirement->reviewed_at = now();
                $requirement->reviewed_by = $user->id;
                $requirement->auto_approved_at = now();
                $requirement->rejection_reason = null;
                $requirement->save();
                
                // Update the associated application
                if ($requirement->application_id) {
                    try {
                        $application = $requirement->application;
                        if ($application) {
                            $application->basic_requirements_approved_at = now();
                            $application->basic_requirements_approved_by = $user->id;
                            $application->last_updated_by = $user->id;
                            $application->save();
                            Log::info('Application updated for auto-approval', [
                                'application_id' => $application->id
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error updating application: ' . $e->getMessage());
                    }
                }
                
                // Send email notification for auto-approval
                try {
                    if ($requirement->user && $requirement->user->email) {
                        $this->gmailService->sendBasicRequirementsApprovedEmail(
                            $requirement->user->email,
                            $requirement->user->first_name,
                            $requirement->id,
                            $user->first_name . ' ' . $user->last_name,
                            $requirement->application ? $requirement->application->application_number : null
                        );
                        Log::info('Auto-approval email sent to: ' . $requirement->user->email);
                    }
                } catch (\Exception $e) {
                    Log::error('Error sending auto-approval email: ' . $e->getMessage());
                }
            }
            
            Log::info('Document check updated successfully', [
                'requirement_id' => $requirement->id,
                'document_type' => $request->document_type,
                'checked' => $request->checked,
                'all_verified' => $allVerified,
                'auto_approved' => $autoApproved
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'all_verified' => $allVerified,
                'auto_approved' => $autoApproved,
                'status' => $requirement->status,
                'data' => [
                    'tct_checked' => $requirement->tct_checked,
                    'tax_declaration_checked' => $requirement->tax_declaration_checked,
                    'tax_receipt_checked' => $requirement->tax_receipt_checked
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating document check status: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update check status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve basic requirements
     */
    public function approve(Request $request, $id)
    {
        try {
            Log::info('Approving basic requirement', [
                'id' => $id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user() ? Auth::user()->email : 'unknown'
            ]);
            
            $validator = Validator::make($request->all(), [
                'notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            
            $requirement = BasicRequirement::with(['user', 'application'])
                ->findOrFail($id);
            
            if (!$requirement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requirement not found'
                ], 404);
            }
            
            Log::info('Requirement found', [
                'requirement_id' => $requirement->id,
                'current_status' => $requirement->status,
                'application_id' => $requirement->application_id,
                'applicant_email' => $requirement->user->email
            ]);
            
            if ($requirement->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This requirement has already been processed.'
                ], 400);
            }

            // Check if auto_approve flag is set (from auto-approval)
            $isAutoApprove = $request->auto_approve ?? false;
            
            // Mark as approved
            $updateData = [
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'admin_notes' => $request->notes,
                'rejection_reason' => null
            ];
            
            if ($isAutoApprove) {
                $updateData['auto_approved_at'] = now();
            }
            
            $requirement->update($updateData);
            
            // Update the associated application if it exists
            if ($requirement->application_id) {
                $application = $requirement->application;
                if ($application) {
                    $application->update([
                        'basic_requirements_approved_at' => now(),
                        'basic_requirements_approved_by' => Auth::id(),
                        'last_updated_by' => Auth::id()
                    ]);
                    Log::info('Updated application with approval', [
                        'application_id' => $application->id,
                        'application_number' => $application->application_number
                    ]);
                }
            }

            DB::commit();

            Log::info('Basic requirements approved successfully', [
                'requirement_id' => $requirement->id,
                'application_id' => $requirement->application_id,
                'user_id' => $requirement->user_id,
                'approved_by' => Auth::id(),
                'auto_approved' => $isAutoApprove
            ]);

            // SEND EMAIL NOTIFICATION TO APPLICANT (skip if auto-approve email already sent)
            if (!$isAutoApprove) {
                try {
                    Log::info('Attempting to send approval email to: ' . $requirement->user->email);
                    
                    $emailSent = $this->gmailService->sendBasicRequirementsApprovedEmail(
                        $requirement->user->email,
                        $requirement->user->first_name,
                        $requirement->id,
                        Auth::user()->first_name . ' ' . Auth::user()->last_name,
                        $requirement->application ? $requirement->application->application_number : null
                    );
                    
                    if ($emailSent) {
                        Log::info('✅ Basic requirements approval email sent successfully to ' . $requirement->user->email);
                    } else {
                        Log::error('❌ Failed to send basic requirements approval email to ' . $requirement->user->email);
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Error sending approval email: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Basic requirements approved successfully. Applicant has been notified via email.',
                'data' => [
                    'requirement_id' => $requirement->id,
                    'application_id' => $requirement->application_id,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving basic requirements: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error approving requirements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject basic requirements
     */
    public function reject(Request $request, $id)
    {
        try {
            Log::info('Rejecting basic requirement', [
                'id' => $id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user() ? Auth::user()->email : 'unknown'
            ]);
            
            $validator = Validator::make($request->all(), [
                'rejection_reason' => 'required|string|min:10',
                'notes' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }
            
            DB::beginTransaction();
            
            $requirement = BasicRequirement::with(['user', 'application'])
                ->findOrFail($id);
            
            if (!$requirement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requirement not found'
                ], 404);
            }
            
            if ($requirement->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This requirement has already been processed'
                ], 400);
            }
            
            // Mark as rejected and reset verification status
            $requirement->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'admin_notes' => $request->notes,
                'approved_at' => null,
                'approved_by' => null,
                'auto_approved_at' => null,
                'tct_checked' => false,
                'tax_declaration_checked' => false,
                'tax_receipt_checked' => false
            ]);
            
            // Update the associated application if it exists
            if ($requirement->application_id) {
                $application = $requirement->application;
                if ($application) {
                    $application->update([
                        'basic_requirements_approved_at' => null,
                        'basic_requirements_approved_by' => null,
                        'rejection_reason' => $request->rejection_reason,
                        'last_updated_by' => Auth::id()
                    ]);
                    Log::info('Updated application with rejection', [
                        'application_id' => $application->id,
                        'reason' => $request->rejection_reason
                    ]);
                }
            }
            
            DB::commit();
            
            Log::info('Basic requirements rejected', [
                'requirement_id' => $requirement->id,
                'application_id' => $requirement->application_id,
                'user_id' => $requirement->user_id,
                'rejected_by' => Auth::id(),
                'reason' => $request->rejection_reason
            ]);
            
            // SEND EMAIL NOTIFICATION TO APPLICANT
            try {
                Log::info('Attempting to send rejection email to: ' . $requirement->user->email);
                
                $emailSent = $this->gmailService->sendBasicRequirementsRejectedEmail(
                    $requirement->user->email,
                    $requirement->user->first_name,
                    $requirement->id,
                    $request->rejection_reason,
                    $requirement->application ? $requirement->application->application_number : null
                );
                
                if ($emailSent) {
                    Log::info('✅ Basic requirements rejection email sent successfully to ' . $requirement->user->email);
                } else {
                    Log::error('❌ Failed to send basic requirements rejection email to ' . $requirement->user->email);
                }
            } catch (\Exception $e) {
                Log::error('❌ Error sending rejection email: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Requirements rejected successfully. Applicant has been notified via email.',
                'data' => [
                    'requirement_id' => $requirement->id,
                    'application_id' => $requirement->application_id,
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting requirements: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject requirements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        try {
            $stats = [
                'pending' => BasicRequirement::where('status', 'pending')->count(),
                'approved_today' => BasicRequirement::whereDate('approved_at', today())->count(),
                'rejected_today' => BasicRequirement::whereDate('reviewed_at', today())
                    ->where('status', 'rejected')
                    ->count(),
                'total_submitted' => BasicRequirement::whereNotNull('submitted_at')->count(),
                'approved_this_week' => BasicRequirement::whereBetween('approved_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'pending_over_3_days' => BasicRequirement::where('status', 'pending')
                    ->where('submitted_at', '<', now()->subDays(3))
                    ->count(),
                'tct_verified' => BasicRequirement::where('tct_checked', true)->count(),
                'tax_declaration_verified' => BasicRequirement::where('tax_declaration_checked', true)->count(),
                'tax_receipt_verified' => BasicRequirement::where('tax_receipt_checked', true)->count(),
                'auto_approved' => BasicRequirement::whereNotNull('auto_approved_at')->count(),
            ];
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export basic requirements to CSV
     */
    public function export(Request $request)
    {
        try {
            Log::info('Exporting basic requirements', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user() ? Auth::user()->email : 'unknown',
                'filters' => $request->all()
            ]);

            $query = BasicRequirement::with(['user', 'application'])
                ->orderBy('submitted_at', 'desc');

            // Apply status filter
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Apply search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $requirements = $query->get();

            $filename = 'basic-requirements-' . now()->format('Y-m-d-H-i-s') . '.csv';

            return response()->streamDownload(function () use ($requirements) {
                $handle = fopen('php://output', 'w');

                // Write CSV headers
                fputcsv($handle, [
                    'ID',
                    'Submitted Date',
                    'Applicant Name',
                    'Email',
                    'Application Number',
                    'Status',
                    'TCT Verified',
                    'Tax Declaration Verified',
                    'Tax Receipt Verified',
                    'Auto Approved',
                    'Reviewed Date',
                    'Reviewed By',
                    'Rejection Reason',
                    'Admin Notes',
                    'TCT/Deed of Sale Link',
                    'Tax Declaration Link',
                    'Current Tax Receipt Link',
                    'SPA Link'
                ]);

                // Write data rows
                foreach ($requirements as $req) {
                    fputcsv($handle, [
                        $req->id,
                        $req->submitted_at ? $req->submitted_at->format('Y-m-d H:i:s') : '',
                        $req->user->first_name . ' ' . $req->user->last_name,
                        $req->user->email,
                        $req->application ? $req->application->application_number : 'N/A',
                        ucfirst($req->status),
                        $req->tct_checked ? 'Yes' : 'No',
                        $req->tax_declaration_checked ? 'Yes' : 'No',
                        $req->tax_receipt_checked ? 'Yes' : 'No',
                        $req->auto_approved_at ? 'Yes' : 'No',
                        $req->reviewed_at ? $req->reviewed_at->format('Y-m-d H:i:s') : '',
                        $req->reviewed_by ? ($req->reviewer ? $req->reviewer->first_name . ' ' . $req->reviewer->last_name : 'N/A') : '',
                        $req->rejection_reason ?: '',
                        $req->admin_notes ?: '',
                        $req->tct_link ?: '',
                        $req->tax_declaration_link ?: '',
                        $req->current_tax_receipt_link ?: '',
                        $req->spa_link ?: ''
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Error exporting basic requirements: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to export basic requirements: ' . $e->getMessage()
            ], 500);
        }
    }
}