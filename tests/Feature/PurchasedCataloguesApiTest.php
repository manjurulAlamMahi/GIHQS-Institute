<?php

use App\Models\User;
use App\Models\Catalogue;
use App\Models\Purchase;
use App\Models\CatalogueExam;
use App\Models\UserExamResult;

test('fetching purchased catalogues requires authentication', function () {
    $response = $this->getJson('/api/profile/purchased-catalogues');
    $response->assertStatus(401);
});

test('authenticated user with no purchases gets 404', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues');

    $response->assertStatus(404)
        ->assertJsonFragment([
            'status' => false,
            'message' => 'No purchased catalogues found.',
        ]);
});

test('authenticated user can retrieve their purchased catalogues', function () {
    $user = User::factory()->create();

    $catalogue = Catalogue::create([
        'title' => 'Test Catalogue',
        'short_title' => 'Test',
        'short_description' => 'A test description',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    // Create a purchase record
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'catalogues' => [
                    '*' => [
                        'id',
                        'title',
                        'short_title',
                        'short_description',
                        'price_regular',
                        'service_type',
                        'features',
                    ]
                ]
            ]
        ])
        ->assertJsonFragment([
            'title' => 'Test Catalogue',
        ]);
});

test('authenticated user who purchased a catalogue gets secure exam links via detail endpoint', function () {
    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    $catalogue = Catalogue::create([
        'title' => 'Test Catalogue',
        'short_title' => 'Test',
        'short_description' => 'A test description',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Midterm Exam',
        'exam_link' => 'https://www.classmarker.com/online-test/start/?quiz=abc',
        'is_premium' => true,
    ]);

    // Create a paid purchase
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $catalogue->id);

    $response->assertStatus(200);
    $data = $response->json('data.catalogue.exams.0');
    
    expect($data['exam_link'])->toContain('quiz=abc')
        ->toContain('cm_user_id=' . $user->id . '_' . $exam->id)
        ->toContain('cm_e=john.doe%40example.com')
        ->toContain('cm_fn=John')
        ->toContain('cm_ln=Doe');
});

test('public catalog details api returns only simplified catalogue details', function () {
    $catalogue = Catalogue::create([
        'title' => 'Premium Catalogue',
        'short_title' => 'Test',
        'short_description' => 'A test description',
        'price_regular' => 50.00,
        'service_type' => 'Course',
        'details_file' => 'uploads/development-catalogues/test_details.html',
        'story_guide_file' => 'uploads/development-catalogues/test_story_guide.html',
        'module_file' => 'uploads/development-catalogues/test_module.html',
    ]);

    CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Premium Exam',
        'exam_link' => 'https://www.classmarker.com/online-test/start/?quiz=abc',
        'is_premium' => true,
    ]);

    // 1. Unauthenticated request - the marketing details file stays public, but
    //    the paid material (story guide, module) is withheld.
    $response1 = $this->getJson("/api/catalogues/{$catalogue->id}");
    $response1->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Catalogue details fetched successfully.',
            'data' => [
                'catalogue' => [
                    'id' => $catalogue->id,
                    'title' => $catalogue->title,
                    'details_file' => asset($catalogue->details_file),
                    'story_guide_file' => null,
                    'module_file' => null,
                    'has_access' => false,
                ]
            ]
        ]);

    // Ensure no additional fields are present
    $catalogueData = $response1->json('data.catalogue');
    expect($catalogueData)->toHaveKeys(['id', 'title', 'details_file', 'story_guide_file', 'module_file'])
        ->not->toHaveKey('exams')
        ->not->toHaveKey('resources')
        ->not->toHaveKey('features');

    // 2. Authenticated but unpaid request - still no paid material.
    $user = User::factory()->create();
    $response2 = $this->actingAs($user, 'api')->getJson("/api/catalogues/{$catalogue->id}");
    $response2->assertStatus(200);
    $catalogueData2 = $response2->json('data.catalogue');
    expect($catalogueData2)->toHaveKeys(['id', 'title', 'details_file', 'story_guide_file', 'module_file'])
        ->not->toHaveKey('exams');
    expect($catalogueData2['module_file'])->toBeNull()
        ->and($catalogueData2['story_guide_file'])->toBeNull();

    // 3. A user who has paid for the catalogue receives the paid material.
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    $this->actingAs($user, 'api')
        ->getJson("/api/catalogues/{$catalogue->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.catalogue.module_file', asset($catalogue->module_file))
        ->assertJsonPath('data.catalogue.story_guide_file', asset($catalogue->story_guide_file));
});

test('classmarker webhook verification returns 200', function () {
    config(['services.classmarker.webhook_secret' => 'super_secret']);

    $payload = [
        'payload_status' => 'verify',
    ];
    $json = json_encode($payload);
    $signature = base64_encode(hash_hmac('sha256', $json, 'super_secret', true));

    $response = $this->withHeader('X-Classmarker-Hmac-Sha256', $signature)
        ->postJson('/api/classmarker/webhook', $payload);

    $response->assertStatus(200)
        ->assertJson(['status' => 'verified']);
});

test('classmarker webhook stores live exam results correctly', function () {
    config(['services.classmarker.webhook_secret' => 'super_secret']);

    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Test Catalogue',
        'short_title' => 'Test',
        'price_regular' => 10.00,
        'service_type' => 'Course',
    ]);
    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Midterm Exam',
        'exam_link' => 'https://example.com/test',
        'is_premium' => true,
    ]);

    // The webhook only credits a result to a user who is entitled to the exam.
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 10.00,
        'payment_status' => 'paid',
    ]);

    $payload = [
        'payload_status' => 'live',
        'result' => [
            // cm_user_id must carry the signature this application issued.
            'cm_user_id' => App\Support\ExamLinkSigner::sign($user->id, $exam->id),
            'points_scored' => '8.5',
            'points_available' => '10.0',
            'percentage' => '85.0',
            'percentage_passmark' => '70.0',
            'passed' => true,
            'duration' => '10 minutes',
            'ip_address' => '192.168.1.1',
            'time_started' => 1436263102,
            'time_finished' => 1436263702,
            'link_result_id' => 'res_12345',
            'certificate_serial' => 'CERT-12345',
            'certificate_url' => 'https://example.com/cert.pdf',
            'view_results_url' => 'https://example.com/result',
            'categories' => [
                [
                    'name' => 'Category 1',
                    'percentage' => 85.0,
                ]
            ],
        ]
    ];
    $json = json_encode($payload);
    $signature = base64_encode(hash_hmac('sha256', $json, 'super_secret', true));

    $response = $this->withHeader('X-Classmarker-Hmac-Sha256', $signature)
        ->postJson('/api/classmarker/webhook', $payload);

    $response->assertStatus(200)
        ->assertJson(['status' => 'success']);

    $this->assertDatabaseHas('user_exam_results', [
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam->id,
        'score' => 8.5,
        'points_available' => 10.0,
        'percentage' => 85.0,
        'percentage_passmark' => 70.0,
        'status' => 'passed',
        'duration' => '10 minutes',
        'ip_address' => '192.168.1.1',
        'classmarker_result_id' => 'res_12345',
        'view_results_url' => 'https://example.com/result',
        'category_results' => json_encode([
            [
                'name' => 'Category 1',
                'percentage' => 85.0,
            ]
        ]),
    ]);

    $examResult = \App\Models\UserExamResult::where('classmarker_result_id', 'res_12345')->first();
    expect($examResult)->not->toBeNull();
    expect($examResult->certificate_serial_number)->toStartWith('GIHQS-' . now()->format('Y') . '-');
    expect($examResult->certificate_url)->toContain('uploads/certificates/certificate_');
    expect($examResult->download_certificate)->toContain('uploads/certificates/certificate_');
});

test('classmarker webhook rejects invalid signatures', function () {
    config(['services.classmarker.webhook_secret' => 'super_secret']);

    $payload = [
        'payload_status' => 'live',
    ];

    $response = $this->withHeader('X-Classmarker-Hmac-Sha256', 'wrong_signature')
        ->postJson('/api/classmarker/webhook', $payload);

    $response->assertStatus(403);
});

test('authenticated user who purchased a catalogue can fetch single catalogue details', function () {
    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    // Create a membership package with multiple exam attempts allowed
    $membershipPackage = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 95.00,
        'discount_percentage' => 15.00,
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);

    // Create a paid membership purchase so user has active membership
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $membershipPackage->id,
        'amount' => 95.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    $user->refresh();

    $catalogue = Catalogue::create([
        'title' => 'Test Single Catalogue',
        'short_title' => 'Single Test',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Single Exam',
        'exam_link' => 'https://www.classmarker.com/online-test/start/?quiz=xyz',
        'is_premium' => true,
    ]);

    // Create a paid purchase
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    // Create a mock exam result
    UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam->id,
        'score' => 9.0,
        'percentage' => 90.0,
        'status' => 'passed',
        'duration' => '15 minutes',
        'classmarker_result_id' => 'mock_res_123',
        'certificate_serial_number' => 'CERT-12345',
        'certificate_url' => 'https://example.com/cert.pdf',
        'download_certificate' => 'https://example.com/cert.pdf',
        'view_results_url' => 'https://example.com/result',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'catalogue' => [
                    'id',
                    'title',
                    'exams' => [
                        '*' => [
                            'id',
                            'exam_title',
                            'exam_link',
                            'user_status' => [
                                'status',
                                'score',
                                'percentage',
                                'taken_at',
                                'certificate_serial_number',
                                'certificate_url',
                                'download_certificate',
                                'view_results_url'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

    $data = $response->json('data.catalogue.exams.0');
    expect($data['exam_link'])->toContain('quiz=xyz')
        ->toContain('cm_user_id=' . $user->id . '_' . $exam->id)
        ->toContain('cm_e=john.doe%40example.com');
    expect($data['user_status']['status'])->toBe('passed');
    expect($data['user_status']['certificate_serial_number'])->toBe('CERT-12345');
    expect($data['user_status']['certificate_url'])->toBe('https://example.com/cert.pdf');
    expect($data['user_status']['download_certificate'])->toBe('https://example.com/cert.pdf');
    expect($data['user_status']['view_results_url'])->toBe('https://example.com/result');
});

test('user cannot fetch single purchased catalogue details if they have not purchased it', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Unpurchased Catalogue',
        'short_title' => 'Unpurchased',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");

    $response->assertStatus(404);
});

test('user can fetch only purchased courses and certifications using dynamic filter', function () {
    $user = User::factory()->create();

    $course = Catalogue::create([
        'title' => 'Test Course Item',
        'short_title' => 'Course Item',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $exam = Catalogue::create([
        'title' => 'Test Exam Item',
        'short_title' => 'Exam Item',
        'price_regular' => 50.00,
        'service_type' => 'Exam',
    ]);

    // Purchase both
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $course->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $exam->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues?service_type=course');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.catalogues')
        ->assertJsonFragment([
            'title' => 'Test Course Item',
            'service_type' => 'Course',
        ]);
});

test('user can fetch only purchased examinations using dynamic filter', function () {
    $user = User::factory()->create();

    $course = Catalogue::create([
        'title' => 'Test Course Item',
        'short_title' => 'Course Item',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $exam = Catalogue::create([
        'title' => 'Test Exam Item',
        'short_title' => 'Exam Item',
        'price_regular' => 50.00,
        'service_type' => 'Exam',
    ]);

    // Purchase both
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $course->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $exam->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues?service_type=exam');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.catalogues')
        ->assertJsonFragment([
            'title' => 'Test Exam Item',
            'service_type' => 'Exam',
        ]);
});

test('user can fetch multiple purchased types using comma-separated values', function () {
    $user = User::factory()->create();

    $course = Catalogue::create([
        'title' => 'Test Course Item',
        'short_title' => 'Course Item',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $exam = Catalogue::create([
        'title' => 'Test Exam Item',
        'short_title' => 'Exam Item',
        'price_regular' => 50.00,
        'service_type' => 'Exam',
    ]);

    // Purchase both
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $course->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $exam->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues?service_type=course,exam');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data.catalogues');
});

test('public catalogues api supports sorting by service type', function () {
    Catalogue::query()->delete();

    Catalogue::create([
        'title' => 'Course A',
        'short_title' => 'CA',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    Catalogue::create([
        'title' => 'Certification B',
        'short_title' => 'CB',
        'price_regular' => 50.00,
        'service_type' => 'Certification',
    ]);

    // Test sorting=course (returns 1 item)
    $response = $this->getJson('/api/catalogues?sorting=course');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.catalogues')
        ->assertJsonFragment(['title' => 'Course A']);

    // Test sorting=certification (returns 1 item)
    $response = $this->getJson('/api/catalogues?sorting=certification');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.catalogues')
        ->assertJsonFragment(['title' => 'Certification B']);

    // Test sorting=all (returns both items)
    $response = $this->getJson('/api/catalogues?sorting=all');
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data.catalogues');
});

test('public catalogues api supports combined sorting and filtering', function () {
    Catalogue::query()->delete();

    // 1. Trending Course
    Catalogue::create([
        'title' => 'Trending Course',
        'short_title' => 'TC',
        'price_regular' => 50.00,
        'service_type' => 'Course',
        'is_trending' => true,
    ]);

    // 2. Non-trending Course
    Catalogue::create([
        'title' => 'Regular Course',
        'short_title' => 'RC',
        'price_regular' => 50.00,
        'service_type' => 'Course',
        'is_trending' => false,
    ]);

    // 3. Trending Certification
    Catalogue::create([
        'title' => 'Trending Certification',
        'short_title' => 'Tcert',
        'price_regular' => 50.00,
        'service_type' => 'Certification',
        'is_trending' => true,
    ]);

    // Query combination: course + trending (should only return item 1)
    $response = $this->getJson('/api/catalogues?sorting=course&filtering=trending');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.catalogues')
        ->assertJsonFragment(['title' => 'Trending Course']);
});

test('user exam results fall back to raw_payload values when database columns are null', function () {
    $user = User::factory()->create();

    $catalogue = Catalogue::create([
        'title' => 'Test Catalogue for Fallback',
        'short_title' => 'Fallback Test',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Fallback Exam',
        'exam_link' => 'https://www.classmarker.com/online-test/start/?quiz=fallback',
        'is_premium' => true,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    // Create a user exam result without the DB columns filled, but with raw_payload containing them
    $result = UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam->id,
        'score' => 9.5,
        'percentage' => 95.0,
        'status' => 'passed',
        'duration' => '12 minutes',
        'classmarker_result_id' => 'fallback_res_123',
        'certificate_serial_number' => null,
        'certificate_url' => null,
        'download_certificate' => null,
        'view_results_url' => null,
        'raw_payload' => [
            'result' => [
                'certificate_serial' => 'RAW-CERT-12345',
                'certificate_url' => 'https://example.com/raw-cert.pdf',
                'view_results_url' => 'https://example.com/raw-result',
            ]
        ],
    ]);

    // Retrieve via model and assert accessors work
    $freshResult = UserExamResult::find($result->id);
    expect($freshResult->certificate_serial_number)->toBe('RAW-CERT-12345');
    expect($freshResult->certificate_url)->toBe('https://example.com/raw-cert.pdf');
    expect($freshResult->download_certificate)->toBe('https://example.com/raw-cert.pdf');
    expect($freshResult->view_results_url)->toBe('https://example.com/raw-result');

    // Retrieve via API endpoint and assert the response has the correct values
    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");

    $response->assertStatus(200);
    $examData = $response->json('data.catalogue.exams.0.user_status');
    expect($examData['certificate_serial_number'])->toBe('RAW-CERT-12345');
    expect($examData['certificate_url'])->toBe('https://example.com/raw-cert.pdf');
    expect($examData['download_certificate'])->toBe('https://example.com/raw-cert.pdf');
    expect($examData['view_results_url'])->toBe('https://example.com/raw-result');
});

test('fetching exam attempts requires authentication', function () {
    $response = $this->getJson('/api/profile/exams/1/attempts');
    $response->assertStatus(401);
});

test('fetching attempts for non-existent exam returns 404', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/exams/9999/attempts');

    $response->assertStatus(404)
        ->assertJsonFragment([
            'status' => false,
            'message' => 'Exam not found.',
        ]);
});

test('user cannot fetch attempts for an exam in an unpaid/unpurchased catalogue', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Test Catalogue',
        'short_title' => 'Test',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);
    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Unpurchased Exam',
        'exam_link' => 'https://www.classmarker.com/online-test/start/?quiz=abc',
        'is_premium' => true,
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}/attempts");

    $response->assertStatus(403)
        ->assertJsonFragment([
            'status' => false,
            'message' => 'You do not have access to this exam.',
        ]);
});

test('user can successfully fetch their attempts for a purchased catalogue exam', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Test Catalogue',
        'short_title' => 'Test',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);
    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Purchased Exam',
        'exam_link' => 'https://www.classmarker.com/online-test/start/?quiz=abc',
        'is_premium' => true,
    ]);

    // Create a purchase record
    $purchase = Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);
    $purchase->created_at = now()->subMinutes(10);
    $purchase->save();

    // Create a couple of exam attempts
    $attempt1 = UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam->id,
        'score' => 2.0,
        'percentage' => 66.6,
        'status' => 'failed',
        'duration' => '10 minutes',
        'classmarker_result_id' => 'res_1',
    ]);
    $attempt1->created_at = now()->subMinutes(5);
    $attempt1->save();

    $attempt2 = UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam->id,
        'score' => 3.0,
        'percentage' => 100.0,
        'status' => 'passed',
        'duration' => '8 minutes',
        'classmarker_result_id' => 'res_2',
        'certificate_serial_number' => 'CERT-123',
    ]);
    $attempt2->created_at = now();
    $attempt2->save();

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/exams/{$exam->id}/attempts");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'exam' => [
                    'id',
                    'catalogue_id',
                    'exam_title',
                    'exam_link',
                    'is_premium',
                ],
                'attempts' => [
                    '*' => [
                        'id',
                        'score',
                        'points_available',
                        'percentage',
                        'percentage_passmark',
                        'status',
                        'duration',
                        'ip_address',
                        'start_time',
                        'end_time',
                        'taken_at',
                        'certificate_serial_number',
                        'certificate_url',
                        'download_certificate',
                        'view_results_url',
                        'category_results',
                    ]
                ]
            ]
        ]);

    $attempts = $response->json('data.attempts');
    expect($attempts)->toHaveCount(2);
    // Since they are ordered by latest first, the second one created (which passed) should be first
    expect($attempts[0]['status'])->toBe('passed');
    expect($attempts[0]['certificate_serial_number'])->toBe('CERT-123');
    expect($attempts[1]['status'])->toBe('failed');
});

test('authenticated user can filter purchased catalogues by keyword', function () {
    $user = User::factory()->create();

    $catalogue1 = Catalogue::create([
        'title' => 'Advanced AI in Healthcare',
        'short_title' => 'AI Healthcare',
        'short_description' => 'A course about AI',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $catalogue2 = Catalogue::create([
        'title' => 'Risk Management',
        'short_title' => 'Risk Mgmt',
        'short_description' => 'A course about risk management',
        'price_regular' => 60.00,
        'service_type' => 'Course',
    ]);

    // Purchase both
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue1->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue2->id,
        'amount' => 60.00,
        'payment_status' => 'paid',
    ]);

    // Search for "Healthcare"
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues?keyword=Healthcare');

    $response->assertStatus(200);
    $catalogues = $response->json('data.catalogues');
    expect($catalogues)->toHaveCount(1);
    expect($catalogues[0]['title'])->toBe('Advanced AI in Healthcare');

    // Search for "Risk"
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues?keyword=Risk');

    $response->assertStatus(200);
    $catalogues = $response->json('data.catalogues');
    expect($catalogues)->toHaveCount(1);
    expect($catalogues[0]['title'])->toBe('Risk Management');

    // Search for "Nonexistent"
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues?keyword=Nonexistent');

    $response->assertStatus(404);
});

test('credit and ce_credit_total_required fields are saved and returned in public/purchased catalogues API responses', function () {
    $user = User::factory()->create();

    $catalogue = Catalogue::create([
        'title' => 'Credit Test Course',
        'short_title' => 'CTC',
        'short_description' => 'A test course with credits',
        'price_regular' => 100.00,
        'service_type' => 'Course',
        'credit_earn' => 4.50,
        'ce_credit_total_required' => 20.00,
        'status' => 1,
    ]);

    // Test public catalogues API
    $responsePublic = $this->getJson('/api/catalogues');
    $responsePublic->assertStatus(200);
    $publicCatalogues = $responsePublic->json('data.catalogues');
    $publicItem = collect($publicCatalogues)->firstWhere('id', $catalogue->id);
    expect($publicItem)->not->toBeNull();
    expect((float)$publicItem['credit_earn'])->toBe(4.5);
    expect((float)$publicItem['ce_credit_total_required'])->toBe(20.0);

    // Create a purchase record
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 100.00,
        'payment_status' => 'paid',
    ]);

    // Test profile purchased catalogues list API
    $responsePurchased = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues');
    $responsePurchased->assertStatus(200);
    $purchasedCatalogues = $responsePurchased->json('data.catalogues');
    $purchasedItem = collect($purchasedCatalogues)->firstWhere('id', $catalogue->id);
    expect($purchasedItem)->not->toBeNull();
    expect((float)$purchasedItem['credit_earn'])->toBe(4.5);
    expect((float)$purchasedItem['ce_credit_total_required'])->toBe(20.0);

    // Test profile purchased catalogue show API
    $responseShow = $this->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");
    $responseShow->assertStatus(200);
    $showItem = $responseShow->json('data.catalogue');
    expect((float)$showItem['credit_earn'])->toBe(4.5);
    expect((float)$showItem['ce_credit_total_required'])->toBe(20.0);
});

test('user with active membership automatically gets access to members only catalogues', function () {
    $user = User::factory()->create();

    $membershipPackage = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 95.00,
        'discount_percentage' => 15.00,
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $membershipPackage->id,
        'amount' => 95.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    // Create a regular catalogue (paid)
    $paidCatalogue = Catalogue::create([
        'title' => 'Paid Course',
        'short_title' => 'Paid',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    // Create a members only catalogue
    $membersOnlyCatalogue = Catalogue::create([
        'title' => 'Members Only Course',
        'short_title' => 'Members Only',
        'catalogue_type' => 'members only',
        'price_regular' => 0.00,
        'service_type' => 'Course',
    ]);

    // Request purchased catalogues listing
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues');

    $response->assertStatus(200);
    $catalogues = $response->json('data.catalogues');
    
    // User should see the "members only" catalogue, but not the "paid" catalogue (since they haven't purchased it)
    $ids = collect($catalogues)->pluck('id')->toArray();
    expect($ids)->toContain($membersOnlyCatalogue->id);
    expect($ids)->not->toContain($paidCatalogue->id);
});

test('user with active membership can view details of a members only catalogue', function () {
    $user = User::factory()->create();

    $membershipPackage = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 95.00,
        'discount_percentage' => 15.00,
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $membershipPackage->id,
        'amount' => 95.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    // Create a members only catalogue
    $membersOnlyCatalogue = Catalogue::create([
        'title' => 'Members Only Course',
        'short_title' => 'Members Only',
        'catalogue_type' => 'members only',
        'price_regular' => 0.00,
        'service_type' => 'Course',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $membersOnlyCatalogue->id);

    $response->assertStatus(200)
        ->assertJsonFragment([
            'title' => 'Members Only Course',
        ]);
});

test('user with active membership can access exam attempts of a members only catalogue', function () {
    $user = User::factory()->create();

    $membershipPackage = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 95.00,
        'discount_percentage' => 15.00,
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $membershipPackage->id,
        'amount' => 95.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    // Create a members only catalogue
    $membersOnlyCatalogue = Catalogue::create([
        'title' => 'Members Only Course',
        'short_title' => 'Members Only',
        'catalogue_type' => 'members only',
        'price_regular' => 0.00,
        'service_type' => 'Course',
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $membersOnlyCatalogue->id,
        'exam_title' => 'Members Exam',
        'exam_link' => 'https://example.com/exam',
        'is_premium' => true,
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/exams/' . $exam->id . '/attempts');

    $response->assertStatus(200);
});

test('user without active membership cannot access members only catalogues', function () {
    $user = User::factory()->create();

    // Create a members only catalogue
    $membersOnlyCatalogue = Catalogue::create([
        'title' => 'Members Only Course',
        'short_title' => 'Members Only',
        'catalogue_type' => 'members only',
        'price_regular' => 0.00,
        'service_type' => 'Course',
    ]);

    // Request purchased catalogues listing - should get 404 since no purchases and not a member
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues');

    $response->assertStatus(404);

    // Try accessing detail endpoint
    $responseDetail = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $membersOnlyCatalogue->id);

    $responseDetail->assertStatus(404);
});

test('user with standard membership cannot access members only catalogues and cannot purchase them', function () {
    $user = User::factory()->create();

    $standardPackage = \App\Models\MembershipPackage::create([
        'name' => 'Standard',
        'title' => 'Standard Member',
        'price' => 0.00,
        'discount_percentage' => 0.00,
        'exam_attempt_limit' => 1,
        'status' => 1,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $standardPackage->id,
        'amount' => 0.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => null,
    ]);

    // Create a members only catalogue
    $membersOnlyCatalogue = Catalogue::create([
        'title' => 'Members Only Course',
        'short_title' => 'Members Only',
        'catalogue_type' => 'members only',
        'price_regular' => 0.00,
        'service_type' => 'Course',
    ]);

    // Request purchased catalogues listing - should get 404 since no access
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues');

    $response->assertStatus(404);

    // Try accessing detail endpoint
    $responseDetail = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $membersOnlyCatalogue->id);

    $responseDetail->assertStatus(404);

    // Try checkout - should fail with 403
    $responseCheckout = $this->actingAs($user, 'api')
        ->postJson('/api/checkout', [
            'catalogue_id' => $membersOnlyCatalogue->id,
        ]);
    $responseCheckout->assertStatus(403);
});

test('members only catalogues are hidden from guest and standard members in public APIs but visible and free for premium members', function () {
    // Clean up
    Catalogue::query()->delete();
    
    $regularCatalogue = Catalogue::create([
        'title' => 'Regular Public Course',
        'short_title' => 'Regular Course',
        'catalogue_type' => 'paid',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $membersOnlyCatalogue = Catalogue::create([
        'title' => 'Premium Members Course Only',
        'short_title' => 'Premium Only',
        'catalogue_type' => 'members only',
        'price_regular' => 100.00,
        'service_type' => 'Course',
    ]);

    // 1. Guest Check
    $responseGuestList = $this->getJson('/api/catalogues');
    $responseGuestList->assertStatus(200);
    $guestIds = collect($responseGuestList->json('data.catalogues'))->pluck('id')->toArray();
    expect($guestIds)->not->toContain($membersOnlyCatalogue->id);

    $responseGuestShow = $this->getJson('/api/catalogues/' . $membersOnlyCatalogue->id);
    $responseGuestShow->assertStatus(404);

    $responseGuestDetails = $this->getJson('/api/catalogues/' . $membersOnlyCatalogue->id . '/details');
    $responseGuestDetails->assertStatus(404);

    // 2. Standard Member Check
    $standardUser = User::factory()->create();
    $standardPackage = \App\Models\MembershipPackage::create([
        'name' => 'Standard',
        'title' => 'Standard Member',
        'price' => 0.00,
        'discount_percentage' => 0.00,
        'exam_attempt_limit' => 1,
        'status' => 1,
    ]);
    Purchase::create([
        'user_id' => $standardUser->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $standardPackage->id,
        'amount' => 0.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => null,
    ]);

    $responseStandardList = $this->actingAs($standardUser, 'api')->getJson('/api/catalogues');
    $responseStandardList->assertStatus(200);
    $standardIds = collect($responseStandardList->json('data.catalogues'))->pluck('id')->toArray();
    expect($standardIds)->not->toContain($membersOnlyCatalogue->id);

    $responseStandardShow = $this->actingAs($standardUser, 'api')->getJson('/api/catalogues/' . $membersOnlyCatalogue->id);
    $responseStandardShow->assertStatus(404);

    $responseStandardDetails = $this->actingAs($standardUser, 'api')->getJson('/api/catalogues/' . $membersOnlyCatalogue->id . '/details');
    $responseStandardDetails->assertStatus(404);

    // 3. Premium Member Check (Paid Member)
    $premiumUser = User::factory()->create();
    $premiumPackage = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 95.00,
        'discount_percentage' => 15.00,
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);
    Purchase::create([
        'user_id' => $premiumUser->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $premiumPackage->id,
        'amount' => 95.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    $responsePremiumList = $this->actingAs($premiumUser, 'api')->getJson('/api/catalogues');
    $responsePremiumList->assertStatus(200);
    $premiumCatalogues = $responsePremiumList->json('data.catalogues');
    $premiumIds = collect($premiumCatalogues)->pluck('id')->toArray();
    expect($premiumIds)->toContain($membersOnlyCatalogue->id);

    $premiumItem = collect($premiumCatalogues)->firstWhere('id', $membersOnlyCatalogue->id);
    expect((float)$premiumItem['price_final'])->toBe(0.00);

    $responsePremiumShow = $this->actingAs($premiumUser, 'api')->getJson('/api/catalogues/' . $membersOnlyCatalogue->id);
    $responsePremiumShow->assertStatus(200);

    $responsePremiumDetails = $this->actingAs($premiumUser, 'api')->getJson('/api/catalogues/' . $membersOnlyCatalogue->id . '/details');
    $responsePremiumDetails->assertStatus(200);
    expect((float)$responsePremiumDetails->json('data.catalogue.price_final'))->toBe(0.00);
});

test('user with active membership cannot purchase members only catalogue', function () {
    $user = User::factory()->create();

    $membershipPackage = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 95.00,
        'discount_percentage' => 15.00,
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $membershipPackage->id,
        'amount' => 95.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    // Create a members only catalogue
    $membersOnlyCatalogue = Catalogue::create([
        'title' => 'Members Only Course',
        'short_title' => 'Members Only',
        'catalogue_type' => 'members only',
        'price_regular' => 0.00,
        'service_type' => 'Course',
    ]);

    // Try stripe checkout initiation
    $responseStripe = $this->actingAs($user, 'api')
        ->getJson('/api/checkout/' . $membersOnlyCatalogue->id, ['Accept' => 'application/json']);
    
    $responseStripe->assertStatus(400)
        ->assertJsonFragment(['message' => 'You already have access to this item.']);

    // Try POST checkout initiation (which also calls the check)
    $responseCredit = $this->actingAs($user, 'api')
        ->postJson('/api/checkout', [
            'catalogue_id' => $membersOnlyCatalogue->id,
        ]);
    
    $responseCredit->assertStatus(400)
        ->assertJsonFragment(['message' => 'You already have access to this item.']);
});

test('certifications_approved is returned correctly in catalogues API response based on application status', function () {
    Catalogue::query()->delete();
    \App\Models\CertificationApplication::query()->delete();

    $user = User::factory()->create();

    $catalogueApproved = Catalogue::create([
        'title' => 'Certification Approved Programme',
        'short_title' => 'CAP',
        'price_regular' => 100.00,
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    $cataloguePending = Catalogue::create([
        'title' => 'Certification Pending Programme',
        'short_title' => 'CPP',
        'price_regular' => 100.00,
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    $catalogueNoApp = Catalogue::create([
        'title' => 'Certification No App Programme',
        'short_title' => 'CNAP',
        'price_regular' => 100.00,
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    // Create an accepted certification application for $catalogueApproved
    \App\Models\CertificationApplication::create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => $user->email,
        'phone' => '12345678',
        'country' => 'USA',
        'city' => 'NY',
        'current_job_title' => 'Developer',
        'organization' => 'Corp',
        'years_of_experience' => '5',
        'primary_area_of_experience' => 'Coding',
        'professional_role' => 'Engineer',
        'catalogue_id' => $catalogueApproved->id,
        'confirm_accuracy' => true,
        'agree_policies' => true,
        'status' => 'accepted',
    ]);

    // Create a pending certification application for $cataloguePending
    \App\Models\CertificationApplication::create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => $user->email,
        'phone' => '12345678',
        'country' => 'USA',
        'city' => 'NY',
        'current_job_title' => 'Developer',
        'organization' => 'Corp',
        'years_of_experience' => '5',
        'primary_area_of_experience' => 'Coding',
        'professional_role' => 'Engineer',
        'catalogue_id' => $cataloguePending->id,
        'confirm_accuracy' => true,
        'agree_policies' => true,
        'status' => 'pending',
    ]);

    // 1. Check guest response
    $responseGuest = $this->getJson('/api/catalogues');
    $responseGuest->assertStatus(200);
    $guestCatalogues = $responseGuest->json('data.catalogues');
    
    expect($guestCatalogues)->toHaveCount(3);
    foreach ($guestCatalogues as $cat) {
        expect($cat['certification_approved'])->toBeFalse();
    }

    // 2. Check authenticated response
    $responseAuth = $this->actingAs($user, 'api')->getJson('/api/catalogues');
    $responseAuth->assertStatus(200);
    
    $authCatalogues = $responseAuth->json('data.catalogues');
    expect($authCatalogues)->toHaveCount(3);

    $approvedItem = collect($authCatalogues)->firstWhere('id', $catalogueApproved->id);
    expect($approvedItem['certification_approved'])->toBeTrue();

    $pendingItem = collect($authCatalogues)->firstWhere('id', $cataloguePending->id);
    expect($pendingItem['certification_approved'])->toBeFalse();

    $noAppItem = collect($authCatalogues)->firstWhere('id', $catalogueNoApp->id);
    expect($noAppItem['certification_approved'])->toBeFalse();
});

test('single catalogue details endpoint returns correct structure and certification_approved status', function () {
    Catalogue::query()->delete();
    \App\Models\CertificationApplication::query()->delete();

    $user = User::factory()->create();

    $catalogueApproved = Catalogue::create([
        'title' => 'Certification Approved Detail Programme',
        'short_title' => 'CADP',
        'price_regular' => 120.00,
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    $cataloguePending = Catalogue::create([
        'title' => 'Certification Pending Detail Programme',
        'short_title' => 'CPDP',
        'price_regular' => 120.00,
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    // Create accepted certification application for $catalogueApproved
    \App\Models\CertificationApplication::create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => $user->email,
        'phone' => '12345678',
        'country' => 'USA',
        'city' => 'NY',
        'current_job_title' => 'Developer',
        'organization' => 'Corp',
        'years_of_experience' => '5',
        'primary_area_of_experience' => 'Coding',
        'professional_role' => 'Engineer',
        'catalogue_id' => $catalogueApproved->id,
        'confirm_accuracy' => true,
        'agree_policies' => true,
        'status' => 'accepted',
    ]);

    // Create pending certification application for $cataloguePending
    \App\Models\CertificationApplication::create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => $user->email,
        'phone' => '12345678',
        'country' => 'USA',
        'city' => 'NY',
        'current_job_title' => 'Developer',
        'organization' => 'Corp',
        'years_of_experience' => '5',
        'primary_area_of_experience' => 'Coding',
        'professional_role' => 'Engineer',
        'catalogue_id' => $cataloguePending->id,
        'confirm_accuracy' => true,
        'agree_policies' => true,
        'status' => 'pending',
    ]);

    // 1. Guest request for details
    $responseGuest = $this->getJson("/api/catalogues/{$catalogueApproved->id}/details");
    $responseGuest->assertStatus(200)
        ->assertJsonPath('data.catalogue.certification_approved', false)
        ->assertJsonPath('data.catalogue.price_final', 120);

    // 2. Auth request for details of approved catalogue
    $responseAuthApproved = $this->actingAs($user, 'api')->getJson("/api/catalogues/{$catalogueApproved->id}/details");
    $responseAuthApproved->assertStatus(200)
        ->assertJsonPath('data.catalogue.certification_approved', true)
        ->assertJsonPath('data.catalogue.price_final', 120)
        ->assertJsonPath('data.catalogue.title', 'Certification Approved Detail Programme');

    // 3. Auth request for details of pending catalogue
    $responseAuthPending = $this->actingAs($user, 'api')->getJson("/api/catalogues/{$cataloguePending->id}/details");
    $responseAuthPending->assertStatus(200)
        ->assertJsonPath('data.catalogue.certification_approved', false)
        ->assertJsonPath('data.catalogue.title', 'Certification Pending Detail Programme');

    // 4. Invalid ID returns 404
    $responseInvalid = $this->getJson("/api/catalogues/999999/details");
    $responseInvalid->assertStatus(404);
});

test('dynamic attempt limit and cooldown check for Certification services', function () {
    $user = User::factory()->create();

    $catalogue = Catalogue::create([
        'title' => 'Specialized Certification',
        'short_title' => 'SC',
        'price_regular' => 150.00,
        'service_type' => 'Certification',
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Certification Exam',
        'exam_link' => 'https://www.classmarker.com/online-test/start/?quiz=cert',
        'is_premium' => true,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 150.00,
        'payment_status' => 'paid',
    ]);

    // 1. Never taken yet: max_attempts=1, attempts_count=0, attempts_exceeded=false
    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");
    $response->assertStatus(200);
    $examData = $response->json('data.catalogue.exams.0');
    expect($examData['max_attempts'])->toBe(1);
    expect($examData['attempts_count'])->toBe(0);
    expect($examData['attempts_exceeded'])->toBeFalse();
    expect($examData['exam_link'])->not->toBeNull();

    // 2. Fail the attempt
    $attempt = UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam->id,
        'score' => 5.0,
        'percentage' => 50.0,
        'status' => 'failed',
        'duration' => '10 minutes',
        'classmarker_result_id' => 'res_failed_1',
    ]);
    $attempt->created_at = now();
    $attempt->save();

    // Within cooldown: max_attempts=1, attempts_count=1, attempts_exceeded=true, retake_locked=true, exam_link=null
    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");
    $response->assertStatus(200);
    $examData = $response->json('data.catalogue.exams.0');
    expect($examData['max_attempts'])->toBe(1);
    expect($examData['attempts_count'])->toBe(1);
    expect($examData['attempts_exceeded'])->toBeTrue();
    expect($examData['retake_locked'])->toBeTrue();
    expect($examData['exam_link'])->toBeNull();

    // 3. Move the failed attempt to 4 months ago (cooldown expired)
    $attempt->created_at = now()->subMonths(4);
    $attempt->save();

    // Cooldown expired: max_attempts=1, attempts_count=0, attempts_exceeded=false, retake_locked=false, exam_link is valid
    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");
    $response->assertStatus(200);
    $examData = $response->json('data.catalogue.exams.0');
    expect($examData['max_attempts'])->toBe(1);
    expect($examData['attempts_count'])->toBe(0);
    expect($examData['attempts_exceeded'])->toBeFalse();
    expect($examData['retake_locked'])->toBeFalse();
    expect($examData['exam_link'])->not->toBeNull();
});

test('purchased catalogue details API returns live links, video files, and video links', function () {
    $user = User::factory()->create();

    $catalogue = Catalogue::create([
        'title' => 'Multimedia Course',
        'short_title' => 'MC',
        'price_regular' => 200.00,
        'service_type' => 'Course',
    ]);

    // Create live link, video file, and video link
    $liveLink = \App\Models\CatalogueLiveLink::create([
        'catalogue_id' => $catalogue->id,
        'link_title' => 'Zoom Session',
        'link_url' => 'https://zoom.us/j/123456',
    ]);

    $video = \App\Models\CatalogueVideo::create([
        'catalogue_id' => $catalogue->id,
        'video_title' => 'Introduction Video',
        'video_file' => 'uploads/videos/intro.mp4',
    ]);

    $videoLink = \App\Models\CatalogueVideoLink::create([
        'catalogue_id' => $catalogue->id,
        'video_link_title' => 'YouTube Tutorial',
        'video_link_url' => 'https://youtube.com/watch?v=xyz',
    ]);

    // Purchase the catalogue
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 200.00,
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");

    $response->assertStatus(200);
    $catalogueData = $response->json('data.catalogue');

    // Assert live links
    expect($catalogueData['live_links'])->toHaveCount(1);
    expect($catalogueData['live_links'][0]['link_title'])->toBe('Zoom Session');
    expect($catalogueData['live_links'][0]['link_url'])->toBe('https://zoom.us/j/123456');

    // Assert videos (video files)
    expect($catalogueData['video_files'])->toHaveCount(1);
    expect($catalogueData['video_files'][0]['video_title'])->toBe('Introduction Video');
    expect($catalogueData['video_files'][0]['video_file'])->toBe(asset('uploads/videos/intro.mp4'));

    // Assert video links
    expect($catalogueData['video_links'])->toHaveCount(1);
    expect($catalogueData['video_links'][0]['video_link_title'])->toBe('YouTube Tutorial');
    expect($catalogueData['video_links'][0]['video_link_url'])->toBe('https://youtube.com/watch?v=xyz');
});

test('dashboard stats API returns correct statistics', function () {
    $user = User::factory()->create();

    // Create 1 Course and 1 Certification
    $course = Catalogue::create([
        'title' => 'Course 1',
        'short_title' => 'C1',
        'price_regular' => 100.00,
        'service_type' => 'Course',
    ]);

    $certification = Catalogue::create([
        'title' => 'Certification 1',
        'short_title' => 'Cert1',
        'price_regular' => 150.00,
        'service_type' => 'Certification',
    ]);

    // Create exams for the Course and Certification
    $examCourse = CatalogueExam::create([
        'catalogue_id' => $course->id,
        'exam_title' => 'Course Exam',
        'is_premium' => true,
    ]);

    $examCert = CatalogueExam::create([
        'catalogue_id' => $certification->id,
        'exam_title' => 'Cert Exam',
        'is_premium' => true,
    ]);

    // Purchase both
    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $course->id,
        'amount' => 100.00,
        'payment_status' => 'paid',
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $certification->id,
        'amount' => 150.00,
        'payment_status' => 'paid',
    ]);

    // Request dashboard stats
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/dashboard-stats');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Dashboard statistics fetched successfully.',
            'data' => [
                'stats' => [
                    'active_courses' => 1,
                    'active_certification' => 1,
                    'completed_courses' => 0,
                    'exams_pending' => 2,
                    'ce_eligible_courses' => 0
                ]
            ]
        ]);

    // Complete the course exam
    UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $examCourse->id,
        'score' => 90.0,
        'percentage' => 90.0,
        'status' => 'passed',
        'duration' => '10 minutes',
        'classmarker_result_id' => 'res_1',
    ]);

    // Request dashboard stats again
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/dashboard-stats');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Dashboard statistics fetched successfully.',
            'data' => [
                'stats' => [
                    'active_courses' => 0, // course completed, so active course is 0
                    'active_certification' => 1,
                    'completed_courses' => 1,
                    'exams_pending' => 1,
                    'ce_eligible_courses' => 0
                ]
            ]
        ]);

    // Create a new course with 2 exams
    $course2 = Catalogue::create([
        'title' => 'Course 2',
        'short_title' => 'C2',
        'price_regular' => 120.00,
        'service_type' => 'Course',
    ]);

    $exam2a = CatalogueExam::create([
        'catalogue_id' => $course2->id,
        'exam_title' => 'Exam 2A',
        'is_premium' => true,
    ]);

    $exam2b = CatalogueExam::create([
        'catalogue_id' => $course2->id,
        'exam_title' => 'Exam 2B',
        'is_premium' => true,
    ]);

    Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $course2->id,
        'amount' => 120.00,
        'payment_status' => 'paid',
    ]);

    // Pass only 1 exam of course 2
    UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam2a->id,
        'score' => 95.0,
        'percentage' => 95.0,
        'status' => 'passed',
        'duration' => '15 minutes',
        'classmarker_result_id' => 'res_2a',
    ]);

    // Request stats: completed_courses should still be 1 (only Course 1 is completed)
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/dashboard-stats');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Dashboard statistics fetched successfully.',
            'data' => [
                'stats' => [
                    'active_courses' => 1, // Course 2 is active
                    'active_certification' => 1,
                    'completed_courses' => 1, // only Course 1 completed
                    'exams_pending' => 2, // examCert and exam2b are pending
                    'ce_eligible_courses' => 0
                ]
            ]
        ]);

    // Now pass the second exam of course 2
    UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam2b->id,
        'score' => 90.0,
        'percentage' => 90.0,
        'status' => 'passed',
        'duration' => '20 minutes',
        'classmarker_result_id' => 'res_2b',
    ]);

    // Request stats: completed_courses should now be 2
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/dashboard-stats');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Dashboard statistics fetched successfully.',
            'data' => [
                'stats' => [
                    'active_courses' => 0, // both completed
                    'active_certification' => 1,
                    'completed_courses' => 2,
                    'exams_pending' => 1, // only examCert is pending
                    'ce_eligible_courses' => 0
                ]
            ]
        ]);
});

test('catalogues menu API endpoint returns catalogues grouped for menu bar', function () {
    // Create a module under Healthcare Quality Improvement
    $m1 = Catalogue::create([
        'title' => 'Lean Healthcare',
        'short_title' => 'Lean',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Module',
        'healthcare_quality_improvement' => true,
        'patient_safety_risk_management' => false,
        'status' => 1,
    ]);

    // Create a module under Patient Safety & Risk Management
    $m2 = Catalogue::create([
        'title' => 'Root Cause Analysis',
        'short_title' => 'RCA',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Module',
        'healthcare_quality_improvement' => false,
        'patient_safety_risk_management' => true,
        'status' => 1,
    ]);

    // Create a Course
    $c = Catalogue::create([
        'title' => 'High-Reliability Healthcare Systems',
        'short_title' => 'HRHS',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Course',
        'status' => 1,
    ]);

    // Create a Toolkit
    $t = Catalogue::create([
        'title' => 'Hospital Quality Improvement Toolkit',
        'short_title' => 'HQIT',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Toolkit',
        'status' => 1,
    ]);

    // Create a Certification
    $cert = Catalogue::create([
        'title' => 'AIHQSP',
        'short_title' => 'AIHQSP',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    // Create a Webinar
    $web = Catalogue::create([
        'title' => 'Safety Webinar',
        'short_title' => 'SW',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Webinar',
        'status' => 1,
    ]);

    // Create a Workshop
    $work = Catalogue::create([
        'title' => 'Quality Workshop',
        'short_title' => 'QW',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Workshop',
        'status' => 1,
    ]);

    // Create an uncategorized Module
    $m3 = Catalogue::create([
        'title' => 'General Quality Module',
        'short_title' => 'GQM',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Module',
        'healthcare_quality_improvement' => false,
        'patient_safety_risk_management' => false,
        'status' => 1,
    ]);

    $response = $this->getJson('/api/catalogues/menu');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Menu catalogues fetched successfully.',
            'data' => [
                'modules' => [
                    'healthcare_quality_improvement' => [
                        [
                            'id' => $m1->id,
                            'title' => 'Lean Healthcare',
                            'name' => 'Lean Healthcare',
                            'details_file' => null,
                        ]
                    ],
                    'patient_safety_risk_management' => [
                        [
                            'id' => $m2->id,
                            'title' => 'Root Cause Analysis',
                            'name' => 'Root Cause Analysis',
                            'details_file' => null,
                        ]
                    ],
                    'others' => [
                        [
                            'id' => $m3->id,
                            'title' => 'General Quality Module',
                            'name' => 'General Quality Module',
                            'details_file' => null,
                        ]
                    ]
                ],
                'courses' => [
                    [
                        'id' => $c->id,
                        'title' => 'High-Reliability Healthcare Systems',
                        'name' => 'High-Reliability Healthcare Systems',
                        'details_file' => null,
                    ]
                ],
                'toolkits' => [
                    [
                        'id' => $t->id,
                        'title' => 'Hospital Quality Improvement Toolkit',
                        'name' => 'Hospital Quality Improvement Toolkit',
                        'details_file' => null,
                    ]
                ],
                'certifications' => [
                    [
                        'id' => $cert->id,
                        'title' => 'AIHQSP',
                        'name' => 'AIHQSP',
                        'details_file' => null,
                    ]
                ],
                'webinars' => [
                    [
                        'id' => $web->id,
                        'title' => 'Safety Webinar',
                        'name' => 'Safety Webinar',
                        'details_file' => null,
                    ]
                ],
                'workshops' => [
                    [
                        'id' => $work->id,
                        'title' => 'Quality Workshop',
                        'name' => 'Quality Workshop',
                        'details_file' => null,
                    ]
                ]
            ]
        ]);
});

test('single catalogues menu API endpoint returns details for a specific item', function () {
    $c = Catalogue::create([
        'title' => 'Single Menu Item Test',
        'short_title' => 'SMIT',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Course',
        'details_file' => 'uploads/development-catalogues/test.html',
        'story_guide_file' => 'uploads/development-catalogues/story.html',
        'status' => 1,
    ]);

    // Anonymous: marketing details only, the story guide is paid material.
    $response = $this->getJson("/api/catalogues/menu/{$c->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Menu catalogue details fetched successfully.',
            'data' => [
                'catalogue' => [
                    'id'               => $c->id,
                    'name'             => 'Single Menu Item Test',
                    'title'            => 'Single Menu Item Test',
                    'short_title'      => 'SMIT',
                    'details_file'     => asset('uploads/development-catalogues/test.html'),
                    'story_guide_file' => null,
                ]
            ]
        ]);

    // A user who owns the catalogue gets the story guide.
    $owner = User::factory()->create();
    Purchase::create([
        'user_id' => $owner->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $c->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
    ]);

    $this->actingAs($owner, 'api')
        ->getJson("/api/catalogues/menu/{$c->id}")
        ->assertStatus(200)
        ->assertJsonPath(
            'data.catalogue.story_guide_file',
            asset('uploads/development-catalogues/story.html')
        );

    // Test 404 for non-existent item
    $response404 = $this->getJson('/api/catalogues/menu/99999');
    $response404->assertStatus(404);
});

test('catalogues menu API endpoint supports filtering by service_type', function () {
    $cert = Catalogue::create([
        'title' => 'AIHQSP Certification',
        'short_title' => 'AIHQSP',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    $course = Catalogue::create([
        'title' => 'RCA Course',
        'short_title' => 'RCA',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Course',
        'status' => 1,
    ]);

    // Query only certifications
    $response = $this->getJson('/api/catalogues/menu?service_type=certification');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Menu catalogues fetched successfully.',
            'data' => [
                'certifications' => [
                    [
                        'id' => $cert->id,
                        'title' => 'AIHQSP Certification',
                    ]
                ]
            ]
        ]);

    // Ensure courses are NOT present in the filtered response
    $data = $response->json('data');
    expect($data)->not->toHaveKey('courses');
});

test('catalogues menu-without-certification API endpoint returns all categories except certifications', function () {
    $m1 = Catalogue::create([
        'title' => 'Lean Healthcare',
        'short_title' => 'LH',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Module',
        'healthcare_quality_improvement' => true,
        'patient_safety_risk_management' => false,
        'status' => 1,
    ]);

    $cert = Catalogue::create([
        'title' => 'AIHQSP',
        'short_title' => 'AIHQSP',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    $course = Catalogue::create([
        'title' => 'High-Reliability Course',
        'short_title' => 'HRC',
        'short_description' => 'Desc',
        'price_regular' => 50.00,
        'service_type' => 'Course',
        'status' => 1,
    ]);

    $response = $this->getJson('/api/catalogues/menu-without-certification');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Menu catalogues fetched successfully.',
            'data' => [
                'modules' => [
                    'healthcare_quality_improvement' => [
                        [
                            'id' => $m1->id,
                            'title' => 'Lean Healthcare',
                        ]
                    ]
                ],
                'courses' => [
                    [
                        'id' => $course->id,
                        'title' => 'High-Reliability Course',
                    ]
                ]
            ]
        ]);

    $data = $response->json('data');
    expect($data)->not->toHaveKey('certifications');
});

test('catalogues menu-without-certification API endpoint returns 404 when querying certification specifically', function () {
    $response = $this->getJson('/api/catalogues/menu-without-certification?service_type=certification');
    $response->assertStatus(404);
});
