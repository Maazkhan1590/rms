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
            ->line('Thank you for registering with the Research Management System (RMS)')
            ->line('<strong>Your Account Details:</strong>')
            ->line('<strong>Email:</strong> ' . $notifiable->email)
            ->line('<strong>Password:</strong> ' . $this->password)
            ->line('<strong>Account Status:</strong> Pending Approval')
            ->line('<strong>Important Information:</strong>')
            ->line('• Your account is currently <strong>pending admin approval</strong> and will be activated shortly.')
            ->line('• You will receive a confirmation email once your account is approved by our administrators.')
            ->line('• After approval, you can log in and access your profile.')
            ->line('• You can browse public profiles and publications while waiting for approval.')
            ->action('Browse Publications', url('/publications'))
            ->line('If you did not create this account, please contact support immediately.')
            ->salutation('Best regards,
Research Management System Team');
    }
}
