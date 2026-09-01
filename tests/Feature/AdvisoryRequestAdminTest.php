<?php

use App\Models\User;
use App\Models\AdvisoryRequest;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $this->request = AdvisoryRequest::create([
        'organization_name' => 'Health Corp',
        'full_name' => 'Jane Doe',
        'work_email' => 'jane.doe@example.com',
        'phone_number' => '0987654321',
        'country' => 'Canada',
        'organization_type' => 'Hospital',
        'service_of_interest' => 'Consulting',
        'desired_timeline' => 'Immediate',
        'description_of_needs' => 'We need urgent healthcare consulting.',
        'status' => 'pending',
    ]);
});

it('can render advisory requests index page for admin', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.advisory-requests.index'));

    $response->assertStatus(200);
});

it('can fetch advisory requests ajax request', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.advisory-requests.index'), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['data']);
});

it('can render show advisory request details page', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.advisory-requests.show', $this->request->id));

    $response->assertStatus(200);
    $response->assertSee('Health Corp');
    $response->assertSee('Jane Doe');
});

it('can update advisory request status and notes', function () {
    $response = $this->actingAs($this->admin)->patch(route('admin.advisory-requests.update-status', $this->request->id), [
        'status' => 'accepted',
        'admin_notes' => 'Approved consulting request',
    ]);

    $response->assertRedirect();
    
    $this->request->refresh();
    expect($this->request->status)->toBe('accepted');
    expect($this->request->admin_notes)->toBe('Approved consulting request');
});

it('can delete an advisory request', function () {
    $response = $this->actingAs($this->admin)->delete(route('admin.advisory-requests.destroy', $this->request->id));

    $response->assertRedirect();
    $this->assertSoftDeleted($this->request);
});
