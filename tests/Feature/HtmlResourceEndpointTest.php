<?php

use App\Models\Catalogue;
use App\Models\CatalogueHtmlResource;
use App\Models\Purchase;
use App\Models\User;

function makeHtmlCatalogue(array $overrides = []): Catalogue
{
    return Catalogue::create(array_merge([
        'title'          => 'HTML Course',
        'short_title'    => 'HC',
        'price_regular'  => 40.00,
        'service_type'   => 'Course',
        'status'         => 1,
    ], $overrides));
}

/**
 * Copy a fixture into the uploads directory and register it on a catalogue.
 */
function makeResource(Catalogue $catalogue, string $fixture, array $overrides = []): CatalogueHtmlResource
{
    $directory = public_path('uploads/html-resources');

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    $filename = 'test_' . uniqid() . '.html';
    copy(base_path("tests/Fixtures/html/{$fixture}"), $directory . '/' . $filename);

    return CatalogueHtmlResource::create(array_merge([
        'catalogue_id' => $catalogue->id,
        'title'        => 'Test Document',
        'kind'         => 'module',
        'file_path'    => 'uploads/html-resources/' . $filename,
        'is_public'    => false,
        'sort_order'   => 0,
    ], $overrides));
}

function buyCatalogue(User $user, Catalogue $catalogue): void
{
    Purchase::create([
        'user_id'        => $user->id,
        'purchase_type'  => 'catalogue',
        'catalogue_id'   => $catalogue->id,
        'amount'         => $catalogue->price_regular,
        'payment_status' => 'paid',
    ]);
}

afterEach(function () {
    foreach (glob(public_path('uploads/html-resources/test_*.html')) as $file) {
        @unlink($file);
    }
});

/*
|--------------------------------------------------------------------------
| Access control
|--------------------------------------------------------------------------
*/

test('an unknown resource returns 404', function () {
    $this->getJson('/api/html/999999')->assertStatus(404);
});

test('a paid document is refused to an anonymous visitor', function () {
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'learning-module.html');

    $this->get('/api/html/' . $resource->id)->assertStatus(403);
});

test('a paid document is refused to a signed in non purchaser', function () {
    $user      = User::factory()->create();
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'learning-module.html');

    $this->actingAs($user, 'api')
        ->get('/api/html/' . $resource->id)
        ->assertStatus(403);
});

test('a paid document is served to a purchaser', function () {
    $user      = User::factory()->create();
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'learning-module.html');

    buyCatalogue($user, $catalogue);

    $this->actingAs($user, 'api')
        ->get('/api/html/' . $resource->id)
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
});

test('a public document is served to anyone', function () {
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'learning-module.html', ['is_public' => true]);

    $this->get('/api/html/' . $resource->id)->assertStatus(200);
});

test('a document whose file is missing returns 404', function () {
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'learning-module.html', ['is_public' => true]);

    unlink(public_path($resource->file_path));

    $this->get('/api/html/' . $resource->id)->assertStatus(404);
});

/*
|--------------------------------------------------------------------------
| Content
|--------------------------------------------------------------------------
*/

test('the client toolkit is served byte for byte as uploaded', function () {
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'toolkit.html', ['is_public' => true]);

    $response = $this->get('/api/html/' . $resource->id);

    $response->assertStatus(200);
    expect($response->getContent())
        ->toBe(file_get_contents(base_path('tests/Fixtures/html/toolkit.html')));
});

test('a document without navigation is served with the bootstrap', function () {
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'learning-module.html', ['is_public' => true]);

    $this->get('/api/html/' . $resource->id)
        ->assertStatus(200)
        ->assertSee('data-gihqs-bootstrap', false);
});

test('the response forbids content type sniffing', function () {
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'learning-module.html', ['is_public' => true]);

    $this->get('/api/html/' . $resource->id)
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

/*
|--------------------------------------------------------------------------
| Model
|--------------------------------------------------------------------------
*/

test('deleting a catalogue removes its html resources', function () {
    $catalogue = makeHtmlCatalogue();
    makeResource($catalogue, 'learning-module.html');

    expect(CatalogueHtmlResource::count())->toBe(1);

    $catalogue->delete();

    expect(CatalogueHtmlResource::count())->toBe(0);
});

test('a catalogue exposes its html resources in sort order', function () {
    $catalogue = makeHtmlCatalogue();
    makeResource($catalogue, 'learning-module.html', ['title' => 'Second', 'sort_order' => 2]);
    makeResource($catalogue, 'toolkit.html', ['title' => 'First', 'sort_order' => 1]);

    expect($catalogue->htmlResources->pluck('title')->all())->toBe(['First', 'Second']);
});

/*
|--------------------------------------------------------------------------
| Exposure through the catalogue API
|--------------------------------------------------------------------------
*/

test('a purchased catalogue lists its html resources with viewer urls', function () {
    $user      = User::factory()->create();
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'toolkit.html', [
        'title' => 'RCA Toolkit',
        'kind'  => 'toolkit',
    ]);

    buyCatalogue($user, $catalogue);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $catalogue->id)
        ->assertStatus(200);

    $response->assertJsonPath('data.catalogue.html_resources.0.id', $resource->id)
        ->assertJsonPath('data.catalogue.html_resources.0.title', 'RCA Toolkit')
        ->assertJsonPath('data.catalogue.html_resources.0.kind', 'toolkit')
        // No document URL is published. The viewer exchanges its token for a
        // single-use ticket, so there is never a durable link to copy.
        ->assertJsonPath('data.catalogue.html_resources.0.requires_license', false)
        ->assertJsonPath('data.catalogue.html_resources.0.has_license', true);
});

test('a licensed document reports whether this user has redeemed it', function () {
    $user      = User::factory()->create();
    $catalogue = makeHtmlCatalogue();
    $resource  = makeResource($catalogue, 'toolkit.html', ['access_key' => 'SECRET-KEY']);

    buyCatalogue($user, $catalogue);

    $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $catalogue->id)
        ->assertJsonPath('data.catalogue.html_resources.0.requires_license', true)
        ->assertJsonPath('data.catalogue.html_resources.0.has_license', false);

    $this->actingAs($user, 'api')
        ->postJson("/api/html/{$resource->id}/redeem", ['key' => 'SECRET-KEY'])
        ->assertStatus(200);

    $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $catalogue->id)
        ->assertJsonPath('data.catalogue.html_resources.0.has_license', true);
});

test('the access key is never sent to the client', function () {
    $user      = User::factory()->create();
    $catalogue = makeHtmlCatalogue();
    makeResource($catalogue, 'toolkit.html', ['access_key' => 'TOP-SECRET-KEY']);

    buyCatalogue($user, $catalogue);

    $body = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $catalogue->id)
        ->getContent();

    expect($body)->not->toContain('TOP-SECRET-KEY');
});

test('the file path of an html resource is never exposed to the client', function () {
    $user      = User::factory()->create();
    $catalogue = makeHtmlCatalogue();
    makeResource($catalogue, 'toolkit.html');
    buyCatalogue($user, $catalogue);

    $body = $this->actingAs($user, 'api')
        ->getJson('/api/profile/purchased-catalogues/' . $catalogue->id)
        ->getContent();

    expect($body)->not->toContain('uploads/html-resources');
});
