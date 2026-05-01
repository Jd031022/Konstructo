<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentProofUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $paymentProof;
    protected $applicant;

    public function __construct(ApplicationDocument $application, PaymentProof $paymentProof, User $applicant)
    {
        $this->application = $application;
        $this->paymentProof = $paymentProof;
        $this->applicant = $applicant;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'payment_proof_uploaded',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'payment_proof_id' => $this->paymentProof->id,
            'applicant_name' => $this->applicant->first_name . ' ' . $this->applicant->last_name,
            'or_link' => $this->paymentProof->or_link,
            'message' => "New Official Receipt uploaded by {$this->applicant->first_name} {$this->applicant->last_name} for application #{$this->application->application_number}",
            'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12',
            'action_url' => "/staff/application-details/{$this->application->id}",
            'created_at' => now()
        ];
    }
}