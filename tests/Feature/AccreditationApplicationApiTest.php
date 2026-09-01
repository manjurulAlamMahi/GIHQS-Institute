<?php

use App\Models\User;
use App\Models\AccreditationApplication;

test('submitting accreditation application requires authentication', function () {
    $response = $this->postJson('/api/apply-accreditation', [
        'applicant_category' => 'University',
        'applicant_name' => 'Harvard University',
        'country' => 'USA',
        'city' => 'Cambridge',
        'program_name' => 'MD Program',
        'program_type' => 'Medical Degree',
        'program_delivery_format' => 'On Campus',
        'primary_contact_person' => 'Dr. Smith',
        'contact_title_position' => 'Dean',
        'email_address' => 'dean@harvard.edu',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Please login first',
        ]);
});

test('authenticated user can submit accreditation application successfully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/apply-accreditation', [
            'applicant_category' => 'University',
            'applicant_name' => 'Harvard University',
            'country' => 'USA',
            'city' => 'Cambridge',
            'program_name' => 'MD Program',
            'program_type' => 'Medical Degree',
            'program_delivery_format' => 'On Campus',
            'primary_contact_person' => 'Dr. Smith',
            'contact_title_position' => 'Dean',
            'email_address' => 'dean@harvard.edu',
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment([
            'applicant_category' => 'University',
            'applicant_name' => 'Harvard University',
            'country' => 'USA',
            'city' => 'Cambridge',
            'program_name' => 'MD Program',
            'program_type' => 'Medical Degree',
            'program_delivery_format' => 'On Campus',
            'primary_contact_person' => 'Dr. Smith',
            'contact_title_position' => 'Dean',
            'email_address' => 'dean@harvard.edu',
            'status' => 'pending',
        ]);

    $this->assertDatabaseHas('accreditation_applications', [
        'applicant_category' => 'University',
        'applicant_name' => 'Harvard University',
        'email_address' => 'dean@harvard.edu',
    ]);
});

test('getting accreditation applications list requires authentication', function () {
    $response = $this->getJson('/api/apply-accreditation');

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Please login first',
        ]);
});

test('authenticated user receives 404 when they have no accreditation applications', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/apply-accreditation');

    $response->assertStatus(404)
        ->assertJson([
            'status' => false,
            'message' => 'No accreditation applications found.',
        ]);
});

test('authenticated user can successfully retrieve their accreditation applications list', function () {
    $user = User::factory()->create(['email' => 'dean@harvard.edu']);

    // Application 1: Associated by user_id
    AccreditationApplication::create([
        'user_id' => $user->id,
        'applicant_category' => 'University',
        'applicant_name' => 'Harvard University',
        'country' => 'USA',
        'city' => 'Cambridge',
        'program_name' => 'MD Program',
        'program_type' => 'Medical Degree',
        'program_delivery_format' => 'On Campus',
        'primary_contact_person' => 'Dr. Smith',
        'contact_title_position' => 'Dean',
        'email_address' => 'dean@harvard.edu',
    ]);

    // Application 2: Associated by email_address (fallback support)
    AccreditationApplication::create([
        'user_id' => null,
        'applicant_category' => 'Institute',
        'applicant_name' => 'Harvard Research Lab',
        'country' => 'USA',
        'city' => 'Cambridge',
        'program_name' => 'Fellowship Program',
        'program_type' => 'PostDoc',
        'program_delivery_format' => 'Hybrid',
        'primary_contact_person' => 'Dr. Smith',
        'contact_title_position' => 'Director',
        'email_address' => 'dean@harvard.edu',
    ]);

    // Application 3: Another user's application (should not be returned)
    AccreditationApplication::create([
        'user_id' => null,
        'applicant_category' => 'College',
        'applicant_name' => 'Other College',
        'country' => 'UK',
        'city' => 'London',
        'program_name' => 'BA Program',
        'program_type' => 'Arts',
        'program_delivery_format' => 'Online',
        'primary_contact_person' => 'Jane Doe',
        'contact_title_position' => 'Dean',
        'email_address' => 'other@college.com',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/apply-accreditation');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'applications' => [
                    '*' => [
                        'id',
                        'reference_number',
                        'program_name',
                        'submission_date',
                        'status',
                        'admin_notes',
                        'created_at',
                    ]
                ]
            ]
        ])
        ->assertJsonCount(2, 'data.applications');

    // Confirm that the returned items belong to the logged-in user
    $data = $response->json('data.applications');
    expect($data[0]['program_name'])->toBe('Fellowship Program'); // Latest first
    expect($data[1]['program_name'])->toBe('MD Program');
});

test('getting individual accreditation application details requires authentication', function () {
    $response = $this->getJson('/api/apply-accreditation/1');

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Please login first',
        ]);
});

test('authenticated user receives 404 when individual accreditation application does not exist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/apply-accreditation/99999');

    $response->assertStatus(404)
        ->assertJson([
            'status' => false,
            'message' => 'Accreditation application not found.',
        ]);
});

test('authenticated user receives 403 when trying to access another user\'s accreditation application', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    // Application belonging to user2
    $application = AccreditationApplication::create([
        'user_id' => $user2->id,
        'applicant_category' => 'University',
        'applicant_name' => 'Harvard University',
        'country' => 'USA',
        'city' => 'Cambridge',
        'program_name' => 'MD Program',
        'program_type' => 'Medical Degree',
        'program_delivery_format' => 'On Campus',
        'primary_contact_person' => 'Dr. Smith',
        'contact_title_position' => 'Dean',
        'email_address' => 'user2@example.com',
    ]);

    $response = $this->actingAs($user1, 'api')
        ->getJson('/api/apply-accreditation/' . $application->id);

    $response->assertStatus(403)
        ->assertJson([
            'status' => false,
            'message' => 'Unauthorized access to this application.',
        ]);
});

test('authenticated user can successfully retrieve their own individual accreditation application details', function () {
    $user = User::factory()->create(['email' => 'dean@harvard.edu']);

    $application = AccreditationApplication::create([
        'user_id' => $user->id,
        'applicant_category' => 'University',
        'applicant_name' => 'Harvard University',
        'country' => 'USA',
        'city' => 'Cambridge',
        'program_name' => 'MD Program',
        'program_type' => 'Medical Degree',
        'program_delivery_format' => 'On Campus',
        'primary_contact_person' => 'Dr. Smith',
        'contact_title_position' => 'Dean',
        'email_address' => 'dean@harvard.edu',
        'program_overview_doc' => 'accreditation-docs/overview.pdf',
        'governance_policy_doc' => 'accreditation-docs/policy.pdf',
        'additional_information' => 'Some details',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/apply-accreditation/' . $application->id);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'application' => [
                    'id',
                    'reference_number',
                    'applicant_category',
                    'applicant_name',
                    'department_division',
                    'country',
                    'city',
                    'website_url',
                    'year_established',
                    'program_name',
                    'program_type',
                    'program_delivery_format',
                    'estimated_annual_participants',
                    'primary_language_of_instruction',
                    'program_launch_date',
                    'primary_contact_person',
                    'contact_title_position',
                    'email_address',
                    'phone_number',
                    'program_overview_doc',
                    'governance_policy_doc',
                    'additional_information',
                    'status',
                    'admin_notes',
                    'submission_date',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);

    expect($response->json('data.application.program_name'))->toBe('MD Program');
    expect($response->json('data.application.program_overview_doc'))->toBe(asset('accreditation-docs/overview.pdf'));
});
