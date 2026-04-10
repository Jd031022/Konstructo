<?php

namespace App\Notifications;

use App\Models\ApplicationDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class BFPCommentsAddedNotification extends Notification
{
    use Queueable;

    protected $application;
    protected $bfpUser;
    protected $comments;
    protected $recipientType;

    public function __construct(ApplicationDocument $application, User $bfpUser, $comments, $recipientType = 'applicant')
    {
        $this->application = $application;
        $this->bfpUser = $bfpUser;
        $this->comments = $comments;
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
                'title' => 'BFP Comments Added',
                'message' => "The Bureau of Fire Protection (BFP) has added comments to your application #{$this->application->application_number}.",
                'type' => 'bfp_comments_added',
                'application_id' => $this->application->id,
                'application_number' => $this->application->application_number,
                'bfp_user_name' => $this->bfpUser->first_name . ' ' . $this->bfpUser->last_name,
                'comments' => $this->comments,
                'action_url' => "/applicant/application-details/{$this->application->id}"
            ];
        } else {
            return [
                'title' => 'BFP Comments Added',
                'message' => "BFP user {$this->bfpUser->first_name} {$this->bfpUser->last_name} has added comments to application #{$this->application->application_number}.",
                'type' => 'bfp_comments_added_staff',
                'application_id' => $this->application->id,
                'application_number' => $this->application->application_number,
                'applicant_name' => $this->application->user ? $this->application->user->first_name . ' ' . $this->application->user->last_name : 'Unknown',
                'bfp_user_name' => $this->bfpUser->first_name . ' ' . $this->bfpUser->last_name,
                'comments' => $this->comments,
                'action_url' => "/staff/application-details/{$this->application->id}"
            ];
        }
    }
}