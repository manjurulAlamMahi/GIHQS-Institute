<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;

/**
 * Signs and verifies the identifier that travels with an external (ClassMarker)
 * exam link and comes back to us through the result webhook.
 *
 * The identifier used to be a bare "{userId}_{examId}". It is placed in the exam
 * URL as a query parameter, which means the candidate can see and edit it before
 * sitting the test, and the webhook trusted whatever came back. That allowed a
 * result to be attributed to any user and, worse, to any exam - including a paid
 * certification the candidate never bought or sat.
 *
 * The identifier is now "{userId}_{examId}_{hmac}" so tampering is detectable.
 */
class ExamLinkSigner
{
    /**
     * Length of the truncated signature. ClassMarker caps cm_user_id length, so
     * the identifier is kept short; 20 base32-ish characters (~100 bits) is far
     * beyond what an online forgery attempt could search.
     */
    private const SIGNATURE_LENGTH = 20;

    /**
     * Build the signed identifier for an exam link.
     */
    public static function sign(int $userId, int $examId): string
    {
        return $userId . '_' . $examId . '_' . self::signature($userId, $examId);
    }

    /**
     * Verify a signed identifier and return [userId, examId], or null when the
     * identifier is missing, malformed, unsigned or carries a bad signature.
     *
     * @return array{0:int,1:int}|null
     */
    public static function parse(?string $identifier): ?array
    {
        if (!is_string($identifier) || $identifier === '') {
            return null;
        }

        $parts = explode('_', $identifier);
        if (count($parts) !== 3) {
            return null;
        }

        [$rawUserId, $rawExamId, $providedSignature] = $parts;

        if (!ctype_digit($rawUserId) || !ctype_digit($rawExamId)) {
            return null;
        }

        $userId = (int) $rawUserId;
        $examId = (int) $rawExamId;

        if ($userId <= 0 || $examId <= 0) {
            return null;
        }

        if (!hash_equals(self::signature($userId, $examId), $providedSignature)) {
            return null;
        }

        return [$userId, $examId];
    }

    /**
     * Deterministic signature for a (user, exam) pair.
     */
    private static function signature(int $userId, int $examId): string
    {
        $key = (string) Config::get('app.key');

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7)) ?: $key;
        }

        $digest = hash_hmac('sha256', 'exam-link|' . $userId . '|' . $examId, $key, true);

        return substr(
            rtrim(strtr(base64_encode($digest), '+/', '-_'), '='),
            0,
            self::SIGNATURE_LENGTH
        );
    }
}
