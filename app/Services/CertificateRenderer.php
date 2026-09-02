<?php

namespace App\Services;

use App\Models\Catalogue;
use App\Models\CertificateSetting;
use App\Models\User;
use App\Models\UserExamResult;

/**
 * Fills the certificate template and returns finished HTML.
 *
 * Kept separate from PDF generation so the certificate a recipient actually
 * receives can be asserted against directly, rather than inferred from a binary.
 */
class CertificateRenderer
{
    /** Captions beneath each signature. Moved out of the templates so they
     *  disappear together with their block instead of stranding. */
    private const CAPTION_CHAIRMAN           = 'GIHQS Certification Authority';
    private const CAPTION_EXECUTIVE_DIRECTOR = 'GIHQS Professional Standards';

    /**
     * Render the certificate for a passed exam result.
     */
    public function render(
        UserExamResult $result,
        Catalogue $catalogue,
        User $user,
        string $certificateId
    ): string {
        $setting = CertificateSetting::first();
        $html    = $this->template($catalogue);

        foreach ($this->replacements($result, $catalogue, $user, $certificateId, $setting) as $token => $value) {
            $html = str_replace($token, $value, $html);
        }

        // Relative asset paths must become absolute for Dompdf.
        $html = str_replace("url('assets/", "url('" . public_path('uploads/certificate-settings/assets/'), $html);
        $html = str_replace('src="assets/', 'src="' . public_path('uploads/certificate-settings/assets/'), $html);

        // Anything still unreplaced would print as a literal {{TOKEN}} on a
        // certificate, so it is stripped rather than shown.
        return preg_replace('/\{\{[A-Z_]+\}\}/', '', $html);
    }

    /**
     * @return array<string, string>
     */
    private function replacements(
        UserExamResult $result,
        Catalogue $catalogue,
        User $user,
        string $certificateId,
        ?CertificateSetting $setting
    ): array {
        $frontend = $this->frontendHost();
        $qrUrl    = rtrim($frontend, '/') . '/v/' . $certificateId;

        return [
            '{{RECIPIENT_NAME}}'         => e($user->full_name),
            '{{CREDENTIAL_FULL_NAME}}'   => e($catalogue->title),
            '{{CERTIFICATION_CODE}}'     => e((string) $catalogue->short_title),
            '{{CERTIFICATE_ID}}'         => e($certificateId),
            '{{ISSUE_DATE}}'             => $result->created_at->format('M d, Y'),
            // The client asked for an issue date only. The token is replaced
            // with nothing so a stale template cannot print a literal
            // placeholder where "Valid Until" used to be.
            '{{EXPIRY_DATE}}'            => '',
            '{{VERIFICATION_URL}}'       => e(str_replace(['https://', 'http://'], '', $frontend) . '/v/' . $certificateId),
            '{{VERIFICATION_URL_QR}}'    => urlencode($qrUrl),
            '{{CREDENTIAL_STATEMENT}}'   => e((string) ($catalogue->credential_statement ?? '')),
            '{{SEAL_PATH}}'              => $catalogue->certification_seal
                ? public_path($catalogue->certification_seal)
                : '',
            '{{SEAL_HTML}}'              => $this->sealHtml($catalogue),

            // Whole signature sections. Hiding one has to remove its rule and
            // caption too, not just the image, so the block is built here rather
            // than scaffolded in the template.
            '{{CHAIRMAN_BLOCK}}' => $this->signatureBlock(
                visible:   $setting?->show_chairman ?? true,
                name:      $setting?->chairman_name,
                title:     $setting?->chairmanTitle() ?? CertificateSetting::DEFAULT_CHAIRMAN_TITLE,
                signature: $setting?->chairman_signature,
                alt:       'Chairman Signature',
                caption:   self::CAPTION_CHAIRMAN,
            ),
            '{{EXECUTIVE_DIRECTOR_BLOCK}}' => $this->signatureBlock(
                visible:   $setting?->show_executive_director ?? true,
                name:      $setting?->executive_director_name,
                title:     $setting?->executiveDirectorTitle() ?? CertificateSetting::DEFAULT_EXECUTIVE_DIRECTOR_TITLE,
                signature: $setting?->executive_director_signature,
                alt:       'Executive Director Signature',
                caption:   self::CAPTION_EXECUTIVE_DIRECTOR,
            ),

            // Retired placeholders. A customised template may still contain
            // them; they resolve to nothing rather than printing a literal token.
            '{{CHAIRMAN_SIGNATURE_HTML}}'           => '',
            '{{CHAIRMAN_NAME}}'                     => '',
            '{{CHAIRMAN_TITLE}}'                    => '',
            '{{EXECUTIVE_DIRECTOR_SIGNATURE_HTML}}' => '',
            '{{EXECUTIVE_DIRECTOR_NAME}}'           => '',
            '{{EXECUTIVE_DIRECTOR_TITLE}}'          => '',
        ];
    }

    /**
     * Build one complete signature section, or nothing at all.
     *
     * A section is shown when its toggle is on AND it has something to sign
     * with. An enabled section with neither a name nor a signature image would
     * otherwise print a bare rule and caption, which reads worse than an empty
     * space.
     */
    private function signatureBlock(
        bool $visible,
        ?string $name,
        string $title,
        ?string $signature,
        string $alt,
        string $caption
    ): string {
        $signatureHtml = $this->signatureHtml($signature, $alt);

        if (!$visible || (blank($name) && $signatureHtml === '')) {
            // The cell survives at its declared width so the seal stays centred.
            return '&nbsp;';
        }

        return '<table cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 0 auto; border-collapse: collapse;">'
            . '<tr><td align="center" style="text-align: center; height: 35px; vertical-align: bottom;">'
            . $signatureHtml
            . '</td></tr>'
            . '<tr><td width="180" height="1" style="width: 180px; height: 1px; background-color: #0F2F26; font-size: 1px; line-height: 1px;">&nbsp;</td></tr>'
            . '<tr><td align="center" style="padding-top: 4px; text-align: center;">'
            . $this->nameHtml($name)
            . '<span style="font-family: DejaVu Sans, Arial, sans-serif; font-size: 8pt; letter-spacing: 1px; color: #0F2F26; text-transform: uppercase;">'
            . e($title) . '</span>'
            . '</td></tr>'
            . '<tr><td align="center" style="padding-top: 2px; text-align: center;">'
            . '<span style="font-family: DejaVu Sans, Arial, sans-serif; font-size: 7pt; color: #0F2F26;">'
            . e($caption) . '</span>'
            . '</td></tr>'
            . '</table>';
    }

    /**
     * A blank name renders nothing at all, so the title moves up rather than
     * sitting under an empty line.
     */
    private function nameHtml(?string $name): string
    {
        if (blank($name)) {
            return '';
        }

        return '<span style="font-family: DejaVu Sans, Arial, sans-serif; font-size: 8.5pt;'
            . ' font-weight: bold; color: #0F2F26; line-height: 1.2;">' . e($name) . '</span><br>';
    }

    private function signatureHtml(?string $path, string $alt): string
    {
        if (blank($path) || !file_exists(public_path($path))) {
            return '';
        }

        return '<img src="' . public_path($path) . '" height="35"'
            . ' style="height: 35px; width: auto; display: block; margin: 0 auto 2px;"'
            . ' alt="' . e($alt) . '">';
    }

    private function sealHtml(Catalogue $catalogue): string
    {
        if (blank($catalogue->certification_seal) || !file_exists(public_path($catalogue->certification_seal))) {
            return '';
        }

        return '<img src="' . public_path($catalogue->certification_seal) . '" width="125" height="125"'
            . ' style="width: 125px; height: 125px;" alt="' . e((string) $catalogue->short_title) . ' Seal">';
    }

    private function template(Catalogue $catalogue): string
    {
        $isCertification = strtolower((string) $catalogue->service_type) === 'certification';

        $file = resource_path(
            $isCertification
                ? 'views/certification/certification_template.html'
                : 'views/certification/others_template.html'
        );

        if (file_exists($file)) {
            return file_get_contents($file);
        }

        $fallback = public_path('uploads/certificate-settings/1784905670_6a637fc682ddf.html');

        if (file_exists($fallback)) {
            return file_get_contents($fallback);
        }

        return '<html><body><h1>Certificate of Completion</h1>'
            . '<p>Recipient: {{RECIPIENT_NAME}}</p>'
            . '<p>Credential: {{CREDENTIAL_FULL_NAME}}</p>'
            . '<p>Date of Issue: {{ISSUE_DATE}}</p></body></html>';
    }

    private function frontendHost(): string
    {
        $host = env('FRONTEND_URL', 'https://gihqs.vercel.app');

        return str_starts_with($host, 'http') ? $host : 'https://' . $host;
    }
}
