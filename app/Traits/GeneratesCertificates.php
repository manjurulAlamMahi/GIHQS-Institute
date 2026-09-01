<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Catalogue;
use App\Models\UserExamResult;
use App\Models\CertificateSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

trait GeneratesCertificates
{
    /**
     * Generate a PDF certificate for custom or external exams.
     */
    protected function generateLocalCertificate(UserExamResult $examResult, Catalogue $catalogue, User $user)
    {
        try {
            ini_set('memory_limit', '512M');
            $setting = CertificateSetting::first();
            $serviceType = strtolower($catalogue->service_type ?? '');
            if ($serviceType === 'certification') {
                $templateFile = resource_path('views/certification/certification_template.html');
            } else {
                $templateFile = resource_path('views/certification/others_template.html');
            }

            if (file_exists($templateFile)) {
                $htmlContent = file_get_contents($templateFile);
            } else {
                $fallbackTemplate = public_path('uploads/certificate-settings/1784905670_6a637fc682ddf.html');
                if (file_exists($fallbackTemplate)) {
                    $htmlContent = file_get_contents($fallbackTemplate);
                } else {
                    $htmlContent = '<html><body><h1>Certificate of Completion</h1><p>Recipient: {{RECIPIENT_NAME}}</p><p>Credential: {{CREDENTIAL_FULL_NAME}}</p></body></html>';
                }
            }

            // Generate auto serial number: GIHQS-YYYY-XXXXXX-CCCC
            //
            // The serial used to be exactly the zero-padded result id, which made
            // every certificate in the system guessable: GIHQS-2026-000001,
            // -000002, ... walked through the public verification endpoint and
            // returned each holder's name, credential and certificate PDF. The
            // random block keeps serials unguessable. Existing certificates keep
            // the serial already stored against them.
            $certificateId = 'GIHQS-' . now()->format('Y')
                . '-' . str_pad($examResult->id, 6, '0', STR_PAD_LEFT)
                . '-' . strtoupper(bin2hex(random_bytes(3)));
            
            // Dates
            $issueDate = $examResult->created_at->format('M d, Y');
            $validityYears = $catalogue->validity_years;
            $expiryDate = $validityYears ? $examResult->created_at->addYears($validityYears)->format('M d, Y') : 'Lifetime';

            // Verification URL & QR code URL
            $frontendHostFull = env('FRONTEND_URL', 'https://gihqs.vercel.app');
            if (!str_starts_with($frontendHostFull, 'http')) {
                $frontendHostFull = 'https://' . $frontendHostFull;
            }
            $qrUrl = rtrim($frontendHostFull, '/') . '/v/' . $certificateId;
            $qrUrlEncoded = urlencode($qrUrl);

            $verificationUrl = str_replace(['https://', 'http://'], '', $frontendHostFull) . '/v/' . $certificateId;

            // Seal HTML (absolute local path for Dompdf compatibility)
            $sealHtml = '';
            if ($catalogue->certification_seal && file_exists(public_path($catalogue->certification_seal))) {
                $sealHtml = '<img src="' . public_path($catalogue->certification_seal) . '" width="125" height="125" style="width: 125px; height: 125px;" alt="' . ($catalogue->short_title ?? '') . ' Seal">';
            }

            // Signature paths (absolute local paths for Dompdf compatibility)
            $chairmanSigHtml = '';
            if ($setting && $setting->chairman_signature && file_exists(public_path($setting->chairman_signature))) {
                $chairmanSigHtml = '<img src="' . public_path($setting->chairman_signature) . '" height="35" style="height: 35px; width: auto; display: block; margin: 0 auto 2px;" alt="Chairman Signature">';
            }

            $execSigHtml = '';
            if ($setting && $setting->executive_director_signature && file_exists(public_path($setting->executive_director_signature))) {
                $execSigHtml = '<img src="' . public_path($setting->executive_director_signature) . '" height="35" style="height: 35px; width: auto; display: block; margin: 0 auto 2px;" alt="Executive Director Signature">';
            }

            // Replace standard placeholders
            $replacements = [
                '{{RECIPIENT_NAME}}' => $user->full_name,
                '{{CREDENTIAL_FULL_NAME}}' => $catalogue->title,
                '{{CERTIFICATION_CODE}}' => $catalogue->short_title,
                '{{CERTIFICATE_ID}}' => $certificateId,
                '{{ISSUE_DATE}}' => $issueDate,
                '{{EXPIRY_DATE}}' => $expiryDate,
                '{{VERIFICATION_URL}}' => $verificationUrl,
                '{{VERIFICATION_URL_QR}}' => $qrUrlEncoded,
                '{{CREDENTIAL_STATEMENT}}' => $catalogue->credential_statement ?? '',
                '{{SEAL_PATH}}' => $catalogue->certification_seal ? public_path($catalogue->certification_seal) : '',
                '{{SEAL_HTML}}' => $sealHtml,
                '{{CHAIRMAN_SIGNATURE_HTML}}' => $chairmanSigHtml,
                '{{EXECUTIVE_DIRECTOR_SIGNATURE_HTML}}' => $execSigHtml,
            ];

            $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlContent);

            // Convert relative assets path to absolute local paths for Dompdf compatibility
            $htmlContent = str_replace("url('assets/", "url('" . public_path('uploads/certificate-settings/assets/'), $htmlContent);
            $htmlContent = str_replace('src="assets/', 'src="' . public_path('uploads/certificate-settings/assets/'), $htmlContent);

            // Generate PDF and save
            $dir = public_path('uploads/certificates');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            // The PDF lives under public/, so the filename carries random entropy
            // rather than a predictable id + timestamp that could be walked.
            $filename = 'certificate_' . $examResult->id . '_' . bin2hex(random_bytes(16)) . '.pdf';
            $savePath = 'uploads/certificates/' . $filename;

            $pdf = Pdf::loadHTML($htmlContent)
                ->setPaper('a4', 'landscape')
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true);
            $pdf->save(public_path($savePath));

            // Update exam result columns
            $examResult->update([
                'certificate_serial_number' => $certificateId,
                'certificate_url' => asset($savePath),
                'download_certificate' => asset($savePath),
            ]);

        } catch (\Throwable $e) {
            Log::error('Certificate generation failed: ' . $e->getMessage());
        }
    }
}
