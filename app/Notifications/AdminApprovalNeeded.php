<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminApprovalNeeded extends Notification implements ShouldQueue
{
    use Queueable;

    public $newUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->newUser = $user;
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
            ->subject('New Faculty Registration - Action Required')
            ->greeting('Hello Administrator,')
            ->line('A new faculty member has registered in the Research Management System and requires your approval.')
            ->line('**Faculty Details:**')
            ->line('• **Name:** ' . $this->newUser->name)
            ->line('• **Email:** ' . $this->newUser->email)
            ->line('• **Department:** ' . ($this->newUser->department ?? 'Not specified'))
            ->line('• **Designation:** ' . ($this->newUser->designation ?? 'Not specified'))
            ->line('• **Employee ID:** ' . ($this->newUser->employee_id ?? 'Not specified'))
            ->line('• **Registration Date:** ' . $this->newUser->created_at->format('F d, Y h:i A'))
            ->action('Review Application', route('admin.users.show', $this->newUser->id))
            ->line('Please review the faculty credentials and either approve or reject the application.')
            ->line('**Important:** The user cannot access the system until the account is approved.')
            ->salutation('Best regards,  
Research Management System');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'user_email' => $this->newUser->email,
            'department' => $this->newUser->department,
            'message' => 'New faculty registration requires approval',
        ];
    }
}
