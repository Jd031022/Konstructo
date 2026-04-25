<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentProofVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $staff;
    protected $paymentProof;

    public function __construct(ApplicationDocument $application, User $staff, PaymentProof $paymentProof)
    {
        $this->application = $application;
        $this->staff = $staff;
        $this->paymentProof = $paymentProof;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'payment_proof_verified',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'payment_proof_id' => $this->paymentProof->id,
            'verified_by' => $this->staff->first_name . ' ' . $this->staff->last_name,
            'or_link' => $this->paymentProof->or_link,
            'message' => "Your Official Receipt for application #{$this->application->application_number} has been verified by {$this->staff->first_name} {$this->staff->last_name}",
            'action_url' => "/applicant/application-details/{$this->application->id}",
            'created_at' => now()
        ];
    }
}