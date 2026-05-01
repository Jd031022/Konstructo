<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification
{
    use Queueable;

    protected $application;
    protected $oldStatus;
    protected $newStatus;
    protected $message;
    protected $details;

    public function __construct(ApplicationDocument $application, $oldStatus, $newStatus, $message, $details = null)
    {
        $this->application = $application;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->message = $message;
        $this->details = $details;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'status_changed',
            'for_role' => 'applicant',
            'title' => 'Application Status Updated',
            'message' => $this->message,
            'details' => $this->details,
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'link' => "/applicant/applications/{$this->application->id}"
        ];
    }
}