<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AccreditationApplication;

class AccreditationPaymentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public AccreditationApplication $application;
    public float $amount;
    public string $paymentLink;
    public ?string $paymentDescription;

    /**
     * Create a new message instance.
     */
    public function __construct(
        AccreditationApplication $application,
        float $amount,
        string $paymentLink,
        ?string $paymentDescription = null
    ) {
        $this->application = $application;
        $this->amount = $amount;
        $this->paymentLink = $paymentLink;
        $this->paymentDescription = $paymentDescription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $formattedAmount = '$' . number_format($this->amount, 2);
        $subject = "Action Required: Payment Invoice for Accreditation Program [{$this->application->reference_number}] ({$formattedAmount})";

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
            view: 'mails.accreditation_payment_link',
            with: [
                'application'        => $this->application,
                'amount'             => $this->amount,
                'paymentLink'        => $this->paymentLink,
                'paymentDescription' => $this->paymentDescription,
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
