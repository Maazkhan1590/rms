<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        $this->password = $password;
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
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for registering with the Research Management System (RMS) - URC FCIT.')
            ->line('**Your Account Details:**')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Password:** ' . $this->password)
            ->line('**Account Status:** Pending Approval')
            ->line('**Important Information:**')
            ->line('• Your account is currently **pending admin approval** and will be activated shortly.')
            ->line('• You will receive a confirmation email once your account is approved by our administrators.')
            ->line('• After approval, you can log in and access your profile.')
            ->line('• You can browse public profiles and publications while waiting for approval.')
            ->action('Browse Publications', url('/publications'))
            ->line('**After approval, you will be able to:**')
            ->line('• Manage your research profile')
            ->line('• Submit publications and research work')
            ->line('• View analytics and reports')
            ->line('• Generate and download your CV')
            ->line('If you did not create this account, please contact support immediately.')
            ->salutation('Best regards,  
Research Management System Team  
URC FCIT');
    }
}
