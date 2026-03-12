<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class StaffStatusChangeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $applicant;
    protected $oldStatus;
    protected $newStatus;
    protected $reviewer;

    /**
     * Create a new notification instance.
     */
    public function __construct(ApplicationDocument $application, User $applicant, $oldStatus, $newStatus, User $reviewer)
    {
        $this->application = $application;
        $this->applicant = $applicant;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->reviewer = $reviewer;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $statusColors = [
            'pending' => 'yellow',
            'under-review' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'for-release' => 'purple',
            'verified' => 'emerald'
        ];

        return [
            'type' => 'staff_status_change',
            'for_role' => 'staff',
            'title' => 'Application Status Updated',
            'message' => "Application #{$this->application->application_number} status changed from {$this->oldStatus} to {$this->newStatus}",
            'details' => "Updated by: {$this->reviewer->full_name}",
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'applicant_name' => $this->applicant->full_name,
            'reviewer_name' => $this->reviewer->full_name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'status_color' => $statusColors[$this->newStatus] ?? 'gray',
            'link' => "/admin/applications/{$this->application->id}"
        ];
    }
}