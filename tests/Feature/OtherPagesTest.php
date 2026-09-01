<?php

use App\Models\User;
use App\Models\OtherPage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('other pages can be rendered for admin', function () {
    $user = User::factory()->create(['role' => 'admin']);
    OtherPage::firstOrCreate(
        ['slug' => 'privacy-policy'],
        ['title' => 'Privacy Policy', 'injected_status' => 1]
    );

    $response = $this
        ->actingAs($user)
        ->get('/admin/other-pages');

    $response->assertOk();
    $response->assertSee('Privacy Policy');
});

test('other pages information and file can be updated', function () {
    $user = User::factory()->create(['role' => 'admin']);
    
    $slugs = ['privacy-policy', 'terms-of-use', 'terms-purchase', 'refund-policy', 'disclaimer'];
    foreach ($slugs as $slug) {
        OtherPage::firstOrCreate(
            ['slug' => $slug],
            ['title' => ucfirst($slug), 'injected_status' => 1]
        );
    }

    // Mock file upload
    Storage::fake('public');
    $file = UploadedFile::fake()->create('privacy_test.html', 100, 'text/html');

    $payload = [];
    foreach ($slugs as $slug) {
        $prefix = str_replace('-', '_', $slug);
        $payload["{$prefix}_title"] = ucfirst($slug) . " Custom Title";
        $payload["{$prefix}_injected_status"] = '1';
    }
    // Upload file for privacy policy specifically
    $payload['privacy_policy_file'] = $file;

    $response = $this
        ->actingAs($user)
        ->put("/admin/other-pages", $payload);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/other-pages');

    $page = OtherPage::where('slug', 'privacy-policy')->first();
    expect($page->title)->toBe('Privacy-policy Custom Title');
    expect($page->content_file)->not->toBeNull();

    // Verify file actually exists
    $filePath = public_path($page->content_file);
    expect(file_exists($filePath))->toBeTrue();

    // Clean up
    if (file_exists($filePath)) {
        unlink($filePath);
    }
});

test('other pages public API retrieves correct data', function () {
    $page = OtherPage::firstOrCreate(
        ['slug' => 'terms-of-use'],
        ['title' => 'Terms of Use', 'injected_status' => 1]
    );
    $page->title = 'API Title';
    $page->content_file = 'uploads/other_pages/terms_of_use.html';
    $page->save();

    $response = $this->get('/api/other-pages/terms-of-use');

    $response->assertOk();
    $response->assertJsonPath('data.other_page.title', 'API Title');
    $response->assertJsonPath('data.other_page.slug', 'terms-of-use');
    $response->assertJsonPath('data.other_page.content_file', asset('uploads/other_pages/terms_of_use.html'));
});

test('other pages list API retrieves correct data', function () {
    OtherPage::query()->delete();
    
    OtherPage::firstOrCreate(
        ['slug' => 'privacy-policy'],
        ['title' => 'Privacy Policy', 'injected_status' => 1]
    );
    OtherPage::firstOrCreate(
        ['slug' => 'terms-of-use'],
        ['title' => 'Terms of Use', 'injected_status' => 1]
    );

    $response = $this->get('/api/other-pages');

    $response->assertOk();
    $response->assertJsonCount(2, 'data.other_pages');
});

test('other pages list API supports slug query parameter filtering', function () {
    OtherPage::query()->delete();
    
    OtherPage::firstOrCreate(
        ['slug' => 'privacy-policy'],
        ['title' => 'Privacy Policy', 'injected_status' => 1]
    );
    OtherPage::firstOrCreate(
        ['slug' => 'terms-of-use'],
        ['title' => 'Terms of Use', 'injected_status' => 1]
    );

    $response = $this->get('/api/other-pages?slug=privacy-policy');

    $response->assertOk();
    $response->assertJsonPath('data.other_page.slug', 'privacy-policy');
    $response->assertJsonMissingPath('data.other_pages');
});
