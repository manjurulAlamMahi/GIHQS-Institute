<?php

use App\Models\Catalogue;
use App\Models\CatalogueHtmlResource;
use App\Models\User;
use Illuminate\Http\UploadedFile;

function adminUser(): User
{
    return User::factory()->create(['role' => 'admin', 'status' => 1, 'otp_verified' => true]);
}

function adminCatalogue(): Catalogue
{
    return Catalogue::create([
        'title'         => 'Admin HTML Course',
        'short_title'   => 'AHC',
        'price_regular' => 30.00,
        'service_type'  => 'Course',
        'status'        => 1,
    ]);
}

function htmlUpload(string $fixture = 'learning-module.html'): UploadedFile
{
    $source = base_path("tests/Fixtures/html/{$fixture}");
    $temp   = tempnam(sys_get_temp_dir(), 'html') . '.html';
    copy($source, $temp);

    return new UploadedFile($temp, $fixture, 'text/html', null, true);
}

afterEach(function () {
    foreach (glob(public_path('uploads/html-resources/*.html')) as $file) {
        @unlink($file);
    }
});

/*
|--------------------------------------------------------------------------
| Authorization
|--------------------------------------------------------------------------
*/

test('a guest cannot list html resources', function () {
    $catalogue = adminCatalogue();

    $this->get("/admin/catalogues/{$catalogue->id}/html-resources")
        ->assertRedirect('/login');
});

test('a customer cannot create an html resource', function () {
    $customer  = User::factory()->create(['role' => 'user']);
    $catalogue = adminCatalogue();

    $this->actingAs($customer, 'web')
        ->post("/admin/catalogues/{$catalogue->id}/html-resources", [
            'title' => 'Sneaky',
            'kind'  => 'module',
            'file'  => htmlUpload(),
        ])
        ->assertStatus(403);

    expect(CatalogueHtmlResource::count())->toBe(0);
});

/*
|--------------------------------------------------------------------------
| CRUD
|--------------------------------------------------------------------------
*/

test('an admin can upload an html resource', function () {
    $catalogue = adminCatalogue();

    $this->actingAs(adminUser(), 'web')
        ->post("/admin/catalogues/{$catalogue->id}/html-resources", [
            'title'      => 'RCA Toolkit',
            'kind'       => 'toolkit',
            'is_public'  => 0,
            'sort_order' => 1,
            'file'       => htmlUpload('toolkit.html'),
        ])
        ->assertRedirect();

    $resource = CatalogueHtmlResource::first();

    expect($resource)->not->toBeNull()
        ->and($resource->title)->toBe('RCA Toolkit')
        ->and($resource->kind)->toBe('toolkit')
        ->and($resource->catalogue_id)->toBe($catalogue->id)
        ->and(file_exists(public_path($resource->file_path)))->toBeTrue();
});

test('the uploaded file is stored verbatim', function () {
    $catalogue = adminCatalogue();

    $this->actingAs(adminUser(), 'web')
        ->post("/admin/catalogues/{$catalogue->id}/html-resources", [
            'title' => 'Toolkit',
            'kind'  => 'toolkit',
            'file'  => htmlUpload('toolkit.html'),
        ]);

    $stored = file_get_contents(public_path(CatalogueHtmlResource::first()->file_path));

    expect($stored)->toBe(file_get_contents(base_path('tests/Fixtures/html/toolkit.html')));
});

test('a non html upload is rejected', function () {
    $catalogue = adminCatalogue();

    $this->actingAs(adminUser(), 'web')
        ->post("/admin/catalogues/{$catalogue->id}/html-resources", [
            'title' => 'Not HTML',
            'kind'  => 'module',
            'file'  => UploadedFile::fake()->create('payload.php', 8, 'application/x-php'),
        ])
        ->assertSessionHasErrors('file');

    expect(CatalogueHtmlResource::count())->toBe(0);
});

test('an unknown kind is rejected', function () {
    $catalogue = adminCatalogue();

    $this->actingAs(adminUser(), 'web')
        ->post("/admin/catalogues/{$catalogue->id}/html-resources", [
            'title' => 'Bad Kind',
            'kind'  => 'something-else',
            'file'  => htmlUpload(),
        ])
        ->assertSessionHasErrors('kind');
});

test('an admin can rename a resource without re-uploading the file', function () {
    $catalogue = adminCatalogue();
    $admin     = adminUser();

    $this->actingAs($admin, 'web')->post("/admin/catalogues/{$catalogue->id}/html-resources", [
        'title' => 'Original', 'kind' => 'module', 'file' => htmlUpload(),
    ]);

    $resource = CatalogueHtmlResource::first();
    $original = $resource->file_path;

    $this->actingAs($admin, 'web')
        ->put("/admin/html-resources/{$resource->id}", [
            'title' => 'Renamed', 'kind' => 'worksheet', 'sort_order' => 3,
        ])
        ->assertRedirect();

    $resource->refresh();

    expect($resource->title)->toBe('Renamed')
        ->and($resource->kind)->toBe('worksheet')
        ->and($resource->sort_order)->toBe(3)
        ->and($resource->file_path)->toBe($original);
});

test('deleting a resource removes the stored file', function () {
    $catalogue = adminCatalogue();
    $admin     = adminUser();

    $this->actingAs($admin, 'web')->post("/admin/catalogues/{$catalogue->id}/html-resources", [
        'title' => 'Doomed', 'kind' => 'module', 'file' => htmlUpload(),
    ]);

    $resource = CatalogueHtmlResource::first();
    $path     = public_path($resource->file_path);

    expect(file_exists($path))->toBeTrue();

    $this->actingAs($admin, 'web')
        ->delete("/admin/html-resources/{$resource->id}")
        ->assertRedirect();

    expect(CatalogueHtmlResource::count())->toBe(0)
        ->and(file_exists($path))->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Licensing
|--------------------------------------------------------------------------
*/

test('an admin can set an access key and validity period', function () {
    $catalogue = adminCatalogue();

    $this->actingAs(adminUser(), 'web')
        ->post("/admin/catalogues/{$catalogue->id}/html-resources", [
            'title'                 => 'Licensed Toolkit',
            'kind'                  => 'toolkit',
            'access_key'            => 'RCA-2026-KEY',
            'license_validity_days' => 90,
            'file'                  => htmlUpload('toolkit.html'),
        ])
        ->assertRedirect();

    $resource = CatalogueHtmlResource::first();

    expect($resource->access_key)->toBe('RCA-2026-KEY')
        ->and($resource->license_validity_days)->toBe(90)
        ->and($resource->requiresLicense())->toBeTrue();
});

test('a resource created without a key requires no licence', function () {
    $catalogue = adminCatalogue();

    $this->actingAs(adminUser(), 'web')
        ->post("/admin/catalogues/{$catalogue->id}/html-resources", [
            'title' => 'Open Module', 'kind' => 'module', 'file' => htmlUpload(),
        ]);

    expect(CatalogueHtmlResource::first()->requiresLicense())->toBeFalse();
});

test('an admin can revoke a users licence', function () {
    $catalogue = adminCatalogue();
    $admin     = adminUser();

    $this->actingAs($admin, 'web')->post("/admin/catalogues/{$catalogue->id}/html-resources", [
        'title' => 'Licensed', 'kind' => 'toolkit', 'access_key' => 'K-1', 'file' => htmlUpload('toolkit.html'),
    ]);

    $resource = CatalogueHtmlResource::first();
    $holder   = \App\Models\User::factory()->create();

    $license = \App\Models\HtmlResourceLicense::create([
        'user_id'                    => $holder->id,
        'catalogue_html_resource_id' => $resource->id,
        'granted_at'                 => now(),
    ]);

    $this->actingAs($admin, 'web')
        ->post("/admin/html-resource-licenses/{$license->id}/revoke")
        ->assertRedirect();

    expect($license->fresh()->isRevoked())->toBeTrue();
});

test('a customer cannot revoke a licence', function () {
    $catalogue = adminCatalogue();

    $this->actingAs(adminUser(), 'web')->post("/admin/catalogues/{$catalogue->id}/html-resources", [
        'title' => 'Licensed', 'kind' => 'toolkit', 'access_key' => 'K-1', 'file' => htmlUpload('toolkit.html'),
    ]);

    $resource = CatalogueHtmlResource::first();
    $holder   = \App\Models\User::factory()->create();
    $license  = \App\Models\HtmlResourceLicense::create([
        'user_id'                    => $holder->id,
        'catalogue_html_resource_id' => $resource->id,
        'granted_at'                 => now(),
    ]);

    $this->actingAs($holder, 'web')
        ->post("/admin/html-resource-licenses/{$license->id}/revoke")
        ->assertStatus(403);

    expect($license->fresh()->isRevoked())->toBeFalse();
});
