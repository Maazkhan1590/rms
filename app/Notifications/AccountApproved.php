<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your RMS Account Has Been Approved!')
            ->greeting('Great News, ' . $notifiable->name . '!')
            ->line('Your Research Management System account has been approved by our administrators.')
            ->line('You now have full access to the system and can start managing your research activities.')
            ->line('**What you can do now:**')
            ->line('• Submit your publications and research work')
            ->line('• Apply for grants and funding')
            ->line('• Track your research score and rankings')
            ->line('• Generate reports and analytics')
            ->line('• Collaborate with other researchers')
            ->action('Access Your Dashboard', url('/admin'))
            ->line('If you need any assistance getting started, please don\'t hesitate to contact our support team.')
            ->salutation('Welcome aboard!  
Research Management Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your account has been approved',
            'action' => 'dashboard',
        ];
    }
}
