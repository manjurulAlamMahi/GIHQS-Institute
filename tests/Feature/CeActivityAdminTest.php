<?php

use App\Models\User;
use App\Models\Catalogue;
use App\Models\CeActivity;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $this->user = User::factory()->create([
        'role' => 'user',
        'email_verified_at' => now(),
    ]);

    $this->certification = Catalogue::create([
        'title' => 'AIHQSP Certification',
        'short_title' => 'AIHQSP',
        'price_regular' => 100.00,
        'service_type' => 'Certification',
    ]);

    $this->activity = CeActivity::create([
        'user_id'         => $this->user->id,
        'catalogue_id'    => $this->certification->id,
        'domain'          => 'Patient Safety',
        'activity_type'   => 'Course',
        'activity_title'  => 'RCA Training',
        'provider'        => 'GIHQS',
        'completion_date' => '2026-02-14',
        'credits_earned'  => 4.5,
        'description'     => 'Completed training.',
        'status'          => 'pending',
    ]);
});

test('guest or non-admin user cannot access admin ce activities pages', function () {
    // Guest redirect to login
    $responseGuest = $this->get(route('admin.ce-activities.index'));
    $responseGuest->assertRedirect('/login');

    // Non-admin (regular user) gets 403 Forbidden
    $responseUser = $this->actingAs($this->user)->get(route('admin.ce-activities.index'));
    $responseUser->assertStatus(403);
});

test('admin can view ce activities list page and load datatable ajax', function () {
    // Load standard page
    $response = $this->actingAs($this->admin)->get(route('admin.ce-activities.index'));
    $response->assertStatus(200)
        ->assertViewIs('backend.layouts.ce_activities.index');

    // Load AJAX DataTables
    $responseAjax = $this->actingAs($this->admin)->get(route('admin.ce-activities.index'), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);
    $responseAjax->assertStatus(200)
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data'
        ])
        ->assertJsonFragment([
            'activity_title' => 'RCA Training',
            'domain'         => 'Patient Safety',
        ]);
});

test('admin can view ce activity detail page', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.ce-activities.show', $this->activity->id));
    $response->assertStatus(200)
        ->assertViewIs('backend.layouts.ce_activities.show')
        ->assertSee('RCA Training')
        ->assertSee('Patient Safety');
});

test('admin can update ce activity status and notes', function () {
    $response = $this->actingAs($this->admin)
        ->from(route('admin.ce-activities.show', $this->activity->id))
        ->patch(route('admin.ce-activities.update-status', $this->activity->id), [
            'status' => 'approved',
            'admin_notes' => 'Valid evidence. Approved.',
        ]);

    $response->assertRedirect(route('admin.ce-activities.show', $this->activity->id));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('ce_activities', [
        'id' => $this->activity->id,
        'status' => 'approved',
        'admin_notes' => 'Valid evidence. Approved.',
    ]);
});

test('admin can delete a ce activity', function () {
    $response = $this->actingAs($this->admin)->delete(route('admin.ce-activities.destroy', $this->activity->id));
    $response->assertRedirect(route('admin.ce-activities.index'));
    
    $this->assertDatabaseMissing('ce_activities', [
        'id' => $this->activity->id,
    ]);
});
