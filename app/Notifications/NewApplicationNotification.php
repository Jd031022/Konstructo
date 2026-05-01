<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $applicant;

    public function __construct(ApplicationDocument $application, User $applicant)
    {
        $this->application = $application;
        $this->applicant = $applicant;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'application_submitted',
            'for_role' => 'staff',
            'title' => 'New Application Submitted',
            'message' => "{$this->applicant->full_name} submitted a new application",
            'details' => "Application #: {$this->application->application_number}",
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'applicant_name' => $this->applicant->full_name,
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'link' => "/admin/applications/{$this->application->id}"
        ];
    }
}