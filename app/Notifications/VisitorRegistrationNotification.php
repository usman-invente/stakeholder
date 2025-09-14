<?php

namespace App\Notifications;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VisitorRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $visitor;
    protected $qrCodePath;

    /**
     * Create a new notification instance.
     */
    public function __construct(Visitor $visitor, $qrCodePath = null)
    {
        $this->visitor = $visitor;
        $this->qrCodePath = $qrCodePath;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $mailMessage = (new MailMessage)
            ->subject('New Visitor Registration: ' . $this->visitor->full_name)
            ->greeting('Hello ' . $this->visitor->host_name . '!')
            ->line('You have a new visitor waiting for you at reception.')
            ->line('Visitor Details:')
            ->line('Name: ' . $this->visitor->full_name)
            ->line('Email: ' . ($this->visitor->email ?? 'Not provided'))
            ->line('Contact: ' . $this->visitor->contact_number)
            ->line('Check-in Time: ' . $this->visitor->check_in_time->format('F j, Y, g:i a'))
            ->action('View Meeting Details', url('/meetings/' . $this->visitor->meeting_id))
            ->line('Thank you for using our visitor management system!');
        
        // Try to attach the QR code if available
        if ($this->qrCodePath && file_exists($this->qrCodePath)) {
            try {
                $mailMessage->attach($this->qrCodePath);
                $mailMessage->line('A QR code is attached to this email. Scan it to view meeting details.');
            } catch (\Exception $e) {
                // If attachment fails, include a link to generate it instead
                $mailMessage->line('Unable to attach QR code. You can generate it by clicking the button above.');
            }
        } else {
            // If no QR code path provided, just tell them to use the link
            $mailMessage->line('Scan the QR code or click the button above to view meeting details.');
        }
        
        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'visitor_id' => $this->visitor->id,
            'visitor_name' => $this->visitor->full_name,
            'check_in_time' => $this->visitor->check_in_time,
            'meeting_id' => $this->visitor->meeting_id,
        ];
    }
}
