<?php

use App\Models\User;
use App\Models\Catalogue;
use App\Models\Purchase;
use App\Models\CatalogueExam;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamOption;
use App\Models\UserExamResult;

test('fetching local exam details requires authentication', function () {
    $response = $this->getJson('/api/profile/exams/1');
    $response->assertStatus(401);
});

test('fetching local exam details checks user purchase access', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Sample Course',
        'short_title' => 'SC',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);
    $localExam = Exam::create([
        'name' => 'Local Course Exam',
        'status' => 'published',
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Custom Local Exam',
        'exam_id' => $localExam->id,
        'is_premium' => false,
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}");

    $response->assertStatus(403);
});

test('fetching local exam details returns questions and options but strips is_correct', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Sample Course',
        'short_title' => 'SC',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $localExam = Exam::create([
        'name' => 'Local Course Exam',
        'status' => 'published',
    ]);

    $question = ExamQuestion::create([
        'exam_id' => $localExam->id,
        'question_text' => 'What is 2+2?',
        'sort_order' => 1,
    ]);

    $option1 = ExamOption::create([
        'exam_question_id' => $question->id,
        'option_text' => '4',
        'is_correct' => true,
        'sort_order' => 1,
    ]);

    $option2 = ExamOption::create([
        'exam_question_id' => $question->id,
        'option_text' => '5',
        'is_correct' => false,
        'sort_order' => 2,
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Custom Local Exam',
        'exam_id' => $localExam->id,
        'is_premium' => false,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'exam' => [
                    'id',
                    'catalogue_id',
                    'exam_title',
                    'questions' => [
                        '*' => [
                            'id',
                            'question_text',
                            'options' => [
                                '*' => [
                                    'id',
                                    'option_text',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

    // Ensure is_correct is NOT returned in the API response for security
    $responseData = $response->json();
    $options = $responseData['data']['exam']['questions'][0]['options'];
    foreach ($options as $opt) {
        expect($opt)->not->toHaveKey('is_correct');
    }
});

test('submitting local exam evaluates answers correctly and creates a result record', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Sample Course',
        'short_title' => 'SC',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $localExam = Exam::create([
        'name' => 'Local Course Exam',
        'status' => 'published',
    ]);

    $q1 = ExamQuestion::create([
        'exam_id' => $localExam->id,
        'question_text' => 'What is 2+2?',
        'sort_order' => 1,
    ]);

    $opt1 = ExamOption::create([
        'exam_question_id' => $q1->id,
        'option_text' => '4',
        'is_correct' => true,
        'sort_order' => 1,
    ]);

    $opt2 = ExamOption::create([
        'exam_question_id' => $q1->id,
        'option_text' => '5',
        'is_correct' => false,
        'sort_order' => 2,
    ]);

    $q2 = ExamQuestion::create([
        'exam_id' => $localExam->id,
        'question_text' => 'Capital of France?',
        'sort_order' => 2,
    ]);

    $opt3 = ExamOption::create([
        'exam_question_id' => $q2->id,
        'option_text' => 'Paris',
        'is_correct' => true,
        'sort_order' => 1,
    ]);

    $opt4 = ExamOption::create([
        'exam_question_id' => $q2->id,
        'option_text' => 'London',
        'is_correct' => false,
        'sort_order' => 2,
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Custom Local Exam',
        'exam_id' => $localExam->id,
        'is_premium' => false,
        'pass_mark' => 50.00,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    // Submit answers where 1 is correct (q1) and 1 is incorrect (q2)
    $response = $this->actingAs($user, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [
                [
                    'question_id' => $q1->id,
                    'option_id' => $opt1->id, // correct
                ],
                [
                    'question_id' => $q2->id,
                    'option_id' => $opt4->id, // incorrect
                ],
            ],
            'duration' => 120, // 2 minutes
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'result' => [
                    'id',
                    'score',
                    'points_available',
                    'percentage',
                    'status',
                    'taken_at',
                    'duration',
                    'certificate_serial_number',
                    'certificate_url',
                    'download_certificate',
                ]
            ]
        ])
        ->assertJsonFragment([
            'score' => 1.0,
            'points_available' => 2.0,
            'percentage' => 50.0,
            'status' => 'passed', // 50% pass mark by default
        ]);

    $examResult = UserExamResult::where('user_id', $user->id)
        ->where('catalogue_exam_id', $exam->id)
        ->first();

    expect($examResult->certificate_serial_number)->not->toBeNull();
    expect($examResult->certificate_url)->not->toBeNull();
    expect($examResult->download_certificate)->not->toBeNull();

    // Check if file exists
    $pathOnly = parse_url($examResult->certificate_url, PHP_URL_PATH);
    $filePath = public_path(ltrim($pathOnly, '/'));
    expect(file_exists($filePath))->toBeTrue();

    // Clean up file
    if (file_exists($filePath)) {
        unlink($filePath);
    }
});

test('local custom exam enforces attempt limits and cooldown for Certification service type', function () {
    $user = User::factory()->create();

    $catalogue = Catalogue::create([
        'title' => 'Specialized Certification Catalogue',
        'short_title' => 'SCC',
        'price_regular' => 150.00,
        'service_type' => 'Certification',
    ]);

    $localExam = Exam::create([
        'name' => 'Local Certification Exam',
        'status' => 'published',
    ]);

    $q1 = ExamQuestion::create([
        'exam_id' => $localExam->id,
        'question_text' => 'What is 1+1?',
        'sort_order' => 1,
    ]);

    $opt1 = ExamOption::create([
        'exam_question_id' => $q1->id,
        'option_text' => '2',
        'is_correct' => true,
        'sort_order' => 1,
    ]);

    $opt2 = ExamOption::create([
        'exam_question_id' => $q1->id,
        'option_text' => '3',
        'is_correct' => false,
        'sort_order' => 2,
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Custom Local Certification Exam',
        'exam_id' => $localExam->id,
        'is_premium' => false,
        'pass_mark' => 100.00,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 150.00,
        'payment_status' => 'paid',
    ]);

    // 1. Initial access: should be able to fetch details
    $responseDetails = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}");
    $responseDetails->assertStatus(200);

    // 2. Submit a failed attempt
    $responseSubmit = $this->actingAs($user, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [
                [
                    'question_id' => $q1->id,
                    'option_id' => $opt2->id,
                ]
            ],
            'duration' => 60,
        ]);
    $responseSubmit->assertStatus(200)
        ->assertJsonPath('data.result.status', 'failed');

    // 3. Immediately accessing details or submitting again should be locked (cooldown / limit exceeded)
    $responseDetailsLocked = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}");
    $responseDetailsLocked->assertStatus(403);

    $responseSubmitLocked = $this->actingAs($user, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [
                [
                    'question_id' => $q1->id,
                    'option_id' => $opt1->id,
                ]
            ],
            'duration' => 60,
        ]);
    $responseSubmitLocked->assertStatus(403);

    // 4. Update the failed attempt's created_at to 4 months ago to bypass cooldown
    $attempt = UserExamResult::where('user_id', $user->id)
        ->where('catalogue_exam_id', $exam->id)
        ->first();
    $attempt->created_at = now()->subMonths(4);
    $attempt->save();

    // 5. Accessing details should now be allowed
    $responseDetailsUnlocked = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}");
    $responseDetailsUnlocked->assertStatus(200);

    // 6. Submit a passing attempt
    $responseSubmitSuccess = $this->actingAs($user, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [
                [
                    'question_id' => $q1->id,
                    'option_id' => $opt1->id,
                ]
            ],
            'duration' => 60,
        ]);
    $responseSubmitSuccess->assertStatus(200)
        ->assertJsonPath('data.result.status', 'passed');

    // Clean up certificate file generated
    $passedAttempt = UserExamResult::where('user_id', $user->id)
        ->where('catalogue_exam_id', $exam->id)
        ->where('status', 'passed')
        ->first();
    if ($passedAttempt && $passedAttempt->certificate_url) {
        $pathOnly = parse_url($passedAttempt->certificate_url, PHP_URL_PATH);
        $filePath = public_path(ltrim($pathOnly, '/'));
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // 7. Once passed, even after cooldown, it should be permanently locked
    $responseDetailsPassed = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}");
    $responseDetailsPassed->assertStatus(403);
});

test('local custom exam respects package-specific limit for Course service type', function () {
    $user = User::factory()->create();

    // Create a package allowing 2 attempts
    $membershipPackage = \App\Models\MembershipPackage::create([
        'name' => 'Gold Premium',
        'title' => 'Gold Premium Member',
        'price' => 120.00,
        'discount_percentage' => 20.00,
        'exam_attempt_limit' => 2,
        'status' => 1,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $membershipPackage->id,
        'amount' => 120.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    $user->refresh();

    $catalogue = Catalogue::create([
        'title' => 'Standard Course Catalogue',
        'short_title' => 'SCC2',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $localExam = Exam::create([
        'name' => 'Local Course Exam',
        'status' => 'published',
    ]);

    $q1 = ExamQuestion::create([
        'exam_id' => $localExam->id,
        'question_text' => 'What is 1+1?',
        'sort_order' => 1,
    ]);

    $opt1 = ExamOption::create([
        'exam_question_id' => $q1->id,
        'option_text' => '2',
        'is_correct' => true,
        'sort_order' => 1,
    ]);

    $opt2 = ExamOption::create([
        'exam_question_id' => $q1->id,
        'option_text' => '3',
        'is_correct' => false,
        'sort_order' => 2,
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Custom Local Course Exam',
        'exam_id' => $localExam->id,
        'is_premium' => false,
        'pass_mark' => 100.00,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    // Attempt 1: Fetch and Submit (fail)
    $responseDetails1 = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}");
    $responseDetails1->assertStatus(200);

    $responseSubmit1 = $this->actingAs($user, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [
                [
                    'question_id' => $q1->id,
                    'option_id' => $opt2->id,
                ]
            ],
            'duration' => 60,
        ]);
    $responseSubmit1->assertStatus(200)
        ->assertJsonPath('data.result.status', 'failed');

    // Attempt 2: Fetch and Submit (fail)
    $responseDetails2 = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}");
    $responseDetails2->assertStatus(200);

    $responseSubmit2 = $this->actingAs($user, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [
                [
                    'question_id' => $q1->id,
                    'option_id' => $opt2->id,
                ]
            ],
            'duration' => 60,
        ]);
    $responseSubmit2->assertStatus(200)
        ->assertJsonPath('data.result.status', 'failed');

    // Attempt 3: Exceeded limit (since package allows 2)
    $responseDetails3 = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}");
    $responseDetails3->assertStatus(403);

    $responseSubmit3 = $this->actingAs($user, 'api')
        ->postJson("/api/profile/exams/{$exam->id}/submit", [
            'answers' => [
                [
                    'question_id' => $q1->id,
                    'option_id' => $opt1->id,
                ]
            ],
            'duration' => 60,
        ]);
    $responseSubmit3->assertStatus(403);
});
