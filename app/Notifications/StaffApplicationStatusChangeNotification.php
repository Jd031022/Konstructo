<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class StaffApplicationStatusChangeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $status;
    protected $statusDisplay;
    protected $remarks;
    protected $updatedBy;
    protected $updatedByPosition;

    public function __construct(ApplicationDocument $application, $status, $statusDisplay, $remarks, $updatedBy, $updatedByPosition)
    {
        $this->application = $application;
        $this->status = $status;
        $this->statusDisplay = $statusDisplay;
        $this->remarks = $remarks;
        $this->updatedBy = $updatedBy;
        $this->updatedByPosition = $updatedByPosition;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $statusColors = [
            'under-review' => 'purple',
            'document-verification' => 'blue',
            'for-assessment' => 'orange',
            'approved' => 'green',
            'rejected' => 'red',
            'for-release' => 'blue',
            'verified' => 'emerald'
        ];
        
        $color = $statusColors[$this->status] ?? 'gray';
        
        $formattedNumber = $this->application->application_number;
        if (strlen($formattedNumber) === 10) {
            $formattedNumber = substr($formattedNumber, 0, 2) . '-' . 
                              substr($formattedNumber, 2, 4) . '-' . 
                              substr($formattedNumber, 6, 4);
        }
        
        return [
            'type' => 'application_status_update',
            'application_id' => $this->application->id,
            'application_number' => $formattedNumber,
            'status' => $this->status,
            'status_display' => $this->statusDisplay,
            'status_color' => $color,
            'remarks' => $this->remarks,
            'updated_by' => $this->updatedBy,
            'updated_by_position' => $this->updatedByPosition,
            'message' => "Application #{$formattedNumber} status changed to: {$this->statusDisplay} by {$this->updatedBy} ({$this->updatedByPosition})",
            'action_url' => "/staff/application-details/{$this->application->id}",
            'icon' => 'clipboard-list',
            'priority' => in_array($this->status, ['approved', 'rejected', 'for-release']) ? 'high' : 'medium'
        ];
    }
}