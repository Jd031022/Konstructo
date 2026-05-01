<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class FSECUploadedNotification extends Notification
{
    use Queueable;

    protected $application;
    protected $bfpUser;
    protected $fsecLink;
    protected $filename;
    protected $recipientType;

    public function __construct(ApplicationDocument $application, User $bfpUser, $fsecLink, $filename, $recipientType = 'applicant')
    {
        $this->application = $application;
        $this->bfpUser = $bfpUser;
        $this->fsecLink = $fsecLink;
        $this->filename = $filename;
        $this->recipientType = $recipientType;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        if ($this->recipientType === 'applicant') {
            return [
                'title' => 'FSEC Document Uploaded',
                'message' => "The Bureau of Fire Protection (BFP) has uploaded the Fire Safety Evaluation Clearance (FSEC) for your application #{$this->application->application_number}.",
                'type' => 'fsec_uploaded',
                'application_id' => $this->application->id,
                'application_number' => $this->application->application_number,
                'bfp_user_name' => $this->bfpUser->first_name . ' ' . $this->bfpUser->last_name,
                'filename' => $this->filename,
                'fsec_link' => $this->fsecLink,
                'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                'action_url' => "/applicant/application-details/{$this->application->id}"
            ];
        } else {
            return [
                'title' => 'FSEC Document Uploaded',
                'message' => "BFP user {$this->bfpUser->first_name} {$this->bfpUser->last_name} has uploaded FSEC for application #{$this->application->application_number}.",
                'type' => 'fsec_uploaded_staff',
                'application_id' => $this->application->id,
                'application_number' => $this->application->application_number,
                'applicant_name' => $this->application->user ? $this->application->user->first_name . ' ' . $this->application->user->last_name : 'Unknown',
                'bfp_user_name' => $this->bfpUser->first_name . ' ' . $this->bfpUser->last_name,
                'filename' => $this->filename,
                'fsec_link' => $this->fsecLink,
                'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                'action_url' => "/staff/application-details/{$this->application->id}"
            ];
        }
    }
}