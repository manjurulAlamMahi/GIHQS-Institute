<?php

use App\Models\User;
use App\Models\Catalogue;
use App\Models\CatalogueExam;
use App\Models\CertificationApplication;
use App\Models\UserExamResult;
use App\Models\UserExamOverride;
use App\Models\Purchase;
use Carbon\Carbon;

test('admin can submit an override settings form for a user and exam', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);
    $catalogue = Catalogue::create([
        'title' => 'Admin Override Catalogue',
        'catalogue_type' => 'paid',
        'service_type' => 'Certification',
        'price_regular' => 99.00,
        'price_final' => 99.00,
        'status' => 1,
    ]);
    
    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Admin Override Exam',
        'exam_link' => 'https://example.com/exam',
        'is_premium' => 0,
    ]);

    $application = CertificationApplication::create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => $user->email,
        'phone' => '123456789',
        'country' => 'USA',
        'city' => 'New York',
        'current_job_title' => 'Doctor',
        'organization' => 'General Hospital',
        'years_of_experience' => '5-10',
        'primary_area_of_experience' => 'Quality Care',
        'professional_role' => 'Manager',
        'catalogue_id' => $catalogue->id,
        'status' => 'accepted',
    ]);

    // Create a purchase record so the validation check passes
    Purchase::create([
        'user_id' => $user->id,
        'catalogue_id' => $catalogue->id,
        'purchase_type' => 'catalogue',
        'payment_status' => 'paid',
        'amount' => 99.00,
    ]);

    $response = $this
        ->actingAs($admin)
        ->post('/admin/user-exam-overrides', [
            'user_id' => $user->id,
            'application_id' => $application->id,
            'submit_exam_id' => $exam->id,
            'overrides' => [
                $exam->id => [
                    'max_attempts' => '3',
                    'retake_eligible_date' => '2026-12-25',
                ]
            ]
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $override = UserExamOverride::where('user_id', $user->id)
        ->where('catalogue_exam_id', $exam->id)
        ->first();

    expect($override)->not->toBeNull();
    expect($override->max_attempts)->toBe(3);
    expect($override->retake_eligible_date)->toBe('2026-12-25');
    expect($override->ignore_cooldown)->toBeFalse();
});

test('ignore cooldown bypasses lockout and allows retaking the exam', function () {
    $user = User::factory()->create(['role' => 'user']);
    $catalogue = Catalogue::create([
        'title' => 'Bypass Cooldown Catalogue',
        'catalogue_type' => 'paid',
        'service_type' => 'Certification',
        'price_regular' => 99.00,
        'price_final' => 99.00,
        'status' => 1,
    ]);
    
    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title' => 'Bypass Cooldown Exam',
        'exam_link' => 'https://example.com/exam',
        'is_premium' => 0,
    ]);

    // Create a purchase record so user can access the purchased catalogue details
    Purchase::create([
        'user_id' => $user->id,
        'catalogue_id' => $catalogue->id,
        'purchase_type' => 'catalogue',
        'payment_status' => 'paid',
        'amount' => 99.00,
    ]);

    // Create a failed exam result
    $result = UserExamResult::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam->id,
        'status' => 'failed',
        'score' => 50,
        'created_at' => Carbon::now()->subDays(5),
    ]);

    // Without override, the lockout is active (cooldown is 3 months)
    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");

    $response->assertOk();
    $exams = $response->json('data.catalogue.exams');
    expect($exams[0]['retake_locked'])->toBeTrue();
    expect($exams[0]['exam_link'])->toBeNull();

    // Create override to ignore cooldown and set max attempts override to 3
    UserExamOverride::create([
        'user_id' => $user->id,
        'catalogue_exam_id' => $exam->id,
        'max_attempts' => 3,
        'ignore_cooldown' => true,
    ]);

    // With override, lockout should be disabled and link should be available
    $response = $this
        ->actingAs($user, 'api')
        ->getJson("/api/profile/purchased-catalogues/{$catalogue->id}");

    $response->assertOk();
    $exams = $response->json('data.catalogue.exams');
    expect($exams[0]['retake_locked'])->toBeFalse();
    expect($exams[0]['exam_link'])->not->toBeNull();
});
