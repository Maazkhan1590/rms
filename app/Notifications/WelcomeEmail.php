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
            ->subject('Welcome to RMS')
            ->greeting('Welcome, ' . $notifiable->name . '!')
            ->line('Thank you for registering with the Research Management System.')
            ->line('Your account has been created successfully and is currently pending approval.')
            ->line('**Next Steps:**')
            ->line('1. Please verify your email address by clicking the button below.')
            ->line('2. Our administrators will review your credentials.')
            ->line('3. You will receive a notification once your account is approved.')
            ->action('Verify Email Address', url('/email/verify/' . $notifiable->id))
            ->line('**What you can do while waiting:**')
            ->line('• Review our research policies and guidelines')
            ->line('• Prepare your research documentation')
            ->line('• Familiarize yourself with the system')
            ->line('If you did not create this account, please contact our support team immediately.')
            ->salutation('Best regards,  
Research Management Team');
    }
}
