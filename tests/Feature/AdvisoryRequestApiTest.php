<?php

use App\Models\User;
use App\Models\AdvisoryRequest;

test('getting advisory requests list requires authentication', function () {
    $response = $this->getJson('/api/get-advisory-request');

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Please login first',
        ]);
});

test('authenticated user receives 404 when they have no advisory requests', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/get-advisory-request');

    $response->assertStatus(404)
        ->assertJson([
            'status' => false,
            'message' => 'No advisory requests found.',
        ]);
});

test('authenticated user can successfully retrieve their advisory requests list', function () {
    $user = User::factory()->create(['email' => 'john@hospital.org']);

    // Request 1: Associated by user_id
    AdvisoryRequest::create([
        'user_id'              => $user->id,
        'organization_name'    => 'City General Hospital',
        'full_name'            => 'John Consultant',
        'work_email'           => 'john@hospital.org',
        'phone_number'         => '123456789',
        'country'              => 'USA',
        'organization_type'    => 'Hospital',
        'service_of_interest'  => 'Quality Assessment',
        'desired_timeline'     => '3 months',
        'description_of_needs' => 'We need quality assessment support.',
        'status'               => 'pending',
    ]);

    // Request 2: Associated by work_email (fallback)
    AdvisoryRequest::create([
        'user_id'              => null,
        'organization_name'    => 'City Health Care',
        'full_name'            => 'John Consultant',
        'work_email'           => 'john@hospital.org',
        'phone_number'         => '123456789',
        'country'              => 'USA',
        'organization_type'    => 'Clinic',
        'service_of_interest'  => 'Safety Audit',
        'desired_timeline'     => '1 month',
        'description_of_needs' => 'Safety audit needed.',
        'status'               => 'accepted',
        'admin_notes'          => 'Consultant assigned',
    ]);

    // Request 3: Another user's request
    AdvisoryRequest::create([
        'user_id'              => null,
        'organization_name'    => 'Other Org',
        'full_name'            => 'Other Person',
        'work_email'           => 'other@org.com',
        'phone_number'         => '987654321',
        'country'              => 'UK',
        'organization_type'    => 'Hospital',
        'service_of_interest'  => 'Accreditation Prep',
        'desired_timeline'     => '6 months',
        'description_of_needs' => 'Other needs.',
        'status'               => 'pending',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/get-advisory-request');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'advisory_requests' => [
                    '*' => [
                        'id',
                        'reference_number',
                        'organization_name',
                        'full_name',
                        'work_email',
                        'phone_number',
                        'country',
                        'organization_type',
                        'service_of_interest',
                        'desired_timeline',
                        'status',
                        'admin_notes',
                        'submission_date',
                        'created_at',
                    ]
                ]
            ]
        ])
        ->assertJsonCount(2, 'data.advisory_requests');

    $data = $response->json('data.advisory_requests');
    expect($data[0]['service_of_interest'])->toBe('Safety Audit');
    expect($data[0]['status'])->toBe('accepted');
    expect($data[0]['admin_notes'])->toBe('Consultant assigned');
    expect($data[1]['service_of_interest'])->toBe('Quality Assessment');
});

test('getting individual advisory request details requires authentication', function () {
    $response = $this->getJson('/api/get-advisory-request/1');

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Please login first',
        ]);
});

test('authenticated user receives 404 when individual advisory request does not exist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/get-advisory-request/99999');

    $response->assertStatus(404)
        ->assertJson([
            'status' => false,
            'message' => 'Advisory request not found.',
        ]);
});

test('authenticated user receives 403 when trying to access another user\'s advisory request', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    $advisoryRequest = AdvisoryRequest::create([
        'user_id'              => $user2->id,
        'organization_name'    => 'Org Two',
        'full_name'            => 'User Two',
        'work_email'           => 'user2@example.com',
        'phone_number'         => '123456789',
        'country'              => 'USA',
        'organization_type'    => 'Hospital',
        'service_of_interest'  => 'Consulting',
        'desired_timeline'     => 'Immediate',
        'description_of_needs' => 'Needs help.',
        'status'               => 'pending',
    ]);

    $response = $this->actingAs($user1, 'api')
        ->getJson('/api/get-advisory-request/' . $advisoryRequest->id);

    $response->assertStatus(403)
        ->assertJson([
            'status' => false,
            'message' => 'Unauthorized access to this advisory request.',
        ]);
});

test('authenticated user can successfully retrieve their own individual advisory request details', function () {
    $user = User::factory()->create(['email' => 'john@hospital.org']);

    $advisoryRequest = AdvisoryRequest::create([
        'user_id'              => $user->id,
        'organization_name'    => 'City General Hospital',
        'full_name'            => 'John Consultant',
        'work_email'           => 'john@hospital.org',
        'phone_number'         => '123456789',
        'country'              => 'USA',
        'organization_type'    => 'Hospital',
        'service_of_interest'  => 'Quality Assessment',
        'desired_timeline'     => '3 months',
        'description_of_needs' => 'We need quality assessment support.',
        'status'               => 'accepted',
        'admin_notes'          => 'Approved by admin',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/get-advisory-request/' . $advisoryRequest->id);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'advisory_request' => [
                    'id',
                    'reference_number',
                    'organization_name',
                    'full_name',
                    'work_email',
                    'phone_number',
                    'country',
                    'organization_type',
                    'service_of_interest',
                    'desired_timeline',
                    'description_of_needs',
                    'status',
                    'admin_notes',
                    'submission_date',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);

    expect($response->json('data.advisory_request.service_of_interest'))->toBe('Quality Assessment');
    expect($response->json('data.advisory_request.status'))->toBe('accepted');
    expect($response->json('data.advisory_request.admin_notes'))->toBe('Approved by admin');
});
