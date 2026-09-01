<?php

use App\Models\Catalogue;
use App\Models\CatalogueHtmlResource;
use App\Models\HtmlResourceLicense;
use App\Models\Purchase;
use App\Models\User;

function licCatalogue(): Catalogue
{
    return Catalogue::create([
        'title'         => 'Licensed Course',
        'short_title'   => 'LC',
        'price_regular' => 20.00,
        'service_type'  => 'Course',
        'status'        => 1,
    ]);
}

function licResource(Catalogue $catalogue, array $overrides = []): CatalogueHtmlResource
{
    $directory = public_path('uploads/html-resources');
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    $filename = 'lic_' . uniqid() . '.html';
    copy(base_path('tests/Fixtures/html/toolkit.html'), $directory . '/' . $filename);

    return CatalogueHtmlResource::create(array_merge([
        'catalogue_id' => $catalogue->id,
        'title'        => 'Licensed Toolkit',
        'kind'         => 'toolkit',
        'file_path'    => 'uploads/html-resources/' . $filename,
        'is_public'    => false,
        'sort_order'   => 0,
        'access_key'   => 'RCA-TOOLKIT-2026',
    ], $overrides));
}

function licOwner(Catalogue $catalogue): User
{
    $user = User::factory()->create();

    Purchase::create([
        'user_id'        => $user->id,
        'purchase_type'  => 'catalogue',
        'catalogue_id'   => $catalogue->id,
        'amount'         => 20.00,
        'payment_status' => 'paid',
    ]);

    return $user;
}

afterEach(function () {
    foreach (glob(public_path('uploads/html-resources/lic_*.html')) as $file) {
        @unlink($file);
    }
});

/*
|--------------------------------------------------------------------------
| Redemption
|--------------------------------------------------------------------------
*/

test('the correct key grants a licence', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);
    $user      = licOwner($catalogue);

    $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/redeem", ['key' => 'RCA-TOOLKIT-2026'])
        ->assertStatus(200);

    expect(HtmlResourceLicense::where('user_id', $user->id)->count())->toBe(1);
});

test('a wrong key grants nothing', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);
    $user      = licOwner($catalogue);

    $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/redeem", ['key' => 'NOT-THE-KEY'])
        ->assertStatus(422);

    expect(HtmlResourceLicense::count())->toBe(0);
});

test('a user without course access cannot redeem at all', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);
    $stranger  = User::factory()->create();

    $this->actingAs($stranger, 'api')
        ->postJson("/api/html/{$resource->id}/redeem", ['key' => 'RCA-TOOLKIT-2026'])
        ->assertStatus(403);

    expect(HtmlResourceLicense::count())->toBe(0);
});

test('redeeming twice does not create a second licence', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);
    $user      = licOwner($catalogue);

    foreach ([1, 2] as $ignored) {
        $this->actingAs($user, 'api')
            ->postJson("/api/html/{$resource->id}/redeem", ['key' => 'RCA-TOOLKIT-2026'])
            ->assertStatus(200);
    }

    expect(HtmlResourceLicense::count())->toBe(1);
});

test('a licence expires according to the validity period on the resource', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue, ['license_validity_days' => 30]);
    $user      = licOwner($catalogue);

    $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/redeem", ['key' => 'RCA-TOOLKIT-2026']);

    $license = HtmlResourceLicense::first();

    expect($license->expires_at)->not->toBeNull()
        ->and($license->expires_at->isFuture())->toBeTrue()
        ->and((int) round(now()->diffInDays($license->expires_at)))->toBe(30);
});

/*
|--------------------------------------------------------------------------
| Ticket issue
|--------------------------------------------------------------------------
*/

test('a licensed user is issued a ticket', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);
    $user      = licOwner($catalogue);

    $this->actingAs($user, 'api')->postJson("/api/html/{$resource->id}/redeem", ['key' => 'RCA-TOOLKIT-2026']);

    $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/ticket")
        ->assertStatus(200)
        ->assertJsonStructure(['data' => ['url', 'expires_in']]);
});

test('an unlicensed user is told a licence is required', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);
    $user      = licOwner($catalogue);

    $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/ticket")
        ->assertStatus(403)
        ->assertJsonPath('data.reason', 'license_required');
});

test('an expired licence is reported as expired', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);
    $user      = licOwner($catalogue);

    HtmlResourceLicense::create([
        'user_id'                    => $user->id,
        'catalogue_html_resource_id' => $resource->id,
        'granted_at'                 => now()->subDays(60),
        'expires_at'                 => now()->subDay(),
    ]);

    $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/ticket")
        ->assertStatus(403)
        ->assertJsonPath('data.reason', 'license_expired');
});

test('a revoked licence is reported as revoked', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);
    $user      = licOwner($catalogue);

    HtmlResourceLicense::create([
        'user_id'                    => $user->id,
        'catalogue_html_resource_id' => $resource->id,
        'granted_at'                 => now()->subDay(),
        'revoked_at'                 => now(),
    ]);

    $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/ticket")
        ->assertStatus(403)
        ->assertJsonPath('data.reason', 'license_revoked');
});

test('a resource with no access key needs no licence', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue, ['access_key' => null]);
    $user      = licOwner($catalogue);

    $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/ticket")
        ->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| The ticket itself - this is where the iframe defect is pinned down
|--------------------------------------------------------------------------
*/

test('a ticket url serves the document with NO authorization header', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue, ['access_key' => null]);
    $user      = licOwner($catalogue);

    $url = $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/ticket")
        ->json('data.url');

    // Exactly what an <iframe src="..."> produces: a plain GET, no auth header.
    $this->get(parse_url($url, PHP_URL_PATH))
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
});

test('a ticket cannot be used twice', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue, ['access_key' => null]);
    $user      = licOwner($catalogue);

    $url  = $this->actingAs($user, 'api')->postJson("/api/html/{$resource->id}/ticket")->json('data.url');
    $path = parse_url($url, PHP_URL_PATH);

    $this->get($path)->assertStatus(200);
    $this->get($path)->assertStatus(404);
});

test('an unknown ticket is refused', function () {
    $this->get('/api/html/view/' . str_repeat('a', 43))->assertStatus(404);
});

test('a copied ticket url stops working once the original viewer has loaded it', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue, ['access_key' => null]);
    $user      = licOwner($catalogue);

    $url  = $this->actingAs($user, 'api')->postJson("/api/html/{$resource->id}/ticket")->json('data.url');
    $path = parse_url($url, PHP_URL_PATH);

    $this->get($path)->assertStatus(200);

    // Someone else pastes the same URL.
    $stranger = User::factory()->create();
    $this->actingAs($stranger, 'api')->get($path)->assertStatus(404);
});

/*
|--------------------------------------------------------------------------
| Direct access is still closed
|--------------------------------------------------------------------------
*/

test('the direct resource url refuses a licensed but header-less request', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);

    $this->get("/api/html/{$resource->id}")->assertStatus(403);
});

test('another users account cannot open a resource it has no licence for', function () {
    $catalogue = licCatalogue();
    $resource  = licResource($catalogue);
    $owner     = licOwner($catalogue);
    $other     = licOwner($catalogue); // has the course, not the licence

    $this->actingAs($owner, 'api')->postJson("/api/html/{$resource->id}/redeem", ['key' => 'RCA-TOOLKIT-2026']);

    $this->actingAs($other, 'api')
        ->postJson("/api/html/{$resource->id}/ticket")
        ->assertStatus(403)
        ->assertJsonPath('data.reason', 'license_required');
});
