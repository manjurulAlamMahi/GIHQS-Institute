<?php

use App\Models\User;
use App\Models\HomeGihq;
use App\Models\HomeServicesPathway;
use App\Models\HomeProfessionalPathway;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('home services pathways page can be rendered for admin', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this
        ->actingAs($user)
        ->get('/admin/home-services-pathways');

    $response->assertOk();
    $response->assertSee('Home Page Module');
    $response->assertSee('Services & Pathways', false);
});

test('home page services pathways information can be updated with repeaters', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $homeGihq = HomeGihq::firstOrCreate([], [
        'title1' => 'Initial Title',
    ]);

    $response = $this
        ->actingAs($user)
        ->put("/admin/home-services-pathways/{$homeGihq->id}", [
            'title1' => 'Updated Title 1',
            'title2' => 'Updated Title 2',
            'tagline' => 'Updated Tagline',
            'description' => 'Updated Description',
            'certificate_btn_text' => 'Cert Text',
            'learning_btn_text' => 'Learn Text',
            'advisory_btn_text' => 'Adv Text',
            'member_btn_text' => 'Mem Text',

            'professional_ecosystem_title' => 'Ecosystem Title',
            'learning_tagline' => 'L Tagline',
            'learning_title' => 'L Title',
            'learning_details' => 'L Details',
            'certificate_tagline' => 'C Tagline',
            'certificate_title' => 'C Title',
            'certificate_details' => 'C Details',
            'lead_tagline' => 'Lead Tagline',
            'lead_title' => 'Lead Title',
            'lead_details' => 'Lead Details',

            'injected_status' => '1',

            // Services & Pathways Repeater
            'services_pathways' => [
                [
                    'serial' => '01',
                    'target_audience' => 'Target 1',
                    'title' => 'Service Title 1',
                    'description' => 'Service Description 1',
                    'link_text' => 'Service Link 1',
                ],
                [
                    'serial' => '02',
                    'target_audience' => 'Target 2',
                    'title' => 'Service Title 2',
                    'description' => 'Service Description 2',
                    'link_text' => 'Service Link 2',
                ]
            ],

            // Professional Pathways Repeater
            'professional_pathways' => [
                [
                    'serial' => 'P1',
                    'title' => 'Prof Title 1',
                    'description' => 'Prof Description 1',
                    'link_text' => 'Prof Link 1',
                ]
            ],

            // Next Step Section
            'next_step' => [
                'title1' => 'Next Step Title 1',
                'title2' => 'Next Step Title 2',
                'tagline' => 'Next Step Tagline',
                'certificate_btn_text' => 'Next Cert Text',
                'learning_btn_text' => 'Next Learn Text',
                'advisory_btn_text' => 'Next Adv Text',
                'member_btn_text' => 'Next Mem Text',
            ]
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/home-services-pathways');

    $homeGihq->refresh();
    expect($homeGihq->title1)->toBe('Updated Title 1');
    expect($homeGihq->title2)->toBe('Updated Title 2');
    expect($homeGihq->tagline)->toBe('Updated Tagline');
    expect($homeGihq->description)->toBe('Updated Description');
    expect($homeGihq->certificate_btn_text)->toBe('Cert Text');
    expect($homeGihq->learning_btn_text)->toBe('Learn Text');
    expect($homeGihq->advisory_btn_text)->toBe('Adv Text');
    expect($homeGihq->member_btn_text)->toBe('Mem Text');
    expect($homeGihq->professional_ecosystem_title)->toBe('Ecosystem Title');
    expect($homeGihq->learning_tagline)->toBe('L Tagline');
    expect($homeGihq->learning_title)->toBe('L Title');
    expect($homeGihq->learning_details)->toBe('L Details');
    expect($homeGihq->certificate_tagline)->toBe('C Tagline');
    expect($homeGihq->certificate_title)->toBe('C Title');
    expect($homeGihq->certificate_details)->toBe('C Details');
    expect($homeGihq->lead_tagline)->toBe('Lead Tagline');
    expect($homeGihq->lead_title)->toBe('Lead Title');
    expect($homeGihq->lead_details)->toBe('Lead Details');

    // Check repeaters
    expect($homeGihq->servicesPathways()->count())->toBe(2);
    expect($homeGihq->servicesPathways()->first()->title)->toBe('Service Title 1');
    expect($homeGihq->servicesPathways()->first()->serial)->toBe('01');
    expect($homeGihq->servicesPathways()->first()->target_audience)->toBe('Target 1');

    expect($homeGihq->professionalPathways()->count())->toBe(1);
    expect($homeGihq->professionalPathways()->first()->title)->toBe('Prof Title 1');
    expect($homeGihq->professionalPathways()->first()->serial)->toBe('P1');

    // Check Choose Your Next Step
    expect($homeGihq->nextStep)->not->toBeNull();
    expect($homeGihq->nextStep->title1)->toBe('Next Step Title 1');
    expect($homeGihq->nextStep->title2)->toBe('Next Step Title 2');
    expect($homeGihq->nextStep->tagline)->toBe('Next Step Tagline');
    expect($homeGihq->nextStep->certificate_btn_text)->toBe('Next Cert Text');
    expect($homeGihq->nextStep->learning_btn_text)->toBe('Next Learn Text');
    expect($homeGihq->nextStep->advisory_btn_text)->toBe('Next Adv Text');
    expect($homeGihq->nextStep->member_btn_text)->toBe('Next Mem Text');
});
