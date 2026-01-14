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
            ->subject('Account Registration - Research Management System')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for registering with the Research Management System (RMS) - URC FCIT.')
            ->line('**Your Account Details:**')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Password:** ' . $this->password)
            ->line('**Account Status:** Pending Approval')
            ->line('**Important Information:**')
            ->line('• Your account is currently **inactive** and pending admin approval.')
            ->line('• You will receive an email notification once your account is approved.')
            ->line('• Until then, you can browse publications and submit new ones as a guest.')
            ->line('• Once approved, you will be able to access the full dashboard.')
            ->action('Visit Home Page', url('/'))
            ->line('**What you can do now:**')
            ->line('• Browse all publications on the home page')
            ->line('• Submit new publications (will be linked after approval)')
            ->line('• Review publication details')
            ->line('If you did not create this account, please ignore this email or contact support.')
            ->salutation('Best regards,  
Research Management System Team  
URC FCIT');
    }
}
