<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\User;
use App\Models\ApplicationReviewActivity;
use App\Services\NotificationService;
use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ApplicationController extends Controller
{
    /**
     * The notification service instance.
     */
    protected $notificationService;

    /**
     * The Gmail service instance.
     */
    protected $gmailService;

    /**
     * Constructor - Inject NotificationService and GmailService
     */
    public function __construct(NotificationService $notificationService, GmailService $gmailService)
    {
        $this->notificationService = $notificationService;
        $this->gmailService = $gmailService;
    }

    /**
     * Display a listing of all submitted applications (excluding drafts)
     */
    public function index()
    {
        try {
            Log::info('Fetching all staff applications');
            
            // Get all applications EXCLUDING drafts
            $applications = ApplicationDocument::with(['user', 'lastUpdatedBy'])
                ->whereIn('status', [
                    'pending', 
                    'under-review', 
                    'document-verification',
                    'approved', 
                    'rejected', 
                    'for-release', 
                    'verified'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedApplications = [];
            foreach ($applications as $app) {
                // Safely get applicant name
                $applicantName = 'Unknown';
                if ($app->user) {
                    $firstName = $app->user->first_name ?? '';
                    $lastName = $app->user->last_name ?? '';
                    $applicantName = trim($firstName . ' ' . $lastName);
                    if (empty($applicantName)) {
                        $applicantName = 'Unknown';
                    }
                }
                
                // Safely format dates
                $createdAt = $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : null;
                $updatedAt = $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : null;
                
                // Safely get last updated by name
                $lastUpdatedByName = null;
                if ($app->lastUpdatedBy) {
                    $firstName = $app->lastUpdatedBy->first_name ?? '';
                    $lastName = $app->lastUpdatedBy->last_name ?? '';
                    $lastUpdatedByName = trim($firstName . ' ' . $lastName);
                    if (empty($lastUpdatedByName)) {
                        $lastUpdatedByName = null;
                    }
                }
                
                $formattedApplications[] = [
                    'id' => $app->id,
                    'application_number' => $app->application_number ?? 'N/A',
                    'applicant_name' => $applicantName,
                    'email' => $app->user ? ($app->user->email ?? null) : null,
                    'phone' => $app->user ? ($app->user->phone_number ?? null) : null,
                    'address' => $app->user ? ($app->user->address ?? null) : null,
                    'google_drive_link' => $app->google_drive_link,
                    'status' => $app->status ?? 'unknown',
                    'rejection_reason' => $app->rejection_reason,
                    'admin_notes' => $app->admin_notes,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'hard_copy_received' => $app->hard_copy_received ?? false,
                    'last_updated_by' => $app->last_updated_by,
                    'last_updated_by_name' => $lastUpdatedByName,
                    'last_updated_by_role' => $app->lastUpdatedBy ? ($app->lastUpdatedBy->role ?? null) : null
                ];
            }
            
            return response()->json([
                'success' => true,
                'applications' => $formattedApplications,
                'total' => count($formattedApplications)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading staff applications: ' . $e->getMessage());
            Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
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
                ->whereIn('status', [
                    'pending', 
                    'under-review', 
                    'document-verification',
                    'approved', 
                    'rejected', 
                    'for-release', 
                    'verified'
                ])
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

            // TRIGGER NOTIFICATION: Notify staff about new application
            $this->notificationService->notifyStaffNewApplication($application);
            
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
        Log::info('========== UPDATE STATUS START ==========');
        Log::info('updateStatus called', [
            'application_id' => $id,
            'status' => $request->status,
            'hardcopy_received' => $request->hardcopy_received,
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated',
            'user_role' => auth()->user() ? auth()->user()->role : 'unknown'
        ]);

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,under-review,document-verification,approved,rejected,for-release,verified',
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
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            Log::info('Application found', [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'current_status' => $application->status,
                'current_hardcopy_status' => $application->hard_copy_received,
                'applicant_id' => $application->user_id,
                'applicant_email' => $application->user ? $application->user->email : 'no user'
            ]);
            
            $staff = auth()->user();
            $oldStatus = $application->status;
            $newStatus = $request->status;
            $oldHardCopyStatus = $application->hard_copy_received;
            $newHardCopyStatus = $request->has('hardcopy_received') ? $request->hardcopy_received : $oldHardCopyStatus;
            
            Log::info('Status change', [
                'old' => $oldStatus,
                'new' => $newStatus,
                'changed' => ($oldStatus !== $newStatus) ? 'YES' : 'NO'
            ]);
            
            Log::info('Hard copy status change', [
                'old' => $oldHardCopyStatus,
                'new' => $newHardCopyStatus,
                'changed' => ($oldHardCopyStatus != $newHardCopyStatus) ? 'YES' : 'NO'
            ]);
            
            // Update application
            $application->status = $newStatus;
            $application->admin_notes = $request->remarks ?? $application->admin_notes;
            $application->last_updated_by = $staff->id;
            
            // Handle hard copy status
            if ($request->has('hardcopy_received')) {
                $application->hard_copy_received = $newHardCopyStatus;
                
                // If hard copy is being marked as received for the first time
                if ($newHardCopyStatus && !$oldHardCopyStatus) {
                    $application->hard_copy_received_at = now();
                    Log::info('Setting hard_copy_received_at to now');
                }
            }
            
            if ($newStatus === 'verified') {
                $application->verified_at = now();
                $application->verified_by = $staff->id;
                Log::info('Setting verified_at and verified_by');
            }
            
            if ($newStatus === 'rejected' && $request->has('remarks')) {
                $application->rejection_reason = $request->remarks;
                Log::info('Setting rejection_reason');
            }
            
            $application->save();
            
            Log::info('Application saved to database', [
                'new_status' => $application->status,
                'new_hardcopy_status' => $application->hard_copy_received
            ]);

            // Send status change notification if status changed
            if ($oldStatus !== $newStatus) {
                Log::info('ATTEMPTING TO SEND STATUS CHANGE NOTIFICATION');
                
                try {
                    $this->notificationService->notifyApplicantStatusChange(
                        $application,
                        $oldStatus,
                        $newStatus,
                        $staff
                    );
                    Log::info('✓✓✓ STATUS CHANGE NOTIFICATION SENT ✓✓✓');
                    
                    // SEND EMAIL NOTIFICATIONS FOR SPECIFIC STATUSES USING GMAIL SERVICE
                    if ($newStatus === 'approved') {
                        Log::info('📧 SENDING APPROVED EMAIL VIA GMAIL SERVICE');
                        
                        $emailSent = $this->gmailService->sendStatusEmail(
                            $application->user->email,
                            'approved',
                            $application->application_number,
                            $application->user->first_name,
                            $application->id
                        );
                        
                        if ($emailSent) {
                            Log::info('✓✓✓ APPROVED EMAIL SENT SUCCESSFULLY ✓✓✓');
                        } else {
                            Log::error('✗✗✗ FAILED TO SEND APPROVED EMAIL ✗✗✗');
                        }
                    }
                    
                    if ($newStatus === 'for-release') {
                        Log::info('📧 SENDING FOR-RELEASE EMAIL VIA GMAIL SERVICE');
                        
                        $emailSent = $this->gmailService->sendStatusEmail(
                            $application->user->email,
                            'for-release',
                            $application->application_number,
                            $application->user->first_name,
                            $application->id
                        );
                        
                        if ($emailSent) {
                            Log::info('✓✓✓ FOR-RELEASE EMAIL SENT SUCCESSFULLY ✓✓✓');
                        } else {
                            Log::error('✗✗✗ FAILED TO SEND FOR-RELEASE EMAIL ✗✗✗');
                        }
                    }
                    
                } catch (\Exception $e) {
                    Log::error('✗✗✗ FAILED TO SEND STATUS NOTIFICATION ✗✗✗');
                    Log::error('Error message: ' . $e->getMessage());
                }
            }

            // Send hard copy received notification if hard copy status changed to received
            if ($newHardCopyStatus && !$oldHardCopyStatus) {
                Log::info('ATTEMPTING TO SEND HARD COPY RECEIVED NOTIFICATION');
                
                try {
                    $this->notificationService->notifyHardCopyReceived($application, $staff);
                    Log::info('✓✓✓ HARD COPY NOTIFICATION SENT ✓✓✓');
                } catch (\Exception $e) {
                    Log::error('✗✗✗ FAILED TO SEND HARD COPY NOTIFICATION ✗✗✗');
                    Log::error('Error message: ' . $e->getMessage());
                }
            }

            // Log activity
            try {
                Log::info('Creating review activity');
                $activity = ApplicationReviewActivity::create([
                    'application_id' => $application->id,
                    'reviewer_id' => $staff->id,
                    'action' => 'status_updated',
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'remarks' => $request->remarks ?? "Status changed from {$oldStatus} to {$newStatus}",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                Log::info('Review activity created with ID: ' . $activity->id);
            } catch (\Exception $e) {
                Log::error('Failed to log activity: ' . $e->getMessage());
            }

            Log::info('========== UPDATE STATUS END (SUCCESS) ==========');
            
            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully',
                'data' => [
                    'id' => $application->id,
                    'status' => $application->status,
                    'hard_copy_received' => $application->hard_copy_received,
                    'hard_copy_received_at' => $application->hard_copy_received_at
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('========== UPDATE STATUS END (ERROR) ==========');
            Log::error('Error in updateStatus: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add note to application without changing status
     */
    public function addNote(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string'
        ]);

        if ($validator->fails()) {
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

            $staff = auth()->user();

            // Append new note to existing notes
            $existingNotes = $application->admin_notes;
            $newNote = "[" . now()->format('Y-m-d H:i') . "] " . $staff->first_name . " " . $staff->last_name . ": " . $request->note;
            $application->admin_notes = $existingNotes 
                ? $existingNotes . "\n\n" . $newNote 
                : $newNote;
            
            $application->last_updated_by = $staff->id;
            $application->save();

            // TRIGGER NOTIFICATION: Notify applicant about new note
            $this->notificationService->notifyApplicantOfNote(
                $application,
                $request->note,
                $staff
            );

            // Log the note activity
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                'note_added',
                null,
                null,
                $request->note,
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding note: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error adding note: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark hard copies as received
     */
    public function markHardCopyReceived(Request $request, $id)
    {
        Log::info('========== MARK HARD COPY RECEIVED CALLED ==========');
        Log::info('Parameters:', [
            'application_id' => $id,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown',
            'user_name' => auth()->user() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : 'unknown'
        ]);

        try {
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                Log::error('❌ Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            Log::info('✅ Application found', [
                'id' => $application->id,
                'number' => $application->application_number,
                'applicant_id' => $application->user_id,
                'applicant_email' => $application->user->email ?? 'unknown',
                'applicant_name' => $application->user ? $application->user->first_name . ' ' . $application->user->last_name : 'unknown',
                'current_hard_copy_status' => $application->hard_copy_received
            ]);

            $staff = auth()->user();

            // Update hard copy status
            $application->hard_copy_received = true;
            $application->hard_copy_received_at = now();
            $application->last_updated_by = $staff->id;
            $application->save();

            Log::info('✅ Application updated', [
                'hard_copy_received' => $application->hard_copy_received,
                'hard_copy_received_at' => $application->hard_copy_received_at,
                'last_updated_by' => $application->last_updated_by
            ]);

            // TRIGGER NOTIFICATION: Hard copy received
            Log::info('Calling notification service...');
            
            try {
                $this->notificationService->notifyHardCopyReceived($application, $staff);
                Log::info('✅ Notification service called successfully');
            } catch (\Exception $e) {
                Log::error('❌ Error calling notification service: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
            }

            // Log the activity
            try {
                $activity = $this->logReviewActivity(
                    $application->id,
                    $staff->id,
                    'hard_copy_received',
                    null,
                    null,
                    'Hard copies marked as received',
                    $request->ip(),
                    $request->userAgent()
                );
                
                if ($activity) {
                    Log::info('✅ Review activity logged with ID: ' . $activity->id);
                } else {
                    Log::warning('⚠️ Review activity not logged');
                }
            } catch (\Exception $e) {
                Log::error('❌ Error logging review activity: ' . $e->getMessage());
            }

            Log::info('========== MARK HARD COPY RECEIVED COMPLETED ==========');

            return response()->json([
                'success' => true,
                'message' => 'Hard copies marked as received successfully',
                'data' => [
                    'hard_copy_received' => true,
                    'hard_copy_received_at' => now()->toDateTimeString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('========== MARK HARD COPY RECEIVED ERROR ==========');
            Log::error('Error: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error marking hard copies: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request missing documents from applicant
     */
    public function requestMissingDocuments(Request $request, $id)
    {
        Log::info('========== REQUEST MISSING DOCUMENTS START ==========');
        Log::info('requestMissingDocuments called', [
            'application_id' => $id,
            'documents' => $request->documents,
            'remarks' => $request->remarks,
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated'
        ]);

        $validator = Validator::make($request->all(), [
            'documents' => 'required|array|min:1',
            'documents.*' => 'required|string',
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = ApplicationDocument::with('user')->find($id);
            
            if (!$application) {
                Log::error('Application not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $staff = auth()->user();
            
            // Format the missing documents list
            $documentList = implode("\n", array_map(function($doc) {
                return "• " . $doc;
            }, $request->documents));
            
            // Create the note message
            $noteMessage = "Missing documents requested:\n\n" . $documentList;
            
            if ($request->remarks) {
                $noteMessage .= "\n\nRemarks: " . $request->remarks;
            }
            
            // Add note to application
            $existingNotes = $application->admin_notes;
            $newNote = "[" . now()->format('Y-m-d H:i') . "] " . $staff->first_name . " " . $staff->last_name . " requested missing documents:\n" . $documentList;
            
            if ($request->remarks) {
                $newNote .= "\nRemarks: " . $request->remarks;
            }
            
            $application->admin_notes = $existingNotes 
                ? $existingNotes . "\n\n" . $newNote 
                : $newNote;
            
            $application->last_updated_by = $staff->id;
            $application->save();

            // SEND EMAIL NOTIFICATION
            Log::info('📧 SENDING MISSING DOCUMENTS EMAIL VIA GMAIL SERVICE');
            
            $emailSent = $this->gmailService->sendMissingDocumentsEmail(
                $application->user->email,
                $application->application_number,
                $application->user->first_name,
                $request->documents,
                $application->id,
                $request->remarks
            );
            
            if ($emailSent) {
                Log::info('✓✓✓ MISSING DOCUMENTS EMAIL SENT SUCCESSFULLY ✓✓✓');
            } else {
                Log::error('✗✗✗ FAILED TO SEND MISSING DOCUMENTS EMAIL ✗✗✗');
            }

            // Log the activity
            $this->logReviewActivity(
                $application->id,
                $staff->id,
                'missing_documents_requested',
                $application->status,
                $application->status,
                "Requested missing documents: " . implode(", ", $request->documents),
                $request->ip(),
                $request->userAgent()
            );

            Log::info('========== REQUEST MISSING DOCUMENTS END (SUCCESS) ==========');

            return response()->json([
                'success' => true,
                'message' => 'Missing documents request sent successfully',
                'data' => [
                    'document_count' => count($request->documents)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('========== REQUEST MISSING DOCUMENTS END (ERROR) ==========');
            Log::error('Error in requestMissingDocuments: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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
                ->whereIn('status', [
                    'pending', 
                    'under-review', 
                    'document-verification',
                    'approved', 
                    'rejected', 
                    'for-release', 
                    'verified'
                ])
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