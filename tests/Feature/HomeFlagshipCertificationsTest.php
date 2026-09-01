<?php

use App\Models\User;
use App\Models\HomeRecognizedPathway;
use App\Models\HomeCertificate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('home flagship certifications page can be rendered for admin', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this
        ->actingAs($user)
        ->get('/admin/home-flagship-certifications');

    $response->assertOk();
    $response->assertSee('Home Page Module');
    $response->assertSee('Flagship Certifications');
});

test('home flagship certifications can be updated with repeater and icon file upload', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $pathway = HomeRecognizedPathway::firstOrCreate([], [
        'title1' => 'Initial Title',
    ]);

    // Mock file upload
    Storage::fake('public');
    $file = UploadedFile::fake()->image('cert_icon.png', 100, 100);

    $response = $this
        ->actingAs($user)
        ->put("/admin/home-flagship-certifications/{$pathway->id}", [
            'title1' => 'Updated Title 1',
            'title2' => 'Updated Title 2',
            'tagline' => 'Updated Tagline',
            'description' => 'Updated Description',
            'injected_status' => '1',
            'certificates' => [
                [
                    'short_title' => 'BP',
                    'title' => 'Basic Practitioner',
                    'icon' => $file,
                    'tagline' => 'BP Tagline',
                    'headline' => 'BP Headline',
                    'description' => 'BP Description',
                    'audience' => 'BP Audience',
                    'tags' => 'tag1, tag2',
                    'button_text' => 'BP Button',
                ]
            ]
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/home-flagship-certifications');

    $pathway->refresh();
    expect($pathway->title1)->toBe('Updated Title 1');
    expect($pathway->title2)->toBe('Updated Title 2');
    expect($pathway->tagline)->toBe('Updated Tagline');
    expect($pathway->description)->toBe('Updated Description');

    expect($pathway->certificates()->count())->toBe(1);
    
    $certificate = $pathway->certificates()->first();
    expect($certificate->title)->toBe('Basic Practitioner');
    expect($certificate->short_title)->toBe('BP');
    expect($certificate->icon)->not->toBeNull();

    // Verify file exists
    $filePath = public_path($certificate->icon);
    expect(file_exists($filePath))->toBeTrue();

    // Clean up uploaded file
    if (file_exists($filePath)) {
        unlink($filePath);
    }
});
