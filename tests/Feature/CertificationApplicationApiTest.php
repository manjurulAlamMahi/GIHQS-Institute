<?php

use App\Models\User;
use App\Models\Catalogue;
use App\Models\CertificationApplication;

test('getting certification applications list requires authentication', function () {
    $response = $this->getJson('/api/apply-for-certification');

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Please login first',
        ]);
});

test('authenticated user receives 404 when they have no certification applications', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/apply-for-certification');

    $response->assertStatus(404)
        ->assertJson([
            'status' => false,
            'message' => 'No certification applications found.',
        ]);
});

test('authenticated user can successfully retrieve their certification applications list', function () {
    $user = User::factory()->create(['email' => 'john.doe@example.com']);

    $catalogue1 = Catalogue::create([
        'title' => 'Healthcare Quality Certification',
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    $catalogue2 = Catalogue::create([
        'title' => 'Patient Safety Certification',
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    // Application 1: Associated by user_id
    CertificationApplication::create([
        'user_id'                    => $user->id,
        'first_name'                 => 'John',
        'last_name'                  => 'Doe',
        'email'                      => 'john.doe@example.com',
        'phone'                      => '123456789',
        'country'                    => 'USA',
        'city'                       => 'New York',
        'current_job_title'          => 'Quality Officer',
        'organization'               => 'General Hospital',
        'years_of_experience'        => '5-10',
        'primary_area_of_experience' => 'Quality Management',
        'professional_role'          => 'Manager',
        'catalogue_id'               => $catalogue1->id,
        'confirm_accuracy'           => true,
        'agree_policies'             => true,
        'status'                     => 'pending',
    ]);

    // Application 2: Associated by email (fallback)
    CertificationApplication::create([
        'user_id'                    => null,
        'first_name'                 => 'John',
        'last_name'                  => 'Doe',
        'email'                      => 'john.doe@example.com',
        'phone'                      => '123456789',
        'country'                    => 'USA',
        'city'                       => 'New York',
        'current_job_title'          => 'Quality Specialist',
        'organization'               => 'City Hospital',
        'years_of_experience'        => '3-5',
        'primary_area_of_experience' => 'Patient Safety',
        'professional_role'          => 'Specialist',
        'catalogue_id'               => $catalogue2->id,
        'confirm_accuracy'           => true,
        'agree_policies'             => true,
        'status'                     => 'accepted',
        'admin_notes'                => 'Approved by committee',
    ]);

    // Application 3: Another user's application (should not be returned)
    CertificationApplication::create([
        'user_id'                    => null,
        'first_name'                 => 'Jane',
        'last_name'                  => 'Smith',
        'email'                      => 'jane.smith@example.com',
        'phone'                      => '987654321',
        'country'                    => 'UK',
        'city'                       => 'London',
        'current_job_title'          => 'Doctor',
        'organization'               => 'London Clinic',
        'years_of_experience'        => '10+',
        'primary_area_of_experience' => 'Medicine',
        'professional_role'          => 'Lead',
        'catalogue_id'               => $catalogue1->id,
        'confirm_accuracy'           => true,
        'agree_policies'             => true,
        'status'                     => 'pending',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/apply-for-certification');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'applications' => [
                    '*' => [
                        'id',
                        'reference_number',
                        'catalogue_id',
                        'certification_title',
                        'first_name',
                        'last_name',
                        'applicant_name',
                        'email',
                        'phone',
                        'organization',
                        'status',
                        'admin_notes',
                        'submission_date',
                        'created_at',
                    ]
                ]
            ]
        ])
        ->assertJsonCount(2, 'data.applications');

    $data = $response->json('data.applications');
    expect($data[0]['certification_title'])->toBe('Patient Safety Certification');
    expect($data[0]['status'])->toBe('accepted');
    expect($data[0]['admin_notes'])->toBe('Approved by committee');
    expect($data[1]['certification_title'])->toBe('Healthcare Quality Certification');
    expect($data[1]['status'])->toBe('pending');
});

test('getting individual certification application details requires authentication', function () {
    $response = $this->getJson('/api/apply-for-certification/1');

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Please login first',
        ]);
});

test('authenticated user receives 404 when individual certification application does not exist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/apply-for-certification/99999');

    $response->assertStatus(404)
        ->assertJson([
            'status' => false,
            'message' => 'Certification application not found.',
        ]);
});

test('authenticated user receives 403 when trying to access another user\'s certification application', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    $catalogue = Catalogue::create([
        'title' => 'Quality Certification',
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    $application = CertificationApplication::create([
        'user_id'                    => $user2->id,
        'first_name'                 => 'User',
        'last_name'                  => 'Two',
        'email'                      => 'user2@example.com',
        'phone'                      => '123456789',
        'country'                    => 'USA',
        'city'                       => 'New York',
        'current_job_title'          => 'Manager',
        'organization'               => 'Org',
        'years_of_experience'        => '5',
        'primary_area_of_experience' => 'Quality',
        'professional_role'          => 'Role',
        'catalogue_id'               => $catalogue->id,
        'confirm_accuracy'           => true,
        'agree_policies'             => true,
        'status'                     => 'pending',
    ]);

    $response = $this->actingAs($user1, 'api')
        ->getJson('/api/apply-for-certification/' . $application->id);

    $response->assertStatus(403)
        ->assertJson([
            'status' => false,
            'message' => 'Unauthorized access to this application.',
        ]);
});

test('authenticated user can successfully retrieve their own individual certification application details', function () {
    $user = User::factory()->create(['email' => 'john.doe@example.com']);

    $catalogue = Catalogue::create([
        'title' => 'Healthcare Quality Certification',
        'service_type' => 'Certification',
        'status' => 1,
    ]);

    $application = CertificationApplication::create([
        'user_id'                    => $user->id,
        'first_name'                 => 'John',
        'last_name'                  => 'Doe',
        'email'                      => 'john.doe@example.com',
        'phone'                      => '123456789',
        'country'                    => 'USA',
        'city'                       => 'New York',
        'current_job_title'          => 'Quality Officer',
        'organization'               => 'General Hospital',
        'years_of_experience'        => '5-10',
        'primary_area_of_experience' => 'Quality Management',
        'professional_role'          => 'Manager',
        'catalogue_id'               => $catalogue->id,
        'confirm_accuracy'           => true,
        'agree_policies'             => true,
        'status'                     => 'accepted',
        'admin_notes'                => 'Application approved',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/apply-for-certification/' . $application->id);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'application' => [
                    'id',
                    'reference_number',
                    'first_name',
                    'last_name',
                    'applicant_name',
                    'email',
                    'phone',
                    'country',
                    'city',
                    'current_job_title',
                    'organization',
                    'linkedin_profile',
                    'years_of_experience',
                    'primary_area_of_experience',
                    'professional_role',
                    'resume_cv',
                    'catalogue_id',
                    'certification_title',
                    'confirm_accuracy',
                    'agree_policies',
                    'status',
                    'admin_notes',
                    'submission_date',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);

    expect($response->json('data.application.certification_title'))->toBe('Healthcare Quality Certification');
    expect($response->json('data.application.status'))->toBe('accepted');
    expect($response->json('data.application.admin_notes'))->toBe('Application approved');
});
