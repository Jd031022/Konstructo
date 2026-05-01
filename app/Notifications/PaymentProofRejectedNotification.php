<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentProofRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $staff;
    protected $reason;
    protected $paymentProof;

    public function __construct(ApplicationDocument $application, User $staff, $reason, PaymentProof $paymentProof)
    {
        $this->application = $application;
        $this->staff = $staff;
        $this->reason = $reason;
        $this->paymentProof = $paymentProof;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'payment_proof_rejected',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'payment_proof_id' => $this->paymentProof->id,
            'rejected_by' => $this->staff->first_name . ' ' . $this->staff->last_name,
            'rejection_reason' => $this->reason,
            'message' => "Your Official Receipt for application #{$this->application->application_number} was rejected. Reason: {$this->reason}",
            'icon' => 'M6 18L18 6M6 6l12 12',
            'action_url' => "/applicant/application-details/{$this->application->id}",
            'created_at' => now()
        ];
    }
}