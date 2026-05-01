<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class CertificateUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $cpdoUser;
    protected $certificateType;
    protected $certificateName;
    protected $certificateLink;

    public function __construct(ApplicationDocument $application, User $cpdoUser, $certificateType, $certificateName, $certificateLink)
    {
        $this->application = $application;
        $this->cpdoUser = $cpdoUser;
        $this->certificateType = $certificateType;
        $this->certificateName = $certificateName;
        $this->certificateLink = $certificateLink;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'certificate_uploaded',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'certificate_type' => $this->certificateType,
            'certificate_name' => $this->certificateName,
            'certificate_link' => $this->certificateLink,
            'uploaded_by' => $this->cpdoUser->first_name . ' ' . $this->cpdoUser->last_name,
            'message' => "{$this->certificateName} has been uploaded for application #{$this->application->application_number}",
            'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
            'action_url' => "/applicant/application-details/{$this->application->id}",
            'created_at' => now()
        ];
    }
}