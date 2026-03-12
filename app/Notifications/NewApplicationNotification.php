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
            'link' => "/admin/applications/{$this->application->id}"
        ];
    }
}