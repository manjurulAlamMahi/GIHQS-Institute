<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AdvisoryRequest;

class AdvisoryStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public AdvisoryRequest $advisoryRequest;
    public string $status;
    public ?string $adminNotes;
    public ?string $actionLink;

    /**
     * Create a new message instance.
     */
    public function __construct(
        AdvisoryRequest $advisoryRequest,
        string $status,
        ?string $adminNotes = null,
        ?string $actionLink = null
    ) {
        $this->advisoryRequest = $advisoryRequest;
        $this->status = $status;
        $this->adminNotes = $adminNotes;
        $this->actionLink = $actionLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = "Advisory Consultation Request: " . ucfirst($this->status) . " [{$this->advisoryRequest->reference_number}]";

        return new Envelope(
            from: config('mail.from.address'),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.advisory_status',
            with: [
                'advisoryRequest' => $this->advisoryRequest,
                'status'          => $this->status,
                'adminNotes'      => $this->adminNotes,
                'actionLink'      => $this->actionLink,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
