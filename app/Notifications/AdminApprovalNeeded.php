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
            ->subject('New Faculty Registration - Approval Required')
            ->greeting('Hello Administrator,')
            ->line('A new faculty member has registered and requires approval.')
            ->line('**Faculty Details:**')
            ->line('Name: ' . $this->newUser->name)
            ->line('Email: ' . $this->newUser->email)
            ->line('Department: ' . $this->newUser->department)
            ->line('Designation: ' . $this->newUser->designation)
            ->line('Employee ID: ' . $this->newUser->employee_id)
            ->action('Review Application', route('admin.users.show', $this->newUser->id))
            ->line('Please review the credentials and approve or reject the application.')
            ->line('This user will not be able to access the system until approved.');
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
