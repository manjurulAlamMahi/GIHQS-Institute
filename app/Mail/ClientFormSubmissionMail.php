<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientFormSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public string $requestType;
    public string $referenceNumber;
    public string $submissionDate;
    public array $summaryData;
    public string $nextSteps;
    public array $supportContact;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $clientName,
        string $requestType,
        string $referenceNumber,
        string $submissionDate,
        array $summaryData,
        string $nextSteps,
        array $supportContact
    ) {
        $this->clientName = $clientName;
        $this->requestType = $requestType;
        $this->referenceNumber = $referenceNumber;
        $this->submissionDate = $submissionDate;
        $this->summaryData = $summaryData;
        $this->nextSteps = $nextSteps;
        $this->supportContact = $supportContact;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: "Submission Received Successfully - " . $this->requestType . " [" . $this->referenceNumber . "]",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.client_form_submission',
            with: [
                'clientName'      => $this->clientName,
                'requestType'     => $this->requestType,
                'referenceNumber' => $this->referenceNumber,
                'submissionDate'  => $this->submissionDate,
                'summaryData'     => $this->summaryData,
                'nextSteps'       => $this->nextSteps,
                'supportContact'  => $this->supportContact,
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
