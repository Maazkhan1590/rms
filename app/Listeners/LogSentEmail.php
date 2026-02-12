<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSentEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Convert Address objects to email strings
     */
    private function extractEmailFromAddress($address): ?string
    {
        if (is_string($address)) {
            return $address;
        }
        
        if (is_object($address) && method_exists($address, 'getAddress')) {
            return $address->getAddress();
        }
        
        return null;
    }

    /**
     * Convert Address objects to name strings
     */
    private function extractNameFromAddress($address): ?string
    {
        if (is_object($address) && method_exists($address, 'getName')) {
            return $address->getName();
        }
        
        return null;
    }

    /**
     * Convert array of Address objects to email strings
     */
    private function addressArrayToEmails($addresses): array
    {
        if (!is_array($addresses)) {
            return [];
        }

        $emails = [];
        foreach ($addresses as $address) {
            $email = $this->extractEmailFromAddress($address);
            if ($email) {
                $emails[] = $email;
            }
        }
        return $emails;
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $data = $event->data;

        // Extract recipient information
        $recipients = $message->getTo();
        $recipientEmail = null;
        $recipientName = null;

        if (is_array($recipients) && !empty($recipients)) {
            $firstRecipient = reset($recipients);
            $recipientEmail = $this->extractEmailFromAddress($firstRecipient);
            $recipientName = $this->extractNameFromAddress($firstRecipient);
        }

        // Get user ID if notification has notifiable
        $userId = null;
        if (isset($data['notifiable']) && method_exists($data['notifiable'], 'getKey')) {
            $userId = $data['notifiable']->getKey();
        }

        // Get notification type
        $notificationType = null;
        if (isset($data['notification'])) {
            $notificationType = get_class($data['notification']);
        }

        // Get email body
        $body = null;
        try {
            $messageBody = $message->getBody();
            
            if ($messageBody) {
                // Handle different body types
                if (is_string($messageBody)) {
                    $body = substr($messageBody, 0, 5000);
                } elseif (method_exists($messageBody, 'bodyToString')) {
                    $body = substr($messageBody->bodyToString(), 0, 5000);
                } elseif (method_exists($messageBody, 'getBody')) {
                    $bodyContent = $messageBody->getBody();
                    if (is_string($bodyContent)) {
                        $body = substr($bodyContent, 0, 5000);
                    }
                }
            }
        } catch (\Exception $e) {
            // If body extraction fails, just continue without it
            $body = null;
        }

        // Log the email
        try {
            // Safely extract metadata
            $metadata = [];
            
            try {
                if (method_exists($message, 'getHeaders')) {
                    $headers = $message->getHeaders();
                    if ($headers && method_exists($headers, 'get')) {
                        $messageIdHeader = $headers->get('Message-ID');
                        if ($messageIdHeader) {
                            $metadata['message_id'] = method_exists($messageIdHeader, 'getBodyAsString') 
                                ? $messageIdHeader->getBodyAsString() 
                                : (string) $messageIdHeader;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore header extraction errors
            }

            try {
                if (method_exists($message, 'getFrom')) {
                    $from = $message->getFrom();
                    if ($from && !empty($from)) {
                        $metadata['from'] = $this->addressArrayToEmails($from);
                    }
                }
            } catch (\Exception $e) {
                // Ignore from extraction errors
            }

            try {
                if (method_exists($message, 'getCc')) {
                    $cc = $message->getCc();
                    if ($cc && !empty($cc)) {
                        $metadata['cc'] = $this->addressArrayToEmails($cc);
                    }
                }
            } catch (\Exception $e) {
                // Ignore cc extraction errors
            }

            try {
                if (method_exists($message, 'getBcc')) {
                    $bcc = $message->getBcc();
                    if ($bcc && !empty($bcc)) {
                        $metadata['bcc'] = $this->addressArrayToEmails($bcc);
                    }
                }
            } catch (\Exception $e) {
                // Ignore bcc extraction errors
            }

            EmailLog::logEmail(
                recipientEmail: $recipientEmail ?? 'unknown@email.com',
                subject: $message->getSubject() ?? 'No Subject',
                status: 'sent',
                userId: $userId,
                recipientName: $recipientName,
                body: $body,
                notificationType: $notificationType,
                metadata: !empty($metadata) ? $metadata : null
            );
        } catch (\Exception $e) {
            // Log any errors silently to avoid breaking email flow
            \Log::error('Failed to log email: ' . $e->getMessage());
        }
    }
}
