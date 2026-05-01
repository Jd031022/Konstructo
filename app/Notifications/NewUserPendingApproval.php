<?php
// app/Notifications/NewUserPendingApproval.php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserPendingApproval extends Notification
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New User Pending Approval')
            ->line("A new user ({$this->user->first_name} {$this->user->last_name}) has registered and verified their email.")
            ->line("Email: {$this->user->email}")
            ->action('Review User', url('/admin/settings?tab=roles'))
            ->line('Please review and approve this user account.');
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->first_name . ' ' . $this->user->last_name,
            'user_email' => $this->user->email,
            'message' => 'New user pending approval',
            'type' => 'user_approval',
            'for_role' => 'admin',
            'icon' => 'M12 4.354a4 4 0 110 8.646 4 4 0 010-8.646M12 14H8a4 4 0 00-4 4v2a2 2 0 002 2h12a2 2 0 002-2v-2a4 4 0 00-4-4h-4z'
        ];
    }
}