<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminNoteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $note;
    protected $reviewer;

    public function __construct(ApplicationDocument $application, $note, User $reviewer)
    {
        $this->application = $application;
        $this->note = $note;
        $this->reviewer = $reviewer;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'admin_note',
            'for_role' => 'applicant',
            'title' => 'New Note on Your Application',
            'message' => "{$this->reviewer->full_name} added a note to your application",
            'details' => $this->note,
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'reviewer_name' => $this->reviewer->full_name,
            'link' => "/applicant/applications/{$this->application->id}"
        ];
    }
}