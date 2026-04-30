<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentOrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $paymentOrder;
    protected $treasurer;

    public function __construct($application, $paymentOrder, $treasurer)
    {
        $this->application = $application;
        $this->paymentOrder = $paymentOrder;
        $this->treasurer = $treasurer;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_order_created',
            'title' => 'Payment Order Number Ready',
            'message' => "Your Payment Order Number {$this->paymentOrder->order_number} is ready. Please proceed with your payment.",
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'order_number' => $this->paymentOrder->order_number,
            'treasurer_name' => $this->treasurer->first_name . ' ' . $this->treasurer->last_name,
            'action_url' => "/applicant/application-details/{$this->application->id}"
        ];
    }
}