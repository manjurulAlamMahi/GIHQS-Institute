<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\CatalogueExam;
use App\Models\UserExamResult;

class ExamResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public CatalogueExam $exam;
    public UserExamResult $result;
    public ?string $actionLink;

    /**
     * Create a new message instance.
     */
    public function __construct(
        User $user,
        CatalogueExam $exam,
        UserExamResult $result,
        ?string $actionLink = null
    ) {
        $this->user = $user;
        $this->exam = $exam;
        $this->result = $result;
        $this->actionLink = $actionLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusStr = $this->result->status === 'passed' ? 'PASSED' : 'FAILED';
        $subject = "Exam Result: {$this->exam->exam_title} - {$statusStr}";

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
            view: 'mails.exam_result',
            with: [
                'user'       => $this->user,
                'exam'       => $this->exam,
                'result'     => $this->result,
                'actionLink' => $this->actionLink,
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
