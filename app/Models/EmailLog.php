<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmailLog extends Model
{
    use HasFactory;

    protected $table = 'email_logs';

    protected $fillable = [
        'user_id',
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'notification_type',
        'status',
        'error_message',
        'sent_at',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user associated with this email
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only sent emails
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope to get only failed emails
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get only queued emails
     */
    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    /**
     * Get the short notification name
     */
    public function getNotificationNameAttribute()
    {
        if (!$this->notification_type) {
            return 'General Email';
        }

        $parts = explode('\\', $this->notification_type);
        return end($parts);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'sent' => 'success',
            'failed' => 'danger',
            'queued' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Log an email
     */
    public static function logEmail(
        $recipientEmail, 
        $subject, 
        $status = 'sent', 
        $userId = null, 
        $recipientName = null,
        $body = null,
        $notificationType = null,
        $errorMessage = null,
        $metadata = null
    ) {
        // Ensure recipient email is a string
        if (is_object($recipientEmail) && method_exists($recipientEmail, 'getAddress')) {
            $recipientEmail = $recipientEmail->getAddress();
        }
        
        // Ensure recipient name is a string
        if (is_object($recipientName) && method_exists($recipientName, 'getName')) {
            $recipientName = $recipientName->getName();
        }
        
        // Ensure subject is a string
        $subject = is_string($subject) ? $subject : (string) $subject;
        
        return self::create([
            'user_id' => $userId,
            'recipient_email' => (string) $recipientEmail,
            'recipient_name' => $recipientName ? (string) $recipientName : null,
            'subject' => $subject,
            'body' => $body,
            'notification_type' => $notificationType,
            'status' => $status,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'sent' ? now() : null,
            'ip_address' => request()->ip(),
            'metadata' => $metadata,
        ]);
    }
}
