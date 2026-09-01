<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AdvisoryRequest;

class AdvisoryPaymentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public AdvisoryRequest $advisoryRequest;
    public float $amount;
    public string $paymentLink;
    public ?string $paymentDescription;

    /**
     * Create a new message instance.
     */
    public function __construct(
        AdvisoryRequest $advisoryRequest,
        float $amount,
        string $paymentLink,
        ?string $paymentDescription = null
    ) {
        $this->advisoryRequest = $advisoryRequest;
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
        $subject = "Action Required: Payment Invoice for Advisory Consultation [{$this->advisoryRequest->reference_number}] ({$formattedAmount})";

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
            view: 'mails.advisory_payment_link',
            with: [
                'advisoryRequest'    => $this->advisoryRequest,
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
