<?php

use App\Services\HtmlDocumentRenderer;

function renderer(): HtmlDocumentRenderer
{
    return app(HtmlDocumentRenderer::class);
}

function fixtureHtml(string $name): string
{
    return file_get_contents(base_path("tests/Fixtures/html/{$name}"));
}

/*
|--------------------------------------------------------------------------
| Documents that must be served untouched
|--------------------------------------------------------------------------
*/

test('a document that owns its navigation is served byte for byte identical', function () {
    $original = fixtureHtml('toolkit.html');

    expect(renderer()->render($original))->toBe($original);
});

test('a document with too little structure is served untouched', function () {
    $original = '<html><body><h1>Only A Title</h1><p>One paragraph.</p></body></html>';

    expect(renderer()->render($original))->toBe($original);
});

test('an unparseable document is served untouched rather than erroring', function () {
    $original = '<<<not really html at all >>>';

    expect(renderer()->render($original))->toBe($original);
});

/*
|--------------------------------------------------------------------------
| Documents that receive the bootstrap
|--------------------------------------------------------------------------
*/

test('a document without navigation receives exactly one bootstrap script', function () {
    $rendered = renderer()->render(fixtureHtml('learning-module.html'));

    expect(substr_count($rendered, 'data-gihqs-bootstrap'))->toBe(1);
});

test('the original markup is preserved in full ahead of the bootstrap', function () {
    $original = fixtureHtml('learning-module.html');
    $rendered = renderer()->render($original);

    // Everything before the injection point is the untouched original.
    $head = substr($original, 0, strripos($original, '</body>'));

    expect($rendered)->toStartWith($head);
});

test('the bootstrap is inserted before the closing body tag', function () {
    $rendered = renderer()->render(fixtureHtml('learning-module.html'));

    $script = strpos($rendered, 'data-gihqs-bootstrap');
    $body   = strripos($rendered, '</body>');

    expect($script)->toBeLessThan($body);
});

test('the bootstrap carries the tier the analyzer chose', function () {
    $rendered = renderer()->render(fixtureHtml('learning-module.html'));

    expect($rendered)->toMatch('/data-tier="(section-id|section-heading|heading)"/');
});

test('a document with no closing body tag still receives the bootstrap', function () {
    $original = '<h2>One</h2><p>a</p><h2>Two</h2><p>b</p>';
    $rendered = renderer()->render($original);

    expect($rendered)->toStartWith($original)
        ->and(substr_count($rendered, 'data-gihqs-bootstrap'))->toBe(1);
});

test('the bootstrap declares nothing on the global scope', function () {
    $rendered = renderer()->render(fixtureHtml('learning-module.html'));

    // Everything must be wrapped so it cannot collide with the uploaded file's
    // own script. An IIFE is the contract.
    expect($rendered)->toContain('(function()');
});

test('rendering is deterministic for the same input', function () {
    $original = fixtureHtml('learning-module.html');

    expect(renderer()->render($original))->toBe(renderer()->render($original));
});
