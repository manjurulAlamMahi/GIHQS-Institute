<?php

use App\Models\User;
use App\Models\Catalogue;
use App\Models\CertificationApplication;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $this->catalogue = Catalogue::create([
        'title' => 'Test Certification Program',
        'service_type' => 'Certification',
        'status' => 1,
        'description' => 'Description test',
    ]);

    $this->application = CertificationApplication::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '1234567890',
        'country' => 'USA',
        'city' => 'New York',
        'current_job_title' => 'Developer',
        'organization' => 'Tech Corp',
        'years_of_experience' => '3-5',
        'primary_area_of_experience' => 'PHP',
        'professional_role' => 'Backend',
        'catalogue_id' => $this->catalogue->id,
        'confirm_accuracy' => true,
        'agree_policies' => true,
        'status' => 'pending',
    ]);
});

it('can render certification applications index page for admin', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.certification-applications.index'));

    $response->assertStatus(200);
});

it('can fetch certification applications ajax request', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.certification-applications.index'), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['data']);
});

it('can render show application details page', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.certification-applications.show', $this->application->id));

    $response->assertStatus(200);
    $response->assertSee('John Doe');
});

it('can update certification application status and notes', function () {
    $response = $this->actingAs($this->admin)->patch(route('admin.certification-applications.update-status', $this->application->id), [
        'status' => 'accepted',
        'admin_notes' => 'Notes approved',
    ]);

    $response->assertRedirect();
    
    $this->application->refresh();
    expect($this->application->status)->toBe('accepted');
    expect($this->application->admin_notes)->toBe('Notes approved');
});

it('can delete a certification application', function () {
    $response = $this->actingAs($this->admin)->delete(route('admin.certification-applications.destroy', $this->application->id));

    $response->assertRedirect();
    $this->assertSoftDeleted($this->application);
});
