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
            'action_url' => "/staff/application-details/{$this->application->id}",
            'created_at' => now()
        ];
    }
}