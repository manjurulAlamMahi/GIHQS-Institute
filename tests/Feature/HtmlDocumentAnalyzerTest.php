<?php

use App\Services\HtmlDocumentAnalyzer;

function fixture(string $name): string
{
    return file_get_contents(base_path("tests/Fixtures/html/{$name}"));
}

function analyze(string $name): array
{
    return app(HtmlDocumentAnalyzer::class)->analyze(fixture($name));
}

/*
|--------------------------------------------------------------------------
| Classification: does the document own its navigation?
|--------------------------------------------------------------------------
*/

test('a document whose aside holds targeted controls owns its navigation', function () {
    // The client toolkit: aside#nav with six data-target buttons.
    expect(analyze('toolkit.html')['owns_navigation'])->toBeTrue();
});

test('a decorative aside with no controls does not count as navigation', function () {
    // The assessment file has aside.sidebar, but it holds meta rows and a
    // progress bar - no links, no buttons, nothing to navigate with.
    expect(analyze('assessment.html')['owns_navigation'])->toBeFalse();
});

test('a document with no aside or nav does not own its navigation', function () {
    expect(analyze('learning-module.html')['owns_navigation'])->toBeFalse();
});

test('a single targeted control is not enough to count as navigation', function () {
    $html = '<html><body><nav><a href="#one">One</a></nav>'
          . '<section id="one"><h2>One</h2></section></body></html>';

    expect(app(HtmlDocumentAnalyzer::class)->analyze($html)['owns_navigation'])->toBeFalse();
});

test('anchor based navigation counts as owning navigation', function () {
    $html = '<html><body><nav><a href="#one">One</a><a href="#two">Two</a></nav>'
          . '<section id="one"><h2>One</h2></section>'
          . '<section id="two"><h2>Two</h2></section></body></html>';

    expect(app(HtmlDocumentAnalyzer::class)->analyze($html)['owns_navigation'])->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Section candidates: is a generated sidebar worth building?
|--------------------------------------------------------------------------
*/

test('a document with headed sections yields section candidates', function () {
    $result = analyze('assessment.html');

    expect($result['section_count'])->toBeGreaterThanOrEqual(2)
        ->and($result['tier'])->toBeIn(['section-id', 'section-heading', 'heading']);
});

test('the learning module falls back to headings for its sections', function () {
    $result = analyze('learning-module.html');

    expect($result['section_count'])->toBeGreaterThanOrEqual(2);
});

test('headings inside script style and template are never counted', function () {
    $html = '<html><head><script>var s = "<h2>Fake</h2>";</script>'
          . '<style>h2{color:red}</style></head><body>'
          . '<template><h2>Also Fake</h2></template>'
          . '<h2>Real One</h2><h2>Real Two</h2></body></html>';

    $result = app(HtmlDocumentAnalyzer::class)->analyze($html);

    expect($result['section_count'])->toBe(2);
});

test('a document with fewer than two candidates generates no navigation', function () {
    $html = '<html><body><h1>Only A Title</h1><p>One paragraph.</p></body></html>';

    $result = app(HtmlDocumentAnalyzer::class)->analyze($html);

    expect($result['section_count'])->toBeLessThan(2)
        ->and($result['should_inject'])->toBeFalse();
});

test('a document that owns its navigation is never injected into', function () {
    expect(analyze('toolkit.html')['should_inject'])->toBeFalse();
});

test('a document without navigation but with sections is injected into', function () {
    expect(analyze('learning-module.html')['should_inject'])->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Robustness
|--------------------------------------------------------------------------
*/

test('malformed markup is analysed without throwing', function () {
    $result = analyze('pathological.html');

    expect($result)->toHaveKeys(['owns_navigation', 'section_count', 'tier', 'should_inject']);
});

test('an empty document is analysed without throwing', function () {
    $result = app(HtmlDocumentAnalyzer::class)->analyze('');

    expect($result['should_inject'])->toBeFalse();
});
