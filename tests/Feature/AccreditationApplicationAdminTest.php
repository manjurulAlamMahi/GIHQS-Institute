<?php

use App\Models\User;
use App\Models\AccreditationApplication;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $this->application = AccreditationApplication::create([
        'applicant_category' => 'University',
        'applicant_name' => 'Harvard University',
        'department_division' => 'Medicine',
        'country' => 'USA',
        'city' => 'Cambridge',
        'website_url' => 'https://harvard.edu',
        'year_established' => '1636',
        'program_name' => 'MD Program',
        'program_type' => 'Medical Degree',
        'program_delivery_format' => 'On Campus',
        'estimated_annual_participants' => '200',
        'primary_language_of_instruction' => 'English',
        'program_launch_date' => '1900',
        'primary_contact_person' => 'Dr. Smith',
        'contact_title_position' => 'Dean',
        'email_address' => 'dean@harvard.edu',
        'phone_number' => '123456789',
        'status' => 'pending',
    ]);
});

it('can render accreditation applications index page for admin', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.accreditation-applications.index'));

    $response->assertStatus(200);
});

it('can fetch accreditation applications ajax request', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.accreditation-applications.index'), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['data']);
});

it('can render show accreditation application details page', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.accreditation-applications.show', $this->application->id));

    $response->assertStatus(200);
    $response->assertSee('Harvard University');
    $response->assertSee('Dr. Smith');
});

it('can update accreditation application status and notes', function () {
    $response = $this->actingAs($this->admin)->patch(route('admin.accreditation-applications.update-status', $this->application->id), [
        'status' => 'valid',
        'admin_notes' => 'Harvard MD program approved',
    ]);

    $response->assertRedirect();
    
    $this->application->refresh();
    expect($this->application->status)->toBe('valid');
    expect($this->application->admin_notes)->toBe('Harvard MD program approved');
});

it('can delete an accreditation application', function () {
    $response = $this->actingAs($this->admin)->delete(route('admin.accreditation-applications.destroy', $this->application->id));

    $response->assertRedirect();
    $this->assertSoftDeleted($this->application);
});

it('can generate a Stripe payment link for accreditation application', function () {
    Mail::fake();

    $response = $this->actingAs($this->admin)->post(route('admin.accreditation-applications.generate-payment-link', $this->application->id), [
        'payment_amount' => 1500.00,
        'validity_days' => 365,
        'payment_description' => 'Test payment link description',
    ]);

    $response->assertRedirect();
    $this->application->refresh();

    expect($this->application->payment_amount)->toBe(1500.00);
    expect($this->application->validity_days)->toBe(365);
    expect($this->application->payment_description)->toBe('Test payment link description');
    expect($this->application->stripe_session_id)->not->toBeNull();
    expect($this->application->stripe_payment_link)->not->toBeNull();
    expect($this->application->payment_status)->toBe('pending');
    expect($this->application->payment_sent_at)->not->toBeNull();

    Mail::assertSent(\App\Mail\AccreditationPaymentLinkMail::class, function ($mail) {
        return $mail->hasTo($this->application->email_address) &&
               $mail->amount === 1500.00 &&
               $mail->paymentLink === $this->application->stripe_payment_link;
    });
});

it('does not generate certificate if status is updated to valid but unpaid', function () {
    $response = $this->actingAs($this->admin)->patch(route('admin.accreditation-applications.update-status', $this->application->id), [
        'status' => 'valid',
        'admin_notes' => 'Approved but unpaid',
    ]);

    $response->assertRedirect();
    $this->application->refresh();

    expect($this->application->status)->toBe('valid');
    expect($this->application->certificate_pdf)->toBeNull();
});

it('does generate certificate if status is valid and payment is paid', function () {
    Mail::fake();

    $this->application->update(['payment_status' => 'paid']);

    $response = $this->actingAs($this->admin)->patch(route('admin.accreditation-applications.update-status', $this->application->id), [
        'status' => 'valid', // maps to valid
        'admin_notes' => 'Approved and paid',
    ]);

    $response->assertRedirect();
    $this->application->refresh();

    expect($this->application->status)->toBe('valid');
    expect($this->application->certificate_pdf)->not->toBeNull();

    Mail::assertSent(\App\Mail\AccreditationStatusMail::class, function ($mail) {
        return $mail->hasTo($this->application->email_address) &&
               $mail->status === 'valid' &&
               count($mail->attachments()) > 0;
    });
});

it('cannot manually regenerate certificate if unpaid', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.accreditation-applications.regenerate-certificate', $this->application->id));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Cannot generate certificate for unpaid or invalid status.');
});

it('can update status to accepted without generating certificate', function () {
    $this->application->update(['payment_status' => 'paid']);

    $response = $this->actingAs($this->admin)->patch(route('admin.accreditation-applications.update-status', $this->application->id), [
        'status' => 'accepted',
        'admin_notes' => 'Application accepted, waiting for invoice generation',
    ]);

    $response->assertRedirect();
    $this->application->refresh();

    expect($this->application->status)->toBe('accepted');
    expect($this->application->certificate_pdf)->toBeNull();
});
