<?php

namespace App\Traits;

use App\Models\EmailLog;

trait LogsEmails
{
    /**
     * Log a sent email for audit purposes.
     *
     * @param int|null $userId
     * @param string $recipientEmail
     * @param string $recipientRole
     * @param string $subject
     * @param string $stage
     * @param mixed|null $model
     * @param string|null $bodySnippet
     * @return EmailLog
     */
    public function logEmail(
        ?int $userId,
        string $recipientEmail,
        string $recipientRole,
        string $subject,
        string $stage,
        $model = null,
        ?string $bodySnippet = null
    ): EmailLog {
        return EmailLog::create([
            'user_id'         => $userId,
            'recipient_email' => $recipientEmail,
            'recipient_role'  => $recipientRole,
            'subject'         => $subject,
            'stage'           => $stage,
            'model_type'      => $model ? get_class($model) : null,
            'model_id'        => $model ? $model->id : null,
            'body_snippet'    => $bodySnippet,
        ]);
    }
}
