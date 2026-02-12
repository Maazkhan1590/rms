<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmail extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Research Management System')
            ->greeting('Welcome, ' . $notifiable->name . '!')
            ->line('Thank you for registering with the Research Management System.')
            ->line('Your account has been created successfully and is currently pending approval.')
            ->line('**Next Steps:**')
            ->line('1. Our administrators will review your credentials and verify your information.')
            ->line('2. You will receive a notification email once your account is approved.')
            ->line('3. After approval, you can log in and access your research profile.')
            ->line('**What you can do while waiting:**')
            ->line('• Browse public faculty profiles')
            ->line('• Review published research and publications')
            ->line('• Prepare your research documentation for upload')
            ->line('• Familiarize yourself with the system features')
            ->action('Explore Faculty Profiles', route('faculty-members.index'))
            ->line('**Once approved, you will have access to:**')
            ->line('• Your public research profile')
            ->line('• Publication submission system')
            ->line('• Research analytics and scoring')
            ->line('• CV generation and downloads')
            ->line('If you did not create this account, please contact our support team immediately.')
            ->salutation('Best regards,  
Research Management Team');
    }
}
