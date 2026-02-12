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
            ->subject('RMS Account Application - Update Required')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for your interest in the Research Management System.');
        
        if ($this->reason) {
            $message->line('We regret to inform you that we are unable to approve your account application at this time.')
                   ->line('')
                   ->line('<strong>Reason for rejection:</strong>')
                   ->line($this->reason);
        } else {
            $message->line('We regret to inform you that we are unable to approve your account application at this time.');
        }
        
        $message->line('')
               ->line('<strong>What you can do next:</strong>')
               ->line('• Review the requirements for faculty registration')
               ->line('• Ensure all submitted credentials are valid and up-to-date')
               ->line('• Verify that all required information was provided correctly')
               ->line('• Contact our support team for clarification and guidance')
               ->line('')
               ->line('You may submit a new registration application once you have addressed the concerns mentioned above.')
               ->action('Contact Support', 'mailto:support@rms.uos.edu.pk')
               ->line('We appreciate your understanding and look forward to reviewing your updated application.')
               ->salutation('Best regards,  
Research Management Team');
        
        return $message;
    }
}
