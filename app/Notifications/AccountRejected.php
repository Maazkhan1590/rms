<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($reason = null)
    {
        $this->reason = $reason;
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
        $message = (new MailMessage)
            ->subject('RMS Account Application Update')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for your interest in the Research Management System.');
        
        if ($this->reason) {
            $message->line('Unfortunately, we are unable to approve your account at this time.')
                   ->line('**Reason:** ' . $this->reason);
        } else {
            $message->line('Unfortunately, we are unable to approve your account application at this time.');
        }
        
        $message->line('**What you can do:**')
               ->line('• Review the requirements for faculty registration')
               ->line('• Ensure all credentials are valid and up-to-date')
               ->line('• Contact our support team for clarification')
               ->line('You may reapply once you have addressed the issues mentioned above.')
               ->action('Contact Support', 'mailto:support@rms.uos.edu.pk')
               ->salutation('Best regards,  
Research Management Team');
        
        return $message;
    }
}
