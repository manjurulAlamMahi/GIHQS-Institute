<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AccreditationApplication;

class AccreditationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public AccreditationApplication $application;
    public string $status;
    public ?string $adminNotes;
    public ?string $actionLink;

    /**
     * Create a new message instance.
     */
    public function __construct(
        AccreditationApplication $application,
        string $status,
        ?string $adminNotes = null,
        ?string $actionLink = null
    ) {
        $this->application = $application;
        $this->status = $status;
        $this->adminNotes = $adminNotes;
        $this->actionLink = $actionLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = "Accreditation Application Status: " . ucfirst($this->status) . " [{$this->application->reference_number}]";

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
            view: 'mails.accreditation_status',
            with: [
                'application' => $this->application,
                'status'      => $this->status,
                'adminNotes'  => $this->adminNotes,
                'actionLink'  => $this->actionLink,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        if ($this->application->certificate_pdf && file_exists(public_path($this->application->certificate_pdf))) {
            return [
                \Illuminate\Mail\Mailables\Attachment::fromPath(public_path($this->application->certificate_pdf))
                    ->as('Accreditation_Certificate_' . ($this->application->verification_code ?: $this->application->reference_number) . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }
        return [];
    }
}
