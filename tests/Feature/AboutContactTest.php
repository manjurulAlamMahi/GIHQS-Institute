<?php

use App\Models\User;
use App\Models\AboutContact;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('about contact page can be rendered for admin', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this
        ->actingAs($user)
        ->get('/admin/about-contact');

    $response->assertOk();
    $response->assertSee('About Contact');
});

test('about contact information can be updated', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $aboutContact = AboutContact::firstOrCreate([], ['title' => 'Initial Title']);

    $response = $this
        ->actingAs($user)
        ->put("/admin/about-contact/{$aboutContact->id}", [
            'title' => 'Updated Title',
            'phone' => '+1 (555) 0199',
            'email' => 'updated@gmail.com',
            'address' => 'Updated Address',
            'working_hours' => '9 AM - 5 PM',
            'mission' => 'Updated Mission',
            'injected_status' => '1',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/about-contact');

    $aboutContact->refresh();
    expect($aboutContact->title)->toBe('Updated Title');
    expect($aboutContact->phone)->toBe('+1 (555) 0199');
    expect($aboutContact->email)->toBe('updated@gmail.com');
    expect($aboutContact->address)->toBe('Updated Address');
    expect($aboutContact->working_hours)->toBe('9 AM - 5 PM');
    expect($aboutContact->mission)->toBe('Updated Mission');
});

test('about contact page content file injection works', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $aboutContact = AboutContact::firstOrCreate([], ['title' => 'Initial Title']);

    // Mock file upload
    Storage::fake('public');
    $file = UploadedFile::fake()->create('injected_content.html', 100, 'text/html');

    $response = $this
        ->actingAs($user)
        ->put("/admin/about-contact/{$aboutContact->id}", [
            'title' => 'Updated Title',
            'injected_status' => '1',
            'content_file' => $file,
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/about-contact');

    $aboutContact->refresh();
    expect($aboutContact->content_file)->not->toBeNull();

    // Verify it exists in public/uploads/about_contact folder
    $filePath = public_path($aboutContact->content_file);
    expect(file_exists($filePath))->toBeTrue();

    // Clean up uploaded file
    if (file_exists($filePath)) {
        unlink($filePath);
    }
});

test('about contact page content file can be deleted', function () {
    $user = User::factory()->create(['role' => 'admin']);
    
    // Write a dummy file to simulate existing upload
    $dummyDir = public_path('uploads/about_contact');
    if (!file_exists($dummyDir)) {
        mkdir($dummyDir, 0755, true);
    }
    $dummyFile = $dummyDir . '/test_delete.html';
    file_put_contents($dummyFile, 'dummy content');

    $aboutContact = AboutContact::firstOrCreate([], [
        'title' => 'Initial Title',
        'content_file' => 'uploads/about_contact/test_delete.html'
    ]);

    $response = $this
        ->actingAs($user)
        ->put("/admin/about-contact/{$aboutContact->id}", [
            'title' => 'Updated Title',
            'injected_status' => '1',
            'remove_content_file' => '1',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/about-contact');

    $aboutContact->refresh();
    expect($aboutContact->content_file)->toBeNull();
    expect(file_exists($dummyFile))->toBeFalse();
});
