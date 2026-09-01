<?php

use App\Models\Catalogue;
use App\Models\CatalogueExam;
use App\Models\CatalogueVideo;
use App\Models\Exam;
use App\Models\ExamOption;
use App\Models\ExamQuestion;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserExamResult;
use App\Models\UserVideoProgress;
use Illuminate\Support\Facades\Mail;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function makeCertification(array $overrides = []): Catalogue
{
    return Catalogue::create(array_merge([
        'title'          => 'Certified Healthcare Quality Professional',
        'short_title'    => 'CHQP',
        'price_regular'  => 500.00,
        'service_type'   => 'Certification',
        'status'         => 1,
        'validity_years' => 3,
    ], $overrides));
}

function makeCourse(array $overrides = []): Catalogue
{
    return Catalogue::create(array_merge([
        'title'         => 'Intro Course',
        'short_title'   => 'IC',
        'price_regular' => 25.00,
        'service_type'  => 'Course',
        'status'        => 1,
    ], $overrides));
}

function makeLocalExam(Catalogue $catalogue, float $passMark = 50.0): array
{
    $localExam = Exam::create(['name' => 'Local Exam', 'status' => 'published']);

    $question = ExamQuestion::create([
        'exam_id'       => $localExam->id,
        'question_text' => 'What is 2+2?',
        'sort_order'    => 1,
    ]);

    $correct = ExamOption::create([
        'exam_question_id' => $question->id,
        'option_text'      => '4',
        'is_correct'       => true,
        'sort_order'       => 1,
    ]);

    ExamOption::create([
        'exam_question_id' => $question->id,
        'option_text'      => '5',
        'is_correct'       => false,
        'sort_order'       => 2,
    ]);

    $catalogueExam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title'   => 'Final Exam',
        'exam_id'      => $localExam->id,
        'pass_mark'    => $passMark,
        'is_premium'   => false,
    ]);

    return [$catalogueExam, $question, $correct];
}

function buy(User $user, Catalogue $catalogue): Purchase
{
    return Purchase::create([
        'user_id'        => $user->id,
        'purchase_type'  => 'catalogue',
        'catalogue_id'   => $catalogue->id,
        'amount'         => $catalogue->price_regular,
        'payment_status' => 'paid',
    ]);
}

function classmarkerPost(array $payload, ?string $signature = null)
{
    $raw    = json_encode($payload);
    $secret = config('services.classmarker.webhook_secret');

    return test()->call(
        'POST',
        '/api/classmarker/webhook',
        [],
        [],
        [],
        [
            'CONTENT_TYPE'                   => 'application/json',
            'HTTP_X_CLASSMARKER_HMAC_SHA256' => $signature ?? base64_encode(hash_hmac('sha256', $raw, $secret, true)),
        ],
        $raw
    );
}

function classmarkerResultPayload(string $cmUserId, float $percentage = 100.0, array $extra = []): array
{
    return [
        'payload_status' => 'live',
        'result'         => array_merge([
            'cm_user_id'          => $cmUserId,
            'link_result_id'      => 'res_' . uniqid(),
            'points_scored'       => 10,
            'points_available'    => 10,
            'percentage'          => $percentage,
            'percentage_passmark' => 50,
            'certificate_serial'  => 'CM-SERIAL-999',
            'certificate_url'     => 'https://www.classmarker.com/certificate/999/',
            'view_results_url'    => 'https://www.classmarker.com/results/999/',
        ], $extra),
    ];
}

beforeEach(function () {
    config()->set('services.classmarker.webhook_secret', 'test-webhook-secret');
    Mail::fake();
});

/*
|--------------------------------------------------------------------------
| 1. Certification exam bypass - ClassMarker result attribution
|--------------------------------------------------------------------------
*/

test('classmarker webhook rejects an unsigned cm_user_id', function () {
    $user        = User::factory()->create();
    $certificate = makeCertification();

    $exam = CatalogueExam::create([
        'catalogue_id' => $certificate->id,
        'exam_title'   => 'Certification Exam',
        'exam_link'    => 'https://www.classmarker.com/online-test/start/?quiz=cert123',
        'pass_mark'    => 70,
    ]);

    buy($user, $certificate);

    classmarkerPost(classmarkerResultPayload("{$user->id}_{$exam->id}"));

    expect(UserExamResult::count())->toBe(0);
});

test('classmarker webhook refuses to certify a user who never bought the certification', function () {
    $user        = User::factory()->create();
    $certificate = makeCertification();

    $exam = CatalogueExam::create([
        'catalogue_id' => $certificate->id,
        'exam_title'   => 'Certification Exam',
        'exam_link'    => 'https://www.classmarker.com/online-test/start/?quiz=cert123',
        'pass_mark'    => 70,
    ]);

    // A correctly signed identifier is still not enough without entitlement.
    $signed = App\Support\ExamLinkSigner::sign($user->id, $exam->id);

    classmarkerPost(classmarkerResultPayload($signed));

    expect(UserExamResult::count())->toBe(0);
});

test('classmarker webhook refuses a second attempt once a certification is already passed', function () {
    $user        = User::factory()->create();
    $certificate = makeCertification();

    $exam = CatalogueExam::create([
        'catalogue_id' => $certificate->id,
        'exam_title'   => 'Certification Exam',
        'exam_link'    => 'https://www.classmarker.com/online-test/start/?quiz=cert123',
        'pass_mark'    => 70,
    ]);

    buy($user, $certificate);

    UserExamResult::create([
        'user_id'           => $user->id,
        'catalogue_exam_id' => $exam->id,
        'percentage'        => 90,
        'status'            => 'passed',
    ]);

    $signed = App\Support\ExamLinkSigner::sign($user->id, $exam->id);
    classmarkerPost(classmarkerResultPayload($signed));

    expect(UserExamResult::where('user_id', $user->id)->count())->toBe(1);
});

test('a failed exam result never exposes certificate data from the raw payload', function () {
    $user        = User::factory()->create();
    $certificate = makeCertification();

    $exam = CatalogueExam::create([
        'catalogue_id' => $certificate->id,
        'exam_title'   => 'Certification Exam',
        'exam_link'    => 'https://www.classmarker.com/online-test/start/?quiz=cert123',
        'pass_mark'    => 70,
    ]);

    $result = UserExamResult::create([
        'user_id'           => $user->id,
        'catalogue_exam_id' => $exam->id,
        'percentage'        => 55,
        'status'            => 'failed',
        'raw_payload'       => [
            'result' => [
                'certificate_serial' => 'CM-SERIAL-999',
                'certificate_url'    => 'https://www.classmarker.com/certificate/999/',
            ],
        ],
    ]);

    expect($result->fresh()->certificate_serial_number)->toBeNull()
        ->and($result->fresh()->certificate_url)->toBeNull()
        ->and($result->fresh()->download_certificate)->toBeNull();
});

/*
|--------------------------------------------------------------------------
| 2. Local exam prerequisites must be enforced server-side
|--------------------------------------------------------------------------
*/

test('a local exam cannot be opened until the required videos are completed', function () {
    $user   = User::factory()->create();
    $course = makeCourse();

    CatalogueVideo::create([
        'catalogue_id' => $course->id,
        'video_title'  => 'Module 1',
        'video_file'   => 'uploads/videos/m1.mp4',
    ]);

    [$exam] = makeLocalExam($course);
    buy($user, $course);

    $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}")
        ->assertStatus(403);
});

test('a local exam cannot be submitted until the required videos are completed', function () {
    $user   = User::factory()->create();
    $course = makeCourse();

    CatalogueVideo::create([
        'catalogue_id' => $course->id,
        'video_title'  => 'Module 1',
        'video_file'   => 'uploads/videos/m1.mp4',
    ]);

    [$exam, $question, $correct] = makeLocalExam($course);
    buy($user, $course);

    $this->actingAs($user, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [['question_id' => $question->id, 'option_id' => $correct->id]],
        ])
        ->assertStatus(403);

    expect(UserExamResult::count())->toBe(0);
});

test('a local exam opens normally once the required videos are completed', function () {
    $user   = User::factory()->create();
    $course = makeCourse();

    $video = CatalogueVideo::create([
        'catalogue_id' => $course->id,
        'video_title'  => 'Module 1',
        'video_file'   => 'uploads/videos/m1.mp4',
    ]);

    [$exam] = makeLocalExam($course);
    buy($user, $course);

    UserVideoProgress::create([
        'user_id'      => $user->id,
        'video_id'     => $video->id,
        'is_completed' => true,
    ]);

    $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}")
        ->assertStatus(200);
});

test('authorization is checked before the exam type, so an unowned exam does not leak its shape', function () {
    $user        = User::factory()->create();
    $certificate = makeCertification();

    // A ClassMarker exam: no local exam_id. Reporting "this is not a local custom
    // exam" to a user with no entitlement discloses the exam's configuration.
    $exam = CatalogueExam::create([
        'catalogue_id' => $certificate->id,
        'exam_title'   => 'Certification Exam',
        'exam_link'    => 'https://www.classmarker.com/online-test/start/?quiz=cert123',
        'pass_mark'    => 70,
    ]);

    $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}")
        ->assertStatus(403);

    // Same for submit. Real question/option ids are borrowed from an unrelated
    // exam so the request clears validation and reaches the authorization check.
    [, $question, $option] = makeLocalExam(makeCourse(['short_title' => 'OTHER']));

    $this->actingAs($user, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [['question_id' => $question->id, 'option_id' => $option->id]],
        ])
        ->assertStatus(403);
});

test('a validation failure reports 422 rather than a server error', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'api')
        ->postJson('/api/profile/purchased-catalogues/videos/complete', [
            'video_id'     => 999999, // does not exist
            'is_completed' => true,
        ])
        ->assertStatus(422);
});

test('video progress cannot be recorded for a catalogue the user has not bought', function () {
    $user   = User::factory()->create();
    $course = makeCourse();

    $video = CatalogueVideo::create([
        'catalogue_id' => $course->id,
        'video_title'  => 'Module 1',
        'video_file'   => 'uploads/videos/m1.mp4',
    ]);

    $this->actingAs($user, 'api')
        ->postJson('/api/profile/purchased-catalogues/videos/complete', [
            'video_id'     => $video->id,
            'is_completed' => true,
        ])
        ->assertStatus(403);

    expect(UserVideoProgress::count())->toBe(0);
});

/*
|--------------------------------------------------------------------------
| 3. Paid learning material must not be reachable by direct link
|--------------------------------------------------------------------------
*/

test('public catalogue endpoints do not hand out paid course material', function () {
    $course = makeCourse([
        'details_file'     => 'uploads/catalogues/details.pdf',
        'story_guide_file' => 'uploads/catalogues/story.pdf',
        'module_file'      => 'uploads/catalogues/module.pdf',
    ]);

    $this->getJson("/api/catalogues/{$course->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.catalogue.module_file', null)
        ->assertJsonPath('data.catalogue.story_guide_file', null);

    $this->getJson("/api/catalogues/{$course->id}/details")
        ->assertStatus(200)
        ->assertJsonPath('data.catalogue.module_file', null)
        ->assertJsonPath('data.catalogue.story_guide_file', null);
});

test('a paying user still receives the paid course material', function () {
    $user   = User::factory()->create();
    $course = makeCourse([
        'story_guide_file' => 'uploads/catalogues/story.pdf',
        'module_file'      => 'uploads/catalogues/module.pdf',
    ]);

    buy($user, $course);

    $this->actingAs($user, 'api')
        ->getJson("/api/catalogues/{$course->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.catalogue.module_file', asset('uploads/catalogues/module.pdf'));
});

/*
|--------------------------------------------------------------------------
| 4. Certificate verification
|--------------------------------------------------------------------------
*/

test('certificate verification rejects a serial that belongs to a failed attempt', function () {
    $user        = User::factory()->create();
    $certificate = makeCertification();

    $exam = CatalogueExam::create([
        'catalogue_id' => $certificate->id,
        'exam_title'   => 'Certification Exam',
        'pass_mark'    => 70,
    ]);

    UserExamResult::create([
        'user_id'                   => $user->id,
        'catalogue_exam_id'         => $exam->id,
        'percentage'                => 40,
        'status'                    => 'failed',
        'certificate_serial_number' => 'GIHQS-2026-000123',
    ]);

    $this->getJson('/api/certificates/verify/GIHQS-2026-000123')
        ->assertStatus(404);
});

/*
|--------------------------------------------------------------------------
| 5. API authentication hardening
|--------------------------------------------------------------------------
*/

test('a jwt in the query string does not authenticate an api request', function () {
    $user  = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $this->getJson('/api/profile-info?token=' . $token)->assertStatus(401);
});

test('a jwt in the request body does not authenticate an api request', function () {
    $user  = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $this->postJson('/api/profile-update', ['token' => $token, 'first_name' => 'Mallory'])
        ->assertStatus(401);
});

test('login is rate limited', function () {
    User::factory()->create(['email' => 'victim@example.com']);

    $statuses = collect(range(1, 12))->map(function () {
        return $this->postJson('/api/login', [
            'email'    => 'victim@example.com',
            'password' => 'wrong-password-guess',
        ])->status();
    });

    expect($statuses->all())->toContain(429);
});

test('requesting a password otp does not lock an existing account out of login', function () {
    User::factory()->create([
        'email'        => 'victim@example.com',
        'password'     => bcrypt('correct-horse'),
        'status'       => 1,
        'otp_verified' => true,
    ]);

    $this->postJson('/api/password/send-otp', ['email' => 'victim@example.com'])->assertStatus(200);

    $this->postJson('/api/login', [
        'email'    => 'victim@example.com',
        'password' => 'correct-horse',
    ])->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| 5b. Webhook and public-lookup hardening
|--------------------------------------------------------------------------
*/

test('the stripe webhook refuses unsigned payloads when no secret is configured', function () {
    config()->set('services.stripe.webhook_secret', null);

    $this->postJson('/api/stripe/webhook', [
        'type' => 'checkout.session.completed',
        'data' => ['object' => ['id' => 'cs_forged', 'metadata' => ['type' => 'catalogue']]],
    ])->assertStatus(500);
});

test('the stripe webhook rejects a payload with a bad signature', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test');

    $this->withHeader('Stripe-Signature', 't=1,v1=deadbeef')
        ->postJson('/api/stripe/webhook', [
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => 'cs_forged']],
        ])->assertStatus(400);
});

test('accreditation verification cannot be walked by record id', function () {
    $application = \App\Models\AccreditationApplication::create([
        'applicant_category'       => 'University',
        'applicant_name'           => 'Private Applicant Ltd',
        'country'                  => 'USA',
        'city'                     => 'Boston',
        'program_name'             => 'Confidential Programme',
        'program_type'             => 'Medical Degree',
        'program_delivery_format'  => 'On Campus',
        'primary_contact_person'   => 'Dr Smith',
        'contact_title_position'   => 'Dean',
        'email_address'            => 'dean@example.edu',
        'payment_status'           => 'pending',
    ]);

    $this->getJson('/api/accreditation/verify/' . $application->id)
        ->assertStatus(404);

    // The printed verification code still resolves.
    $this->getJson('/api/accreditation/verify/' . $application->fresh()->verification_code)
        ->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| 6. Unauthenticated state changes
|--------------------------------------------------------------------------
*/

test('an anonymous caller cannot cancel someone elses pending purchase', function () {
    $user   = User::factory()->create();
    $course = makeCourse();

    $purchase = Purchase::create([
        'user_id'        => $user->id,
        'purchase_type'  => 'catalogue',
        'catalogue_id'   => $course->id,
        'amount'         => 25.00,
        'payment_status' => 'pending',
    ]);

    $this->get('/api/checkout/cancel?purchase_id=' . $purchase->id);

    expect($purchase->fresh()->payment_status)->toBe('pending');
});
