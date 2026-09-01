<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\CertificationApplication;

class CertificationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public CertificationApplication $application;
    public string $status;
    public ?string $adminNotes;
    public ?string $paymentLink;

    /**
     * Create a new message instance.
     */
    public function __construct(
        CertificationApplication $application,
        string $status,
        ?string $adminNotes = null,
        ?string $paymentLink = null
    ) {
        $this->application = $application;
        $this->status = $status;
        $this->adminNotes = $adminNotes;
        $this->paymentLink = $paymentLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'accepted'
            ? "Certification Application Approved - Action Required [{$this->application->reference_number}]"
            : "Certification Application Status Update [{$this->application->reference_number}]";

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
            view: 'mails.certification_status',
            with: [
                'application' => $this->application,
                'status'      => $this->status,
                'adminNotes'  => $this->adminNotes,
                'paymentLink' => $this->paymentLink,
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
