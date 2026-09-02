<?php

use App\Models\Catalogue;
use App\Models\CatalogueExam;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserExamResult;
use App\Support\ExamLinkSigner;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    config()->set('services.classmarker.webhook_secret', 'cert-test-secret');
    Mail::fake();
});

function securedCertification(): Catalogue
{
    return Catalogue::create([
        'title'          => 'Guarded Certification',
        'short_title'    => 'GC',
        'price_regular'  => 500.00,
        'service_type'   => 'Certification',
        'status'         => 1,
        'validity_years' => 3,
    ]);
}

function securedExam(Catalogue $catalogue): CatalogueExam
{
    return CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title'   => 'Certification Exam',
        'exam_link'    => 'https://www.classmarker.com/online-test/start/?quiz=guarded',
        'pass_mark'    => 70,
    ]);
}

function buyer(Catalogue $catalogue): User
{
    $user = User::factory()->create();

    Purchase::create([
        'user_id'        => $user->id,
        'purchase_type'  => 'catalogue',
        'catalogue_id'   => $catalogue->id,
        'amount'         => 500.00,
        'payment_status' => 'paid',
    ]);

    return $user;
}

function postClassmarker(array $payload)
{
    $raw = json_encode($payload);

    return test()->call('POST', '/api/classmarker/webhook', [], [], [], [
        'CONTENT_TYPE'                   => 'application/json',
        'HTTP_X_CLASSMARKER_HMAC_SHA256' => base64_encode(
            hash_hmac('sha256', $raw, config('services.classmarker.webhook_secret'), true)
        ),
    ], $raw);
}

function passPayload(string $cmUserId): array
{
    return [
        'payload_status' => 'live',
        'result' => [
            'cm_user_id'          => $cmUserId,
            'link_result_id'      => 'r' . uniqid(),
            'points_scored'       => 10,
            'points_available'    => 10,
            'percentage'          => 100,
            'percentage_passmark' => 70,
        ],
    ];
}

/*
|--------------------------------------------------------------------------
| Can an unauthorised user GENERATE another user's certificate?
|--------------------------------------------------------------------------
*/

test('a forged webhook cannot mint a certificate for someone else', function () {
    $catalogue = securedCertification();
    $exam      = securedExam($catalogue);
    $victim    = buyer($catalogue);

    // An attacker naming the victim, without our signature.
    postClassmarker(passPayload("{$victim->id}_{$exam->id}"));

    expect(UserExamResult::count())->toBe(0);
});

test('a signed identifier for one user cannot be pointed at another', function () {
    $catalogue = securedCertification();
    $exam      = securedExam($catalogue);
    $attacker  = buyer($catalogue);
    $victim    = buyer($catalogue);

    // The attacker holds a legitimate signature for themselves and swaps the id.
    $signed  = ExamLinkSigner::sign($attacker->id, $exam->id);
    $tampered = preg_replace('/^\d+/', (string) $victim->id, $signed);

    postClassmarker(passPayload($tampered));

    expect(UserExamResult::where('user_id', $victim->id)->count())->toBe(0);
});

test('a webhook with a bad signature mints nothing', function () {
    $catalogue = securedCertification();
    $exam      = securedExam($catalogue);
    $user      = buyer($catalogue);

    $payload = passPayload(ExamLinkSigner::sign($user->id, $exam->id));
    $raw     = json_encode($payload);

    $this->call('POST', '/api/classmarker/webhook', [], [], [], [
        'CONTENT_TYPE'                   => 'application/json',
        'HTTP_X_CLASSMARKER_HMAC_SHA256' => base64_encode(hash_hmac('sha256', $raw, 'wrong-secret', true)),
    ], $raw)->assertStatus(403);

    expect(UserExamResult::count())->toBe(0);
});

test('a user cannot submit a local exam as another user', function () {
    $catalogue = Catalogue::create([
        'title' => 'Local Course', 'short_title' => 'LOC',
        'price_regular' => 10.00, 'service_type' => 'Course', 'status' => 1,
    ]);

    $localExam = \App\Models\Exam::create(['name' => 'Local', 'status' => 'published']);
    $question  = \App\Models\ExamQuestion::create([
        'exam_id' => $localExam->id, 'question_text' => 'Q', 'sort_order' => 1,
    ]);
    $correct = \App\Models\ExamOption::create([
        'exam_question_id' => $question->id, 'option_text' => 'A', 'is_correct' => true, 'sort_order' => 1,
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id, 'exam_title' => 'Final',
        'exam_id' => $localExam->id, 'pass_mark' => 50,
    ]);

    $stranger = User::factory()->create();

    // No purchase, so no submission is possible regardless of payload.
    $this->actingAs($stranger, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [['question_id' => $question->id, 'option_id' => $correct->id]],
        ])
        ->assertStatus(403);

    expect(UserExamResult::count())->toBe(0);
});

/*
|--------------------------------------------------------------------------
| Can an unauthorised user ACCESS another user's certificate?
|--------------------------------------------------------------------------
*/

test('one users certificate never appears in another users course data', function () {
    $catalogue = securedCertification();
    $exam      = securedExam($catalogue);
    $holder    = buyer($catalogue);
    $other     = buyer($catalogue);

    UserExamResult::create([
        'user_id'                   => $holder->id,
        'catalogue_exam_id'         => $exam->id,
        'percentage'                => 95,
        'status'                    => 'passed',
        'certificate_serial_number' => 'GIHQS-2026-000777-AAAAAA',
        'certificate_url'           => 'https://example.test/uploads/certificates/holder.pdf',
        'download_certificate'      => 'https://example.test/uploads/certificates/holder.pdf',
    ]);

    $body = $this->actingAs($other, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $catalogue->id)
        ->getContent();

    expect($body)->not->toContain('holder.pdf')
        ->and($body)->not->toContain('GIHQS-2026-000777-AAAAAA');
});

test('exam attempts only ever return the callers own results', function () {
    $catalogue = securedCertification();
    $exam      = securedExam($catalogue);
    $holder    = buyer($catalogue);
    $other     = buyer($catalogue);

    UserExamResult::create([
        'user_id'                   => $holder->id,
        'catalogue_exam_id'         => $exam->id,
        'percentage'                => 95,
        'status'                    => 'passed',
        'certificate_serial_number' => 'GIHQS-2026-000888-BBBBBB',
    ]);

    $this->actingAs($other, 'api')
        ->getJson("/api/profile/exams/{$exam->id}/attempts")
        ->assertStatus(200)
        ->assertJsonPath('data.attempts', []);
});

test('certificate serials cannot be walked sequentially', function () {
    $catalogue = securedCertification();
    $exam      = securedExam($catalogue);
    $holder    = buyer($catalogue);

    UserExamResult::create([
        'user_id'                   => $holder->id,
        'catalogue_exam_id'         => $exam->id,
        'percentage'                => 95,
        'status'                    => 'passed',
        'certificate_serial_number' => 'GIHQS-2026-000001-C4F9A1',
    ]);

    // The old, guessable form no longer resolves.
    $this->getJson('/api/certificates/verify/GIHQS-2026-000001')->assertStatus(404);

    // The real serial still does.
    $this->getJson('/api/certificates/verify/GIHQS-2026-000001-C4F9A1')->assertStatus(200);
});

test('a generated serial carries random entropy', function () {
    $catalogue = securedCertification();
    $exam      = securedExam($catalogue);
    $holder    = buyer($catalogue);

    $serials = collect(range(1, 3))->map(function () use ($holder, $exam, $catalogue) {
        $result = UserExamResult::create([
            'user_id'           => $holder->id,
            'catalogue_exam_id' => $exam->id,
            'percentage'        => 95,
            'status'            => 'passed',
        ]);

        (new class {
            use \App\Traits\GeneratesCertificates;
            public function make($r, $c, $u)
            {
                $this->generateLocalCertificate($r, $c, $u);
            }
        })->make($result, $catalogue, $holder);

        return $result->fresh()->certificate_serial_number;
    });

    expect($serials->unique())->toHaveCount(3)
        ->and($serials->first())->toMatch('/^GIHQS-\d{4}-\d{6}-[0-9A-F]{6}$/');

    foreach (glob(public_path('uploads/certificates/certificate_*.pdf')) as $file) {
        @unlink($file);
    }
});
