<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class OwnershipDocumentRemarkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $staff;
    protected $documentKey;
    protected $documentName;
    protected $remark;

    public function __construct(ApplicationDocument $application, User $staff, $documentKey, $documentName, $remark)
    {
        $this->application = $application;
        $this->staff = $staff;
        $this->documentKey = $documentKey;
        $this->documentName = $documentName ?? $this->getDocumentNameFromKey($documentKey);
        $this->remark = $remark;
    }
    
    private function getDocumentNameFromKey($documentKey)
    {
        $documentNames = [
            'tct_link' => 'TCT / Deed of Sale',
            'tax_declaration_link' => 'Tax Declaration',
            'current_tax_receipt_link' => 'Current Tax Receipt',
            'spa_link' => 'Special Power of Attorney (SPA)'
        ];
        
        return $documentNames[$documentKey] ?? 'Ownership Document';
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'ownership_document_remark',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'staff_id' => $this->staff->id,
            'staff_name' => $this->staff->first_name . ' ' . $this->staff->last_name,
            'staff_position' => $this->staff->profile ? $this->staff->profile->position : 'Staff',
            'document_key' => $this->documentKey,
            'document_name' => $this->documentName,
            'remark' => $this->remark,
            'message' => "Clarification needed for your {$this->documentName}. Please review the remarks and provide the requested information.",
            'icon' => 'M12 9v2m0 4v2m0-6a4 4 0 110 8 4 4 0 010-8zm0-2a6 6 0 100 12 6 6 0 000-12z',
            'action_url' => "/applicant/application-details/{$this->application->id}",
            'requires_action' => true
        ];
    }
}