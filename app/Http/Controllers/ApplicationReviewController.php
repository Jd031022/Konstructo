<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\User;
use App\Services\NotificationService; // ADD THIS IMPORT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApplicationReviewController extends Controller
{
    /**
     * The notification service instance.
     */
    protected $notificationService;

    /**
     * Constructor - Inject NotificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $applications = ApplicationDocument::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.applications.index', compact('applications'));
    }

    public function show($id)
    {
        $application = ApplicationDocument::with(['user', 'verifier'])->findOrFail($id);
        return view('admin.applications.show', compact('application'));
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $application = ApplicationDocument::findOrFail($id);
            $oldStatus = $application->status;
            
            // Mark as verified
            $application->markAsVerified(Auth::id(), $request->notes);

            // TRIGGER NOTIFICATION: Notify applicant about verification
            $this->notificationService->notifyApplicantStatusChange(
                $application,
                $oldStatus,
                'verified',
                Auth::user()
            );

            // Log the activity
            $this->logReviewActivity(
                $application,
                Auth::user(),
                'document_verified',
                $oldStatus,
                'verified',
                $request->notes ?? 'Documents verified by admin'
            );

            Log::info('Application verified', [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'verified_by' => Auth::id()
            ]);

            return redirect()->back()->with('success', 'Application documents verified successfully.');

        } catch (\Exception $e) {
            Log::error('Error verifying application: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Error verifying application: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $application = ApplicationDocument::findOrFail($id);
            $oldStatus = $application->status;
            
            // Mark as rejected
            $application->markAsRejected(
                $request->rejection_reason,
                Auth::id(),
                $request->notes
            );

            // TRIGGER NOTIFICATION: Notify applicant about rejection
            $this->notificationService->notifyApplicantStatusChange(
                $application,
                $oldStatus,
                'rejected',
                Auth::user()
            );

            // Log the activity
            $this->logReviewActivity(
                $application,
                Auth::user(),
                'document_rejected',
                $oldStatus,
                'rejected',
                $request->rejection_reason
            );

            Log::info('Application rejected', [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'rejected_by' => Auth::id(),
                'reason' => $request->rejection_reason
            ]);

            return redirect()->back()->with('success', 'Application documents rejected.');

        } catch (\Exception $e) {
            Log::error('Error rejecting application: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Error rejecting application: ' . $e->getMessage());
        }
    }

    /**
     * Additional method: Mark as under review
     */
    public function markUnderReview(Request $request, $id)
    {
        try {
            $application = ApplicationDocument::findOrFail($id);
            $oldStatus = $application->status;
            
            $application->status = 'under-review';
            $application->last_updated_by = Auth::id();
            $application->save();

            // TRIGGER NOTIFICATION: Notify applicant about under review status
            $this->notificationService->notifyApplicantStatusChange(
                $application,
                $oldStatus,
                'under-review',
                Auth::user()
            );

            // Log the activity
            $this->logReviewActivity(
                $application,
                Auth::user(),
                'status_updated',
                $oldStatus,
                'under-review',
                'Application marked as under review'
            );

            return redirect()->back()->with('success', 'Application marked as under review.');

        } catch (\Exception $e) {
            Log::error('Error marking application as under review: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Error updating application status.');
        }
    }

    /**
     * Additional method: Mark as for release
     */
    public function markForRelease(Request $request, $id)
    {
        try {
            $application = ApplicationDocument::findOrFail($id);
            $oldStatus = $application->status;
            
            $application->status = 'for-release';
            $application->last_updated_by = Auth::id();
            $application->save();

            // TRIGGER NOTIFICATION: Notify applicant about for release status
            $this->notificationService->notifyApplicantStatusChange(
                $application,
                $oldStatus,
                'for-release',
                Auth::user()
            );

            // Log the activity
            $this->logReviewActivity(
                $application,
                Auth::user(),
                'status_updated',
                $oldStatus,
                'for-release',
                'Application marked as for release'
            );

            return redirect()->back()->with('success', 'Application marked as for release.');

        } catch (\Exception $e) {
            Log::error('Error marking application as for release: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Error updating application status.');
        }
    }

    /**
     * Log review activity
     */
    private function logReviewActivity($application, $reviewer, $action, $oldStatus, $newStatus, $remarks)
    {
        try {
            if (class_exists('App\Models\ApplicationReviewActivity')) {
                \App\Models\ApplicationReviewActivity::create([
                    'application_id' => $application->id,
                    'reviewer_id' => $reviewer->id,
                    'action' => $action,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'remarks' => $remarks,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error logging review activity: ' . $e->getMessage());
        }
    }

    /**
     * Get pending applications count (for dashboard)
     */
    public function getPendingCount()
    {
        $count = ApplicationDocument::where('status', 'pending')->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Bulk verify multiple applications
     */
    public function bulkVerify(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:application_documents,id',
            'notes' => 'nullable|string'
        ]);

        $count = 0;
        $errors = [];

        foreach ($request->application_ids as $id) {
            try {
                $application = ApplicationDocument::find($id);
                $oldStatus = $application->status;
                
                $application->markAsVerified(Auth::id(), $request->notes);

                // TRIGGER NOTIFICATION for each applicant
                $this->notificationService->notifyApplicantStatusChange(
                    $application,
                    $oldStatus,
                    'verified',
                    Auth::user()
                );

                $count++;
            } catch (\Exception $e) {
                $errors[] = "Failed to verify application ID {$id}: " . $e->getMessage();
            }
        }

        $message = "{$count} applications verified successfully.";
        
        if (!empty($errors)) {
            return redirect()->back()->with('warning', $message)->with('errors', $errors);
        }

        return redirect()->back()->with('success', $message);
    }
}