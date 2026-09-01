<?php

use App\Models\User;
use App\Models\Catalogue;
use App\Models\AdminSetting;
use App\Mail\ClientFormSubmissionMail;
use App\Mail\AdminFormSubmissionMail;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();

    // Create a mock setting
    AdminSetting::create([
        'system_title' => 'GIHQS Test System',
        'email' => 'admin-fallback@gihqs.org',
        'phone_number' => '+123456789',
        'whatsapp_number' => '+987654321',
    ]);

    config(['mail.receive_address' => 'admin-test@gihqs.org']);
});

test('contact message submission triggers client and admin emails when authenticated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')->postJson('/api/about-contact-message', [
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'email' => 'alice@example.com',
        'phone' => '1234567890',
        'organization' => 'Alice Corp',
        'service_of_interest' => 'General Inquiry',
        'message' => 'Hello, I have a question.',
    ]);

    $response->assertStatus(201);

    // Assert client confirmation mail was sent
    Mail::assertSent(ClientFormSubmissionMail::class, function ($mail) {
        return $mail->hasTo('alice@example.com') &&
               $mail->requestType === 'Contact Message' &&
               $mail->clientName === 'Alice Smith' &&
               isset($mail->summaryData['Message']) &&
               str_contains($mail->referenceNumber, 'REF-CON-');
    });

    // Assert admin notification mail was sent
    Mail::assertSent(AdminFormSubmissionMail::class, function ($mail) {
        return $mail->hasTo('admin-test@gihqs.org') &&
               $mail->requestType === 'Contact Message' &&
               $mail->clientInfo['name'] === 'Alice Smith' &&
               str_contains($mail->referenceNumber, 'REF-CON-');
    });
});

test('contact message submission returns 401 when not authenticated', function () {
    $response = $this->postJson('/api/about-contact-message', [
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'email' => 'alice@example.com',
        'phone' => '1234567890',
        'organization' => 'Alice Corp',
        'service_of_interest' => 'General Inquiry',
        'message' => 'Hello, I have a question.',
    ]);

    $response->assertStatus(401);
});

test('accreditation application submission triggers client and admin emails', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/apply-accreditation', [
            'applicant_category' => 'University',
            'applicant_name' => 'Test University',
            'country' => 'USA',
            'city' => 'Boston',
            'program_name' => 'Test BS Program',
            'program_type' => 'Bachelor of Science',
            'program_delivery_format' => 'Hybrid',
            'primary_contact_person' => 'John Dean',
            'contact_title_position' => 'Program Manager',
            'email_address' => 'john.dean@example.com',
        ]);

    $response->assertStatus(201);

    // Assert client confirmation mail was sent
    Mail::assertSent(ClientFormSubmissionMail::class, function ($mail) {
        return $mail->hasTo('john.dean@example.com') &&
               $mail->requestType === 'Accreditation Application' &&
               $mail->clientName === 'John Dean' &&
               str_contains($mail->referenceNumber, 'REF-ACC-');
    });

    // Assert admin notification mail was sent
    Mail::assertSent(AdminFormSubmissionMail::class, function ($mail) {
        return $mail->hasTo('admin-test@gihqs.org') &&
               $mail->requestType === 'Accreditation Application' &&
               $mail->clientInfo['name'] === 'John Dean' &&
               str_contains($mail->referenceNumber, 'REF-ACC-');
    });
});

test('advisory request submission triggers client and admin emails', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/advisory-request', [
            'organization_name' => 'Consulting Org',
            'full_name' => 'Robert Consult',
            'work_email' => 'robert@example.com',
            'phone_number' => '5551234',
            'country' => 'Canada',
            'organization_type' => 'NGO',
            'service_of_interest' => 'Quality Advisory',
            'desired_timeline' => 'Next 3 months',
            'description_of_needs' => 'We need advisory on quality assurance.',
        ]);

    $response->assertStatus(201);

    // Assert client confirmation mail was sent
    Mail::assertSent(ClientFormSubmissionMail::class, function ($mail) {
        return $mail->hasTo('robert@example.com') &&
               $mail->requestType === 'Advisory Request' &&
               $mail->clientName === 'Robert Consult' &&
               str_contains($mail->referenceNumber, 'REF-ADV-');
    });

    // Assert admin notification mail was sent
    Mail::assertSent(AdminFormSubmissionMail::class, function ($mail) {
        return $mail->hasTo('admin-test@gihqs.org') &&
               $mail->requestType === 'Advisory Request' &&
               $mail->clientInfo['name'] === 'Robert Consult' &&
               str_contains($mail->referenceNumber, 'REF-ADV-');
    });
});

test('certification application submission triggers client and admin emails', function () {
    $user = User::factory()->create();

    $catalogue = Catalogue::create([
        'title' => 'Medical Quality Certification',
        'service_type' => 'Certification',
        'status' => 1,
        'description' => 'A certification program',
    ]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/apply-for-certification', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '777888999',
            'country' => 'UK',
            'city' => 'London',
            'current_job_title' => 'QA Officer',
            'organization' => 'Health Care Inc',
            'years_of_experience' => '5-10',
            'primary_area_of_experience' => 'Quality control',
            'professional_role' => 'Administrator',
            'catalogue_id' => $catalogue->id,
            'confirm_accuracy' => true,
            'agree_policies' => true,
        ]);

    $response->assertStatus(201);

    // Assert client confirmation mail was sent
    Mail::assertSent(ClientFormSubmissionMail::class, function ($mail) {
        return $mail->hasTo('jane.doe@example.com') &&
               $mail->requestType === 'Certification Application' &&
               $mail->clientName === 'Jane Doe' &&
               str_contains($mail->referenceNumber, 'REF-CRT-');
    });

    // Assert admin notification mail was sent
    Mail::assertSent(AdminFormSubmissionMail::class, function ($mail) {
        return $mail->hasTo('admin-test@gihqs.org') &&
               $mail->requestType === 'Certification Application' &&
               $mail->clientInfo['name'] === 'Jane Doe' &&
               str_contains($mail->referenceNumber, 'REF-CRT-');
    });
});
