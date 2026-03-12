<?php

namespace App\Services;

use App\Models\User;
use App\Models\ApplicationDocument;
use App\Notifications\ApplicationStatusNotification;
use App\Notifications\AdminNoteNotification;
use App\Notifications\NewApplicationNotification;
use App\Notifications\HardCopyReceivedNotification;
use App\Notifications\StaffStatusChangeNotification;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    
    /**
 * Send notification to applicant when status changes
 */
public function notifyApplicantStatusChange(ApplicationDocument $application, $oldStatus, $newStatus, User $reviewer)
{
    Log::info('========== NOTIFY APPLICANT STATUS CHANGE START ==========');
    Log::info('Parameters:', [
        'application_id' => $application->id,
        'application_number' => $application->application_number,
        'old_status' => $oldStatus,
        'new_status' => $newStatus,
        'reviewer_id' => $reviewer->id,
        'reviewer_email' => $reviewer->email,
        'reviewer_name' => $reviewer->first_name . ' ' . $reviewer->last_name
    ]);
    
    $applicant = $application->user;
    
    if (!$applicant) {
        Log::error('❌ No applicant found for application!');
        Log::info('========== NOTIFY APPLICANT STATUS CHANGE END (ERROR) ==========');
        return;
    }
    
    Log::info('Applicant found:', [
        'applicant_id' => $applicant->id,
        'applicant_email' => $applicant->email,
        'applicant_name' => $applicant->first_name . ' ' . $applicant->last_name,
        'applicant_role' => $applicant->role
    ]);

    $messages = [
        'pending' => 'Your application is now pending review.',
        'under-review' => 'Your application is now under review.',
        'approved' => 'Your application has been approved! Please prepare your hard copies for submission.',
        'rejected' => 'Your application has been rejected. Please check the reason and resubmit.',
        'for-release' => 'Your application is ready for release.',
        'verified' => 'Your application has been verified.'
    ];

    $message = $messages[$newStatus] ?? "Your application status has been updated to {$newStatus}.";
    
    $details = $newStatus === 'approved' 
        ? 'Please prepare the original hard copies of your documents for submission.'
        : null;

    Log::info('Notification details:', [
        'message' => $message,
        'details' => $details
    ]);

    // Send notification to applicant - WITH DETAILED DEBUGGING
    try {
        Log::info('Creating notification instance...');
        
        $notification = new ApplicationStatusNotification(
            $application,
            $oldStatus,
            $newStatus,
            $message,
            $details
        );
        
        Log::info('Notification instance created', [
            'notification_class' => get_class($notification),
            'via' => json_encode($notification->via($applicant))
        ]);
        
        Log::info('Calling $applicant->notify()...');
        
        // Check if notifications table exists
        $tableExists = \Illuminate\Support\Facades\Schema::hasTable('notifications');
        Log::info('Notifications table exists: ' . ($tableExists ? 'YES' : 'NO'));
        
        if (!$tableExists) {
            Log::error('❌ Notifications table does not exist!');
            return;
        }
        
        // Count before
        $beforeCount = $applicant->notifications()->count();
        Log::info('Notifications before: ' . $beforeCount);
        
        // Send notification
        $applicant->notify($notification);
        
        // Count after
        $afterCount = $applicant->notifications()->count();
        Log::info('Notifications after: ' . $afterCount);
        
        if ($afterCount > $beforeCount) {
            Log::info('✅ Notification created successfully in database');
            
            // Get the latest notification
            $latest = $applicant->notifications()->latest()->first();
            Log::info('Latest notification ID: ' . $latest->id);
            Log::info('Latest notification data: ' . json_encode($latest->data));
        } else {
            Log::error('❌ No notification was created in the database!');
            
            // Try to get the raw database query to see if there's an error
            try {
                $pdo = DB::connection()->getPdo();
                Log::info('Database connection OK');
            } catch (\Exception $dbEx) {
                Log::error('Database connection error: ' . $dbEx->getMessage());
            }
        }
        
    } catch (\Exception $e) {
        Log::error('❌ Failed to send notification: ' . $e->getMessage());
        Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
        Log::error($e->getTraceAsString());
    }

    // Also notify staff about this change (optional)
    if (in_array($newStatus, ['verified', 'approved', 'rejected'])) {
        Log::info('Also notifying staff about status change');
        $this->notifyStaffOfStatusChange($application, $oldStatus, $newStatus, $reviewer);
    }

    // Log this activity in application_review_activities table
    try {
        Log::info('Creating review activity record...');
        
        $activity = $application->reviewActivities()->create([
            'reviewer_id' => $reviewer->id,
            'action' => 'status_updated',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'remarks' => "Status changed from {$oldStatus} to {$newStatus} by {$reviewer->first_name} {$reviewer->last_name}",
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
        
        Log::info('✅ Review activity created with ID: ' . $activity->id);
    } catch (\Exception $e) {
        Log::error('❌ Failed to log review activity: ' . $e->getMessage());
    }

    // Update last_updated_by
    try {
        $application->update([
            'last_updated_by' => $reviewer->id
        ]);
        Log::info('✅ Updated last_updated_by to: ' . $reviewer->id);
    } catch (\Exception $e) {
        Log::error('❌ Failed to update last_updated_by: ' . $e->getMessage());
    }

    Log::info('========== NOTIFY APPLICANT STATUS CHANGE END ==========');
}

    /**
     * Send notification when admin adds notes
     */
    public function notifyApplicantOfNote(ApplicationDocument $application, $note, User $reviewer)
    {
        Log::info('========== NOTIFY APPLICANT OF NOTE ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'reviewer_id' => $reviewer->id,
            'reviewer_name' => $reviewer->first_name . ' ' . $reviewer->last_name,
            'note_length' => strlen($note)
        ]);
        
        $applicant = $application->user;
        
        if (!$applicant) {
            Log::error('❌ No applicant found!');
            return;
        }
        
        // Send notification to applicant
        try {
            $applicant->notify(new AdminNoteNotification(
                $application,
                $note,
                $reviewer
            ));
            Log::info('✅ Note notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send note notification: ' . $e->getMessage());
        }

        // Log the note activity
        try {
            $activity = $application->reviewActivities()->create([
                'reviewer_id' => $reviewer->id,
                'action' => 'note_added',
                'remarks' => $note,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
            Log::info('✅ Note activity logged with ID: ' . $activity->id);
        } catch (\Exception $e) {
            Log::error('❌ Failed to log note activity: ' . $e->getMessage());
        }

        // Update last_updated_by
        $application->update(['last_updated_by' => $reviewer->id]);
        
        Log::info('========== NOTIFY APPLICANT OF NOTE END ==========');
    }

    /**
     * Send notification to staff when application is submitted
     */
    public function notifyStaffNewApplication(ApplicationDocument $application)
    {
        Log::info('========== NOTIFY STAFF NEW APPLICATION ==========');
        Log::info('Application:', [
            'id' => $application->id,
            'number' => $application->application_number
        ]);
        
        $staff = User::whereIn('role', ['admin', 'staff'])->get();
        $applicant = $application->user;
        
        Log::info('Staff to notify count: ' . $staff->count());
        
        foreach ($staff as $user) {
            try {
                $user->notify(new NewApplicationNotification($application, $applicant));
                Log::info('✅ Notified staff: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to notify staff ' . $user->email . ': ' . $e->getMessage());
            }
        }

        // Log the submission
        try {
            $activity = $application->reviewActivities()->create([
                'reviewer_id' => $applicant->id,
                'action' => 'application_created',
                'old_status' => 'draft',
                'new_status' => 'pending',
                'remarks' => 'Application submitted by applicant',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
            Log::info('✅ Submission logged with ID: ' . $activity->id);
        } catch (\Exception $e) {
            Log::error('❌ Failed to log submission: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY STAFF NEW APPLICATION END ==========');
    }

    /**
     * Send notification when hard copy is received
     */
    public function notifyHardCopyReceived(ApplicationDocument $application, User $reviewer)
    {
        Log::info('========== NOTIFY HARD COPY RECEIVED ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'reviewer_id' => $reviewer->id,
            'reviewer_name' => $reviewer->first_name . ' ' . $reviewer->last_name
        ]);
        
        $applicant = $application->user;
        
        // Send notification to applicant
        try {
            $applicant->notify(new HardCopyReceivedNotification($application));
            Log::info('✅ Hard copy notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send hard copy notification: ' . $e->getMessage());
        }
        
        // Log the activity
        try {
            $activity = $application->reviewActivities()->create([
                'reviewer_id' => $reviewer->id,
                'action' => 'hard_copy_received',
                'remarks' => 'Hard copies received and verified',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
            Log::info('✅ Hard copy activity logged with ID: ' . $activity->id);
        } catch (\Exception $e) {
            Log::error('❌ Failed to log hard copy activity: ' . $e->getMessage());
        }

        // Update hard copy status
        $application->update([
            'hard_copy_received' => true,
            'hard_copy_received_at' => now(),
            'last_updated_by' => $reviewer->id
        ]);
        
        Log::info('========== NOTIFY HARD COPY RECEIVED END ==========');
    }

    /**
     * Send notification to staff about status change (for monitoring)
     */
    protected function notifyStaffOfStatusChange(ApplicationDocument $application, $oldStatus, $newStatus, User $reviewer)
    {
        Log::info('========== NOTIFY STAFF OF STATUS CHANGE ==========');
        
        $staff = User::whereIn('role', ['admin', 'staff'])
            ->where('id', '!=', $reviewer->id)
            ->get();
        
        $applicant = $application->user;
        
        Log::info('Notifying ' . $staff->count() . ' staff members about status change');
        
        foreach ($staff as $user) {
            try {
                $user->notify(new StaffStatusChangeNotification(
                    $application,
                    $applicant,
                    $oldStatus,
                    $newStatus,
                    $reviewer
                ));
                Log::info('✅ Notified staff: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to notify staff ' . $user->email . ': ' . $e->getMessage());
            }
        }

        // Log this staff notification activity
        try {
            if ($staff->count() > 0) {
                Log::info('Staff notified of status change', [
                    'application_id' => $application->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'reviewer' => $reviewer->id,
                    'notified_staff_count' => $staff->count()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log staff notification: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY STAFF OF STATUS CHANGE END ==========');
    }
}