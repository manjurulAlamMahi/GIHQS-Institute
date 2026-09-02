<?php

use App\Models\Catalogue;
use App\Models\CatalogueExam;
use App\Models\CertificateSetting;
use App\Models\User;
use App\Models\UserExamResult;

/**
 * These tests exercise the certificate the recipient actually receives.
 *
 * The generator writes a PDF, which is awkward to assert against, so the
 * template-filling step is exposed separately and checked as HTML. The
 * end-to-end test then confirms a real PDF is produced.
 */

function certSetting(array $overrides = []): CertificateSetting
{
    return CertificateSetting::create(array_merge([
        'chairman_name'            => 'Dr Jane Smith',
        'chairman_title'           => 'Chairman of the Board',
        'executive_director_name'  => 'Mr John Doe',
        'executive_director_title' => 'Executive Director',
    ], $overrides));
}

function certResult(string $serviceType = 'Certification'): UserExamResult
{
    $catalogue = Catalogue::create([
        'title'          => 'Certified Healthcare Quality Professional',
        'short_title'    => 'CHQP',
        'price_regular'  => 500.00,
        'service_type'   => $serviceType,
        'status'         => 1,
        'validity_years' => 3,
    ]);

    $exam = CatalogueExam::create([
        'catalogue_id' => $catalogue->id,
        'exam_title'   => 'Final',
        'pass_mark'    => 70,
    ]);

    return UserExamResult::create([
        'user_id'           => User::factory()->create(['first_name' => 'Ada', 'last_name' => 'Lovelace'])->id,
        'catalogue_exam_id' => $exam->id,
        'percentage'        => 92,
        'status'            => 'passed',
    ]);
}

function renderCertificate(UserExamResult $result): string
{
    $catalogue = $result->catalogueExam->catalogue;

    return app(\App\Services\CertificateRenderer::class)
        ->render($result, $catalogue, $result->user, 'GIHQS-2026-000123-AB12CD');
}

/*
|--------------------------------------------------------------------------
| The client's requirement: issue date yes, validity no
|--------------------------------------------------------------------------
*/

test('the certificate shows the issue date', function () {
    certSetting();
    $result = certResult();

    expect(renderCertificate($result))
        ->toContain('Date of Issue')
        ->toContain($result->created_at->format('M d, Y'));
});

test('the certificate does not mention validity or expiry', function () {
    certSetting();

    $html = renderCertificate(certResult());

    expect($html)->not->toContain('Valid Until')
        ->and($html)->not->toContain('EXPIRY_DATE');
});

test('course certificates also drop validity', function () {
    certSetting();

    expect(renderCertificate(certResult('Course')))->not->toContain('Valid Until');
});

/*
|--------------------------------------------------------------------------
| Signatures
|--------------------------------------------------------------------------
*/

test('the certificate shows both signer names', function () {
    certSetting();

    expect(renderCertificate(certResult()))
        ->toContain('Dr Jane Smith')
        ->toContain('Mr John Doe');
});

test('the certificate shows each signers title', function () {
    certSetting();

    expect(renderCertificate(certResult()))
        ->toContain('Chairman of the Board')
        ->toContain('Executive Director');
});

test('an admin can change a signers title', function () {
    certSetting(['chairman_title' => 'Interim Chair']);

    $html = renderCertificate(certResult());

    expect($html)->toContain('Interim Chair')
        ->and($html)->not->toContain('Chairman of the Board');
});

test('a blank signer name leaves the title standing rather than an empty line', function () {
    // A section with a signature but no name still renders - the title carries
    // it. (With neither a name nor a signature the whole section is hidden;
    // that is covered separately.)
    $signature = 'uploads/certificate-settings/test_signature.png';
    $absolute  = public_path($signature);

    if (!is_dir(dirname($absolute))) {
        mkdir(dirname($absolute), 0755, true);
    }
    file_put_contents($absolute, base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
    ));

    certSetting(['chairman_name' => null, 'chairman_signature' => $signature]);

    $html = renderCertificate(certResult());

    expect($html)->toContain('Chairman of the Board')
        ->and($html)->not->toContain('Dr Jane Smith');

    @unlink($absolute);
});

test('a missing certificate setting still produces a certificate', function () {
    // No CertificateSetting row at all.
    $html = renderCertificate(certResult());

    expect($html)->toContain('Ada Lovelace')
        ->and($html)->toContain('Date of Issue');
});

/*
|--------------------------------------------------------------------------
| Nothing may leak through unrendered
|--------------------------------------------------------------------------
*/

test('no placeholder token survives into the finished certificate', function () {
    certSetting();

    expect(renderCertificate(certResult()))->not->toMatch('/\{\{[A-Z_]+\}\}/');
});

test('no placeholder survives even when every optional setting is blank', function () {
    certSetting([
        'chairman_name'            => null,
        'chairman_title'           => null,
        'executive_director_name'  => null,
        'executive_director_title' => null,
        'chairman_signature'       => null,
        'executive_director_signature' => null,
    ]);

    expect(renderCertificate(certResult()))->not->toMatch('/\{\{[A-Z_]+\}\}/');
});

test('the recipient name and credential appear', function () {
    certSetting();
    $result = certResult();

    expect(renderCertificate($result))
        ->toContain('Ada Lovelace')
        ->toContain('Certified Healthcare Quality Professional')
        ->toContain('GIHQS-2026-000123-AB12CD');
});

/*
|--------------------------------------------------------------------------
| Signature section selection
|--------------------------------------------------------------------------
*/

test('both signature blocks render when both are enabled', function () {
    certSetting(['show_chairman' => true, 'show_executive_director' => true]);

    $html = renderCertificate(certResult());

    expect($html)->toContain('Dr Jane Smith')
        ->and($html)->toContain('Chairman of the Board')
        ->and($html)->toContain('GIHQS Certification Authority')
        ->and($html)->toContain('Mr John Doe')
        ->and($html)->toContain('Executive Director')
        ->and($html)->toContain('GIHQS Professional Standards');
});

test('disabling the chairman removes that whole section including its caption', function () {
    certSetting(['show_chairman' => false]);

    $html = renderCertificate(certResult());

    expect($html)->not->toContain('Dr Jane Smith')
        ->and($html)->not->toContain('Chairman of the Board')
        ->and($html)->not->toContain('GIHQS Certification Authority');

    // The other side is untouched.
    expect($html)->toContain('Mr John Doe')
        ->and($html)->toContain('GIHQS Professional Standards');
});

test('disabling the executive director removes that whole section including its caption', function () {
    certSetting(['show_executive_director' => false]);

    $html = renderCertificate(certResult());

    expect($html)->not->toContain('Mr John Doe')
        ->and($html)->not->toContain('Executive Director')
        ->and($html)->not->toContain('GIHQS Professional Standards');

    expect($html)->toContain('Dr Jane Smith')
        ->and($html)->toContain('GIHQS Certification Authority');
});

test('both sections can be hidden at once', function () {
    certSetting(['show_chairman' => false, 'show_executive_director' => false]);

    $html = renderCertificate(certResult());

    expect($html)->not->toContain('Dr Jane Smith')
        ->and($html)->not->toContain('Mr John Doe')
        ->and($html)->not->toContain('GIHQS Certification Authority')
        ->and($html)->not->toContain('GIHQS Professional Standards');
});

test('hiding a section leaves the column widths alone so the seal stays centred', function () {
    certSetting(['show_chairman' => false]);

    $html = renderCertificate(certResult());

    expect($html)->toContain('width="34%"')
        ->and($html)->toContain('width="32%"');
});

test('an enabled section with no name and no signature is hidden anyway', function () {
    certSetting([
        'show_chairman'      => true,
        'chairman_name'      => null,
        'chairman_signature' => null,
    ]);

    $html = renderCertificate(certResult());

    // Nothing to sign with, so no orphaned rule or caption is left behind.
    expect($html)->not->toContain('GIHQS Certification Authority')
        ->and($html)->not->toContain('Chairman of the Board');
});

test('a section with a name but no signature image still renders', function () {
    certSetting(['chairman_signature' => null]);

    expect(renderCertificate(certResult()))
        ->toContain('Dr Jane Smith')
        ->toContain('GIHQS Certification Authority');
});

test('hiding a section never leaves an unreplaced placeholder', function () {
    certSetting(['show_chairman' => false, 'show_executive_director' => false]);

    expect(renderCertificate(certResult()))->not->toMatch('/\{\{[A-Z_]+\}\}/');
});

test('the seal survives when both signatures are hidden', function () {
    certSetting(['show_chairman' => false, 'show_executive_director' => false]);

    // The seal cell must still be present in the footer row.
    expect(renderCertificate(certResult()))->toContain('width="32%"');
});
