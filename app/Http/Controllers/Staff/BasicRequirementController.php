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
            $requirement = BasicRequirement::with(['user', 'application'])
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
                    'is_owner' => $requirement->is_owner,
                    'submitted_at' => $requirement->submitted_at,
                    'tct_link' => $requirement->tct_link,
                    'tax_declaration_link' => $requirement->tax_declaration_link,
                    'current_tax_receipt_link' => $requirement->current_tax_receipt_link,
                    'deed_of_sale_link' => $requirement->deed_of_sale_link,
                    'spa_link' => $requirement->spa_link,
                    'status' => $requirement->status,
                    'rejection_reason' => $requirement->rejection_reason,
                    'admin_notes' => $requirement->admin_notes,
                    'reviewed_at' => $requirement->reviewed_at,
                    'reviewed_by' => $requirement->reviewed_by,
                    'approved_at' => $requirement->approved_at,
                    'approved_by' => $requirement->approved_by,
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

            // Mark as approved
            $requirement->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'admin_notes' => $request->notes,
                'rejection_reason' => null
            ]);
            
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
                'approved_by' => Auth::id()
            ]);

            // SEND EMAIL NOTIFICATION TO APPLICANT
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
            
            // Mark as rejected
            $requirement->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'admin_notes' => $request->notes,
                'approved_at' => null,
                'approved_by' => null
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
}