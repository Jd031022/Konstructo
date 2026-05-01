<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HardCopyReceivedNotification extends Notification
{
    use Queueable;

    protected $application;

    public function __construct(ApplicationDocument $application)
    {
        $this->application = $application;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'hard_copy_received',
            'for_role' => 'applicant',
            'title' => 'Hard Copies Received',
            'message' => 'Your hard copy documents have been received and verified',
            'details' => "Application #: {$this->application->application_number}",
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'icon' => 'M20.354 15.354A9 9 0 008.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z',
            'link' => "/applicant/applications/{$this->application->id}"
        ];
    }
}