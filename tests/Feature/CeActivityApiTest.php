<?php

use App\Models\User;
use App\Models\Catalogue;
use App\Models\CeActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('fetching ce activities requires authentication', function () {
    $response = $this->getJson('/api/profile/ce-activities');
    $response->assertStatus(401);
});

test('storing ce activity requires authentication', function () {
    $response = $this->postJson('/api/profile/ce-activities', []);
    $response->assertStatus(401);
});

test('storing ce activity fails validation if required fields are missing', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/profile/ce-activities', []);

    $response->assertStatus(422)
        ->assertJsonFragment([
            'catalogue_id' => ['The catalogue id field is required.'],
            'domain' => ['The domain field is required.'],
            'activity_type' => ['The activity type field is required.'],
            'activity_title' => ['The activity title field is required.'],
            'provider' => ['The provider field is required.'],
            'completion_date' => ['The completion date field is required.'],
            'credits_earned' => ['The credits earned field is required.'],
        ]);
});

test('storing ce activity fails if selected catalogue is not a certification', function () {
    $user = User::factory()->create();

    // Create a catalogue of type Course
    $courseCatalogue = Catalogue::create([
        'title' => 'RCA Course',
        'short_title' => 'RCA',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/profile/ce-activities', [
            'catalogue_id'    => $courseCatalogue->id,
            'domain'          => 'Patient Safety & Risk Management',
            'activity_type'   => 'GIHQS Course',
            'activity_title'  => 'Root Cause Analysis in Healthcare',
            'provider'        => 'GIHQS',
            'completion_date' => '2026-02-14',
            'credits_earned'  => 5.0,
        ]);

    $response->assertStatus(422)
        ->assertJsonFragment([
            'message' => 'Selected catalogue item is not a valid certification.'
        ]);
});

test('user can successfully store a ce activity with file upload', function () {
    $user = User::factory()->create();

    // Create a certification catalogue
    $certification = Catalogue::create([
        'title' => 'AIHQSP — AI Healthcare Quality & Safety Professional',
        'short_title' => 'AIHQSP',
        'price_regular' => 45.00,
        'service_type' => 'Certification',
    ]);

    // Mock evidence file
    $file = UploadedFile::fake()->create('evidence.pdf', 500, 'application/pdf');

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/profile/ce-activities', [
            'catalogue_id'    => $certification->id,
            'domain'          => 'Patient Safety & Risk Management',
            'activity_type'   => 'GIHQS Course',
            'activity_title'  => 'Root Cause Analysis in Healthcare',
            'provider'        => 'GIHQS',
            'completion_date' => '2026-02-14',
            'credits_earned'  => 5.5,
            'evidence_file'   => $file,
            'description'     => 'Completed Root Cause Analysis training course.',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'ce_activity' => [
                    'id',
                    'catalogue_id',
                    'certification',
                    'certification_short',
                    'domain',
                    'activity_type',
                    'activity_title',
                    'provider',
                    'completion_date',
                    'credits_earned',
                    'evidence_file',
                    'description',
                    'status',
                    'created_at',
                ]
            ]
        ])
        ->assertJsonFragment([
            'certification'       => 'AIHQSP — AI Healthcare Quality & Safety Professional',
            'certification_short' => 'AIHQSP',
            'domain'              => 'Patient Safety & Risk Management',
            'activity_type'       => 'GIHQS Course',
            'activity_title'      => 'Root Cause Analysis in Healthcare',
            'provider'            => 'GIHQS',
            'completion_date'     => '2026-02-14',
            'credits_earned'      => 5.5,
            'description'         => 'Completed Root Cause Analysis training course.',
            'status'              => 'pending',
        ]);

    // Assert database has record
    $this->assertDatabaseHas('ce_activities', [
        'user_id'         => $user->id,
        'catalogue_id'    => $certification->id,
        'activity_title'  => 'Root Cause Analysis in Healthcare',
        'status'          => 'pending',
    ]);

    // Clean up uploaded file
    $activity = CeActivity::first();
    if ($activity->evidence_file && file_exists(public_path($activity->evidence_file))) {
        unlink(public_path($activity->evidence_file));
    }
});

test('user can fetch their ce activity history', function () {
    $user = User::factory()->create();

    // Create a certification catalogue
    $certification = Catalogue::create([
        'title' => 'AIHQSP — AI Healthcare Quality & Safety Professional',
        'short_title' => 'AIHQSP',
        'price_regular' => 45.00,
        'service_type' => 'Certification',
    ]);

    // Seed direct CE activity
    $activity = CeActivity::create([
        'user_id'         => $user->id,
        'catalogue_id'    => $certification->id,
        'domain'          => 'Patient Safety & Risk Management',
        'activity_type'   => 'GIHQS Course',
        'activity_title'  => 'Root Cause Analysis in Healthcare',
        'provider'        => 'GIHQS',
        'completion_date' => '2026-02-14',
        'credits_earned'  => 5.5,
        'evidence_file'   => 'uploads/ce-activities/test.pdf',
        'description'     => 'Some notes.',
        'status'          => 'pending',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/ce-activities');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'activities' => [
                    '*' => [
                        'id',
                        'catalogue_id',
                        'certification',
                        'certification_short',
                        'domain',
                        'activity_type',
                        'activity_title',
                        'provider',
                        'completion_date',
                        'credits_earned',
                        'evidence_file',
                        'description',
                        'status',
                        'created_at',
                    ]
                ]
            ]
        ])
        ->assertJsonFragment([
            'activity_title' => 'Root Cause Analysis in Healthcare',
            'status'         => 'pending',
        ]);
});

test('fetching history returns 404 if no ce activities logged', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/ce-activities');

    $response->assertStatus(404)
        ->assertJsonFragment([
            'message' => 'No CE activities found.'
        ]);
});

test('fetching ce tracking requires authentication', function () {
    $response = $this->getJson('/api/profile/ce-activities/tracking');
    $response->assertStatus(401);
});

test('user can fetch ce tracking of purchased certifications with correct math and dates', function () {
    $user = User::factory()->create();

    $certification = Catalogue::create([
        'title' => 'AI Healthcare Quality & Safety Professional (AIHQSP)',
        'short_title' => 'AIHQSP',
        'price_regular' => 100.00,
        'service_type' => 'Certification',
        'ce_credit_total_required' => 30.00,
        'validity_years' => 2,
    ]);

    // Create a paid purchase for the certification (created in year 2025)
    $purchase = \App\Models\Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $certification->id,
        'amount' => 100.00,
        'payment_status' => 'paid',
    ]);
    $purchase->created_at = \Carbon\Carbon::create(2025, 5, 10);
    $purchase->save();

    // Create an associated exam and a passed exam result
    $exam = \App\Models\CatalogueExam::create([
        'catalogue_id' => $certification->id,
        'exam_title' => 'AIHQSP Exam',
        'exam_link' => 'https://example.com/exam',
        'is_premium' => true,
    ]);

    \App\Models\UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam->id,
        'score' => 90.00,
        'points_available' => 100.00,
        'percentage' => 90.00,
        'percentage_passmark' => 80.00,
        'status' => 'passed',
        'start_time' => '2025-06-15 09:00:00',
        'end_time' => '2025-06-15 10:00:00',
    ]);

    // Approved activity (should count)
    CeActivity::create([
        'user_id' => $user->id,
        'catalogue_id' => $certification->id,
        'domain' => 'Domain 1',
        'activity_type' => 'Type 1',
        'activity_title' => 'Title 1',
        'provider' => 'Provider 1',
        'completion_date' => '2025-06-10',
        'credits_earned' => 3.0,
        'status' => 'approved',
    ]);

    // Pending activity (should NOT count)
    CeActivity::create([
        'user_id' => $user->id,
        'catalogue_id' => $certification->id,
        'domain' => 'Domain 1',
        'activity_type' => 'Type 1',
        'activity_title' => 'Title 2',
        'provider' => 'Provider 1',
        'completion_date' => '2025-07-10',
        'credits_earned' => 5.0,
        'status' => 'pending',
    ]);

    // Rejected activity (should NOT count)
    CeActivity::create([
        'user_id' => $user->id,
        'catalogue_id' => $certification->id,
        'domain' => 'Domain 1',
        'activity_type' => 'Type 1',
        'activity_title' => 'Title 3',
        'provider' => 'Provider 1',
        'completion_date' => '2025-08-10',
        'credits_earned' => 10.0,
        'status' => 'rejected',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/ce-activities/tracking');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'trackings' => [
                    '*' => [
                        'catalogue_id',
                        'certification_title',
                        'certification_short',
                        'required_credits',
                        'completed_credits',
                        'renewal_date',
                        'expiration_date',
                        'ce_window',
                        'submission_due',
                    ]
                ]
            ]
        ])
        ->assertJsonFragment([
            'catalogue_id'        => $certification->id,
            'certification_title' => 'AI Healthcare Quality & Safety Professional (AIHQSP)',
            'certification_short' => 'AIHQSP',
            'required_credits'    => 30.0,
            'completed_credits'   => 3.0,
            'renewal_date'        => '2025-06-15',
            'expiration_date'     => '2027-06-15',
            'ce_window'           => 'Jun 15, 2025 -> Jun 15, 2027',
            'submission_due'      => '2027-05-16',
        ]);
});

test('fetching ce tracking returns 404 if no certifications purchased', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/ce-activities/tracking');

    $response->assertStatus(404)
        ->assertJsonFragment([
            'message' => 'No active certification trackings found.'
        ]);
});
