<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContactMessage $contactMessage;
    public string $replySubject;
    public string $replyMessage;

    public function __construct(ContactMessage $contactMessage, string $replySubject, string $replyMessage)
    {
        $this->contactMessage = $contactMessage;
        $this->replySubject = $replySubject;
        $this->replyMessage = $replyMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            subject: $this->replySubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.contact_message_reply_mail',
            with: [
                'contactMessage' => $this->contactMessage,
                'replySubject'   => $this->replySubject,
                'replyMessage'   => $this->replyMessage,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
