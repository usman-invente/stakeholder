<?php

namespace App\Mail;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitorRegistered extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Visitor $visitor,
        public ?string $qrCodePath = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Visitor Registration: ' . $this->visitor->full_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.visitor-registered',
            with: [
                'visitor' => $this->visitor,
                'meetingUrl' => url('/meetings/' . $this->visitor->meeting_id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->qrCodePath && file_exists($this->qrCodePath)) {
            try {
                $attachments[] = Attachment::fromPath($this->qrCodePath)
                    ->as('visitor_qrcode.png')
                    ->withMime('image/png');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to attach QR code: ' . $e->getMessage());
            }
        }
        
        return $attachments;
    }
}
