<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminFormSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $requestType;
    public string $referenceNumber;
    public string $submissionDateTime;
    public array $clientInfo;
    public array $completeData;
    public string $adminUrl;
    public array $uploadedAttachments;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $requestType,
        string $referenceNumber,
        string $submissionDateTime,
        array $clientInfo,
        array $completeData,
        string $adminUrl,
        array $uploadedAttachments = []
    ) {
        $this->requestType = $requestType;
        $this->referenceNumber = $referenceNumber;
        $this->submissionDateTime = $submissionDateTime;
        $this->clientInfo = $clientInfo;
        $this->completeData = $completeData;
        $this->adminUrl = $adminUrl;
        $this->uploadedAttachments = $uploadedAttachments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: "[NEW SUBMISSION] " . $this->requestType . " - Ref: " . $this->referenceNumber,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.admin_form_submission',
            with: [
                'requestType'        => $this->requestType,
                'referenceNumber'    => $this->referenceNumber,
                'submissionDateTime' => $this->submissionDateTime,
                'clientInfo'         => $this->clientInfo,
                'completeData'       => $this->completeData,
                'adminUrl'           => $this->adminUrl,
                'uploadedAttachments' => $this->uploadedAttachments,
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
        return [];
    }
}
