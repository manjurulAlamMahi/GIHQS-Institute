<?php

use App\Models\User;
use App\Models\Catalogue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('catalogue create page can be rendered for admin', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this
        ->actingAs($user)
        ->get('/admin/catalogues/create');

    $response->assertOk();
    $response->assertSee('Create Catalogue Item');
    $response->assertSee('Overview Video');
});

test('admin can create a catalogue item with an overview video', function () {
    $user = User::factory()->create(['role' => 'admin']);

    Storage::fake('public');
    $videoFile = UploadedFile::fake()->create('intro.mp4', 1024, 'video/mp4'); // 1MB video

    $response = $this
        ->actingAs($user)
        ->post('/admin/catalogues', [
            'title' => 'Test Catalogue Title',
            'short_title' => 'TCT',
            'short_description' => 'Test Short Description',
            'catalogue_type' => 'paid',
            'service_type' => 'Course',
            'price_regular' => '99.00',
            'price_final' => '99.00',
            'is_discount_active' => '0',
            'status' => '1',
            'overview_video' => $videoFile,
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/catalogues');

    $catalogue = Catalogue::where('title', 'Test Catalogue Title')->first();
    expect($catalogue)->not->toBeNull();
    expect($catalogue->overview_video)->not->toBeNull();

    // Verify file exists on disk
    $filePath = public_path($catalogue->overview_video);
    expect(file_exists($filePath))->toBeTrue();

    // Clean up uploaded file
    if (file_exists($filePath)) {
        unlink($filePath);
    }
});

test('admin cannot upload overview video file larger than 100MB', function () {
    $user = User::factory()->create(['role' => 'admin']);

    Storage::fake('public');
    // Fake a video file that is 101MB (101 * 1024 = 103424 KB)
    $videoFile = UploadedFile::fake()->create('heavy.mp4', 103424, 'video/mp4');

    $response = $this
        ->actingAs($user)
        ->post('/admin/catalogues', [
            'title' => 'Heavy Video Catalogue',
            'catalogue_type' => 'paid',
            'service_type' => 'Course',
            'price_regular' => '99.00',
            'price_final' => '99.00',
            'status' => '1',
            'overview_video' => $videoFile,
        ]);

    $response->assertSessionHasErrors(['overview_video']);
});

test('admin can update and remove the overview video', function () {
    $user = User::factory()->create(['role' => 'admin']);

    // Create a dummy video file in the target directory
    $dummyDir = public_path('uploads/development-catalogues');
    if (!file_exists($dummyDir)) {
        mkdir($dummyDir, 0755, true);
    }
    $dummyFile = $dummyDir . '/test_overview.mp4';
    file_put_contents($dummyFile, 'dummy video content');

    $catalogue = Catalogue::create([
        'title' => 'Update Video Catalogue',
        'catalogue_type' => 'paid',
        'service_type' => 'Course',
        'price_regular' => 99.00,
        'price_final' => 99.00,
        'status' => 1,
        'overview_video' => 'uploads/development-catalogues/test_overview.mp4',
    ]);

    // Test removing video
    $response = $this
        ->actingAs($user)
        ->put("/admin/catalogues/{$catalogue->id}", [
            'title' => 'Update Video Catalogue',
            'catalogue_type' => 'paid',
            'service_type' => 'Course',
            'price_regular' => '99.00',
            'price_final' => '99.00',
            'status' => '1',
            'remove_overview_video' => '1',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/catalogues');

    $catalogue->refresh();
    expect($catalogue->overview_video)->toBeNull();
    expect(file_exists($dummyFile))->toBeFalse();
});

test('overview video is deleted when catalogue is destroyed', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $dummyDir = public_path('uploads/development-catalogues');
    if (!file_exists($dummyDir)) {
        mkdir($dummyDir, 0755, true);
    }
    $dummyFile = $dummyDir . '/delete_overview.mp4';
    file_put_contents($dummyFile, 'dummy video content');

    $catalogue = Catalogue::create([
        'title' => 'Delete Video Catalogue',
        'catalogue_type' => 'paid',
        'service_type' => 'Course',
        'price_regular' => 99.00,
        'price_final' => 99.00,
        'status' => 1,
        'overview_video' => 'uploads/development-catalogues/delete_overview.mp4',
    ]);

    expect(file_exists($dummyFile))->toBeTrue();

    $response = $this
        ->actingAs($user)
        ->delete("/admin/catalogues/{$catalogue->id}");

    $response->assertRedirect('/admin/catalogues');
    expect(Catalogue::find($catalogue->id))->toBeNull();
    expect(file_exists($dummyFile))->toBeFalse();
});
