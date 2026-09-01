<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserExamResult;
use App\Models\CatalogueExam;
use App\Models\User;
use App\Services\ExamEligibilityService;
use App\Support\ExamLinkSigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExamResultMail;
use App\Traits\LogsEmails;
use App\Traits\GeneratesCertificates;

class ClassmarkerWebhookController extends Controller
{
    use LogsEmails, GeneratesCertificates;

    public function __construct(private ExamEligibilityService $eligibility)
    {
    }

    /**
     * Handle incoming Webhook from ClassMarker.
     */
    public function handleWebhook(Request $request)
    {
        $rawPayload = $request->getContent();
        $receivedSignature = (string) $request->header('X-Classmarker-Hmac-Sha256', '');
        $secret = config('services.classmarker.webhook_secret');

        if (!$secret) {
            Log::error('ClassMarker Webhook Error: CLASSMARKER_WEBHOOK_SECRET is not configured.');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        // Verify HMAC-SHA256 signature
        $computedSignature = base64_encode(hash_hmac('sha256', $rawPayload, $secret, true));

        if ($receivedSignature === '' || !hash_equals($computedSignature, $receivedSignature)) {
            Log::warning('ClassMarker Webhook Warning: HMAC signature mismatch - request rejected.');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $payload = json_decode($rawPayload, true);

        if (!$payload) {
            Log::error('ClassMarker Webhook Error: Failed to parse JSON payload.');
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        Log::info('ClassMarker Webhook Received:', $payload);

        $payloadStatus = $payload['payload_status'] ?? 'live';

        // Verification payload from ClassMarker
        if ($payloadStatus === 'verify') {
            return response()->json(['status' => 'verified'], 200);
        }

        // Process live results
        if (isset($payload['result'])) {
            $result = $payload['result'];
            $cmUserId = $result['cm_user_id'] ?? $result['user_id'] ?? null;

            if (!$cmUserId) {
                Log::warning('ClassMarker Webhook Warning: Missing cm_user_id in result payload.');
                return response()->json(['error' => 'Missing cm_user_id'], 400);
            }

            // cm_user_id travels through a URL the candidate can see and edit, so it
            // is only trusted when it still carries our signature. Without this a
            // result could be re-pointed at any user or any exam - including a paid
            // certification that was never bought or sat.
            $identity = ExamLinkSigner::parse((string) $cmUserId);

            if ($identity === null) {
                Log::warning('ClassMarker Webhook Warning: Rejected unsigned or tampered cm_user_id.', [
                    'cm_user_id' => $cmUserId,
                ]);
                return response()->json(['error' => 'Invalid user_id format'], 403);
            }

            [$userId, $examId] = $identity;

            // Verify if user and exam exist
            $dbUser = User::find($userId);
            $dbExam = CatalogueExam::find($examId);

            if (!$dbUser || !$dbExam) {
                Log::warning('ClassMarker Webhook Warning: User or Exam not found.', [
                    'user_id' => $userId,
                    'exam_id' => $examId,
                    'user_exists' => (bool)$dbUser,
                    'exam_exists' => (bool)$dbExam
                ]);
                return response()->json(['error' => 'User or Exam not found'], 404);
            }

            // The signature proves the identifier was issued by us; it does not prove
            // the candidate is still entitled to a result. Re-run the same
            // entitlement, coursework and attempt rules the API enforces, so a stale
            // or replayed link cannot buy an extra attempt or a free certification.
            [$allowed, $reason] = $this->eligibility->check($dbUser, $dbExam);

            if (!$allowed) {
                Log::warning('ClassMarker Webhook Warning: Result rejected - user not eligible.', [
                    'user_id' => $userId,
                    'exam_id' => $examId,
                    'reason'  => $reason,
                ]);
                return response()->json(['error' => 'Not eligible for this exam'], 403);
            }

            // Guard against a result from one ClassMarker test being credited to a
            // different exam in our catalogue.
            if (!$this->resultMatchesExam($payload, $dbExam)) {
                Log::warning('ClassMarker Webhook Warning: Result does not belong to the referenced exam.', [
                    'user_id' => $userId,
                    'exam_id' => $examId,
                ]);
                return response()->json(['error' => 'Result does not match the referenced exam'], 403);
            }

            $score = isset($result['points_scored']) ? (float)$result['points_scored'] : null;
            $pointsAvailable = isset($result['points_available']) ? (float)$result['points_available'] : null;
            $percentage = isset($result['percentage']) ? (float)$result['percentage'] : null;
            $percentagePassmark = isset($result['percentage_passmark']) ? (float)$result['percentage_passmark'] : null;

            // Custom pass mark logic from DB (fallback to ClassMarker's webhook passmark if not configured in our DB)
            $passMark = ($dbExam->pass_mark !== null) ? (float)$dbExam->pass_mark : $percentagePassmark;

            if ($percentage !== null && $passMark !== null) {
                $status = ($percentage >= $passMark) ? 'passed' : 'failed';
            } else {
                if (isset($result['passed'])) {
                    $status = $result['passed'] ? 'passed' : 'failed';
                } else {
                    $status = $result['status'] ?? 'failed';
                }
            }

            $duration = $result['duration'] ?? null;
            $ipAddress = $result['ip_address'] ?? null;
            $startTime = isset($result['time_started']) ? \Illuminate\Support\Carbon::createFromTimestamp((int)$result['time_started']) : null;
            $endTime = isset($result['time_finished']) ? \Illuminate\Support\Carbon::createFromTimestamp((int)$result['time_finished']) : null;
            $resultId = $result['link_result_id'] ?? $result['result_id'] ?? null;

            $certificateSerial = $result['certificate_serial'] ?? null;
            $certificateUrl = $result['certificate_url'] ?? null;
            $viewResultsUrl = $result['view_results_url'] ?? null;
            $categories = $result['categories'] ?? null;

            // If the status is failed based on our backend passmark, drop the
            // certificate ClassMarker issued under its own (lower) pass mark.
            if ($status === 'failed') {
                $certificateSerial = null;
                $certificateUrl = null;
                unset($result['certificate_serial'], $result['certificate_url']);
                $payload['result'] = $result;
            }

            // Save or update result
            $examResult = UserExamResult::updateOrCreate(
                [
                    'classmarker_result_id' => $resultId,
                ],
                [
                    'user_id'                    => $userId,
                    'catalogue_exam_id'          => $examId,
                    'score'                      => $score,
                    'points_available'           => $pointsAvailable,
                    'percentage'                 => $percentage,
                    'percentage_passmark'        => $passMark,
                    'status'                     => $status,
                    'duration'                   => $duration,
                    'ip_address'                 => $ipAddress,
                    'start_time'                 => $startTime,
                    'end_time'                   => $endTime,
                    'certificate_serial_number'  => $certificateSerial,
                    'certificate_url'            => $certificateUrl,
                    'download_certificate'       => $certificateUrl,
                    'view_results_url'           => $viewResultsUrl,
                    'raw_payload'                => $payload,
                    'category_results'           => $categories,
                ]
            );

            // If the exam is passed, generate a local certificate and save it
            if ($examResult->status === 'passed') {
                $catalogue = $dbExam->catalogue ?? null;
                if ($catalogue) {
                    $this->generateLocalCertificate($examResult, $catalogue, $dbUser);
                    $examResult->refresh();
                }
            }

            if ($dbUser && $dbExam) {
                try {
                    $actionLink = $examResult->status === 'passed'
                        ? ($examResult->certificate_url ?: (env('FRONTEND_URL', 'https://gihqs.vercel.app') . '/dashboard'))
                        : (env('FRONTEND_URL', 'https://gihqs.vercel.app') . '/dashboard');

                    $mail = new ExamResultMail($dbUser, $dbExam, $examResult, $actionLink);
                    Mail::to($dbUser->email)->send($mail);

                    // Log email to database
                    $this->logEmail(
                        $dbUser->id,
                        $dbUser->email,
                        'user',
                        $mail->envelope()->subject,
                        'exam_' . $examResult->status,
                        $examResult
                    );
                } catch (\Throwable $e) {
                    Log::error('Failed to send exam result email: ' . $e->getMessage());
                }
            }

            Log::info('ClassMarker Webhook Success: Exam result stored/updated successfully.', [
                'user_id' => $userId,
                'exam_id' => $examId,
                'result_id' => $resultId
            ]);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Check the ClassMarker test the result came from is the one we linked to.
     *
     * ClassMarker identifies the test by the "quiz" parameter of the link URL. If
     * neither side carries an identifier we can compare, the result is accepted -
     * the signature and eligibility checks above already stand between the
     * candidate and an unearned certificate.
     */
    private function resultMatchesExam(array $payload, CatalogueExam $exam): bool
    {
        $expected = $this->quizIdentifier($exam->exam_link);

        if ($expected === null) {
            return true;
        }

        $candidateUrls = array_filter([
            $payload['link']['link_url'] ?? null,
            $payload['link_url'] ?? null,
            $payload['result']['link_url'] ?? null,
        ]);

        if (empty($candidateUrls)) {
            return true;
        }

        foreach ($candidateUrls as $url) {
            if ($this->quizIdentifier($url) === $expected) {
                return true;
            }
        }

        return false;
    }

    /**
     * Pull the ClassMarker "quiz" identifier out of an exam link.
     */
    private function quizIdentifier(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $query = parse_url($url, PHP_URL_QUERY);
        if (!$query) {
            return null;
        }

        parse_str($query, $params);

        $quiz = $params['quiz'] ?? null;

        return is_string($quiz) && $quiz !== '' ? $quiz : null;
    }
}
