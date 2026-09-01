<?php

namespace App\Services;

use App\Models\Catalogue;
use App\Models\CatalogueExam;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserExamOverride;
use App\Models\UserExamResult;
use App\Models\UserVideoProgress;
use Carbon\Carbon;

/**
 * Single source of truth for "may this user sit / be credited for this exam?".
 *
 * The same rules used to be re-implemented in four places (purchased catalogue
 * listing, exam attempts, local exam details, local exam submit) and were absent
 * altogether from the ClassMarker result webhook - which is how a certification
 * could be awarded without the exam ever being sat legitimately. Every caller now
 * goes through this service so the rules cannot drift apart again.
 */
class ExamEligibilityService
{
    /**
     * Does the user own the catalogue (bought it, or holds a paid membership that
     * covers a "members only" catalogue)?
     */
    public function hasCatalogueAccess(User $user, Catalogue $catalogue): bool
    {
        if ($user->active_paid_membership && $catalogue->catalogue_type === 'members only') {
            return true;
        }

        return Purchase::where('user_id', $user->id)
            ->where('purchase_type', 'catalogue')
            ->where('catalogue_id', $catalogue->id)
            ->where('payment_status', 'paid')
            ->exists();
    }

    /**
     * Has the user completed the coursework that must precede the exam?
     *
     * A catalogue's videos and video links are the required coursework. This was
     * previously only checked in the browser, so a candidate could open
     * /dashboard/exams/{id} directly - or POST the submit endpoint - and take the
     * exam without doing the course at all.
     */
    public function hasCompletedCoursework(User $user, Catalogue $catalogue): bool
    {
        $videoIds     = $catalogue->videos()->pluck('id');
        $videoLinkIds = $catalogue->videoLinks()->pluck('id');

        if ($videoIds->isEmpty() && $videoLinkIds->isEmpty()) {
            return true;
        }

        if ($videoIds->isNotEmpty()) {
            $completed = UserVideoProgress::where('user_id', $user->id)
                ->whereIn('video_id', $videoIds)
                ->where('is_completed', true)
                ->distinct()
                ->count('video_id');

            if ($completed < $videoIds->count()) {
                return false;
            }
        }

        if ($videoLinkIds->isNotEmpty()) {
            $completed = UserVideoProgress::where('user_id', $user->id)
                ->whereIn('video_link_id', $videoLinkIds)
                ->where('is_completed', true)
                ->distinct()
                ->count('video_link_id');

            if ($completed < $videoLinkIds->count()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Attempt/cooldown status for a user on an exam.
     *
     * @return array{
     *     max_attempts:int,
     *     attempts_count:int,
     *     attempts_exceeded:bool,
     *     retake_locked:bool,
     *     retake_eligible_date:?string,
     *     latest_result:?UserExamResult,
     *     already_passed:bool
     * }
     */
    public function attemptStatus(User $user, CatalogueExam $exam, ?Catalogue $catalogue = null): array
    {
        $catalogue ??= $exam->catalogue;
        $isCertification = $catalogue && $catalogue->service_type === 'Certification';

        $latestPurchase = Purchase::where('user_id', $user->id)
            ->where('catalogue_id', $exam->catalogue_id)
            ->where('purchase_type', 'catalogue')
            ->where('payment_status', 'paid')
            ->latest('id')
            ->first();

        $resultsQuery = UserExamResult::where('user_id', $user->id)
            ->where('catalogue_exam_id', $exam->id);

        if ($latestPurchase) {
            $resultsQuery->where('created_at', '>=', $latestPurchase->created_at);
        }

        $totalAttemptsCount = (clone $resultsQuery)->count();
        $latestResult       = (clone $resultsQuery)->latest('id')->first();

        $override = UserExamOverride::where('user_id', $user->id)
            ->where('catalogue_exam_id', $exam->id)
            ->first();

        $maxAttempts = $isCertification ? 1 : $user->getMaxExamAttempts();
        if ($override && $override->max_attempts !== null) {
            $maxAttempts = $override->max_attempts;
        }

        // 3-month wait after failing a certification exam.
        $retakeLocked       = false;
        $retakeEligibleDate = null;

        if ($isCertification && $latestResult && $latestResult->status === 'failed') {
            if ($override && $override->ignore_cooldown) {
                $retakeLocked = false;
            } elseif ($override && $override->retake_eligible_date) {
                $eligibleDate = Carbon::parse($override->retake_eligible_date);
                if (now()->lt($eligibleDate)) {
                    $retakeLocked       = true;
                    $retakeEligibleDate = $eligibleDate->toDateString();
                }
            } else {
                $eligibleDate = Carbon::parse($latestResult->created_at)->copy()->addMonths(3);
                if (now()->lt($eligibleDate)) {
                    $retakeLocked       = true;
                    $retakeEligibleDate = $eligibleDate->toDateString();
                }
            }
        }

        if ($isCertification) {
            if ($override && $override->max_attempts !== null) {
                $attemptsCount = $totalAttemptsCount;
                if ($latestResult && $latestResult->status === 'passed') {
                    $attemptsExceeded = true;
                } elseif ($retakeLocked) {
                    $attemptsExceeded = true;
                } else {
                    $attemptsExceeded = $attemptsCount >= $maxAttempts;
                }
            } elseif (!$latestResult) {
                $attemptsCount    = 0;
                $attemptsExceeded = false;
            } elseif ($latestResult->status === 'passed') {
                $attemptsCount    = 1;
                $attemptsExceeded = true;
            } elseif ($retakeLocked) {
                $attemptsCount    = 1;
                $attemptsExceeded = true;
            } else {
                $attemptsCount    = 0;
                $attemptsExceeded = false;
            }
        } else {
            $attemptsCount    = $totalAttemptsCount;
            $attemptsExceeded = $attemptsCount >= $maxAttempts;
        }

        return [
            'max_attempts'         => (int) $maxAttempts,
            'attempts_count'       => (int) $attemptsCount,
            'attempts_exceeded'    => (bool) $attemptsExceeded,
            'retake_locked'        => (bool) $retakeLocked,
            'retake_eligible_date' => $retakeEligibleDate,
            'latest_result'        => $latestResult,
            'already_passed'       => (bool) ($isCertification && $latestResult && $latestResult->status === 'passed'),
            'override'             => $override,
        ];
    }

    /**
     * Full authorisation check for sitting (or being credited for) an exam.
     *
     * @return array{0:bool,1:?string,2:int} [allowed, reason, httpStatus]
     */
    public function check(User $user, CatalogueExam $exam, ?Catalogue $catalogue = null): array
    {
        $catalogue ??= $exam->catalogue;

        if (!$catalogue) {
            return [false, 'Catalogue not found.', 404];
        }

        if (!$this->hasCatalogueAccess($user, $catalogue)) {
            return [false, 'You do not have access to this exam.', 403];
        }

        if (!$this->hasCompletedCoursework($user, $catalogue)) {
            return [false, 'You must complete all course videos before taking this exam.', 403];
        }

        $status          = $this->attemptStatus($user, $exam, $catalogue);
        $isCertification = $catalogue->service_type === 'Certification';
        $override        = $status['override'];

        if ($isCertification) {
            if ($status['already_passed']) {
                return [false, 'You have already passed this certification exam.', 403];
            }

            if ($status['retake_locked']) {
                if ($override && $override->retake_eligible_date) {
                    return [false, 'You must wait until ' . $override->retake_eligible_date . ' after failing a certification exam to try again.', 403];
                }

                return [false, 'You must wait 3 months after failing a certification exam to try again.', 403];
            }

            if ($override && $override->max_attempts !== null && $status['attempts_exceeded']) {
                return [false, 'You have exceeded the maximum allowed attempts (' . $status['max_attempts'] . ') for this exam.', 403];
            }

            return [true, null, 200];
        }

        if ($status['attempts_exceeded']) {
            return [false, 'You have exceeded the maximum allowed attempts for this exam.', 403];
        }

        return [true, null, 200];
    }
}
