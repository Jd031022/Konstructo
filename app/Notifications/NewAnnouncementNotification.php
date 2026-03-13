<?php

namespace App\Notifications;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewAnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $announcement;
    protected $creator;

    /**
     * Create a new notification instance.
     */
    public function __construct(Announcement $announcement, User $creator)
    {
        $this->announcement = $announcement;
        $this->creator = $creator;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $role = $notifiable->role === 'applicant' ? 'Applicant' : 'Staff';
        
        return (new MailMessage)
            ->subject('📢 New Announcement: ' . $this->announcement->title)
            ->greeting("Hello {$notifiable->first_name}!")
            ->line("A new announcement has been posted by {$this->creator->first_name} :")
            ->line("")
            ->line("**{$this->announcement->title}**")
            ->line($this->announcement->content)
            ->line("")
            ->line("---")
            ->line("Please check your dashboard for more details.")
            ->line("Thank you for using Konstructo!");
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $roleSpecificLink = $notifiable->role === 'applicant' 
            ? '/applicant/dashboard' 
            : '/staff/dashboard';
        
        return [
            'type' => 'new_announcement',
            'for_role' => $notifiable->role,
            'title' => '📢 New Announcement: ' . $this->announcement->title,
            'message' => $this->announcement->content,
            'details' => "Posted by: {$this->creator->first_name} {$this->creator->last_name}",
            'announcement_id' => $this->announcement->id,
            'announcement_color' => $this->announcement->color,
            'link' => $roleSpecificLink,
            'icon' => $this->getIconForColor($this->announcement->color),
        ];
    }

    /**
     * Get icon based on announcement color
     */
    private function getIconForColor($color): string
    {
        return match($color) {
            'blue' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'green' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'yellow' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'red' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }
}