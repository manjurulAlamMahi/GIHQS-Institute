<?php

namespace App\Services;

use App\Models\AccreditationApplication;
use App\Models\CertificateSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class AccreditationCertificateService
{
    /**
     * Generate PDF Accreditation Certificate for an application.
     */
    public static function generatePdf(AccreditationApplication $application)
    {
        try {
            ini_set('memory_limit', '512M');

            // Ensure verification code & dates are populated
            if (!$application->verification_code) {
                $application->verification_code = 'GIHQS-ACC-' . now()->format('Y') . '-' . str_pad($application->id, 5, '0', STR_PAD_LEFT);
            }

            if (!$application->issued_at) {
                $application->issued_at = now();
            }

            $application->saveQuietly();

            $templateFile = resource_path('views/accreditation/accreditation_template.html');

            if (!file_exists($templateFile)) {
                Log::error('Accreditation template file missing: ' . $templateFile);
                return null;
            }

            $htmlContent = file_get_contents($templateFile);

            // Setting signatures and branding
            $setting = CertificateSetting::first();

            // Verification URL & QR code URL
            $frontendHostFull = env('FRONTEND_URL', 'https://gihqs.org');
            if (!str_starts_with($frontendHostFull, 'http')) {
                $frontendHostFull = 'https://' . $frontendHostFull;
            }
            $qrUrl = rtrim($frontendHostFull, '/') . '/accreditation/verify/' . $application->verification_code;
            $qrUrlEncoded = urlencode($qrUrl);

            $verificationUrl = str_replace(['https://', 'http://'], '', $frontendHostFull) . '/accreditation/verify/' . $application->verification_code;

            // Recipient name & category title
            $category = strtolower($application->applicant_category ?? 'institution');

            if (str_contains($category, 'consultant')) {
                $recipientName = $application->program_name
                    ? $application->program_name . ' (Owner: ' . $application->applicant_name . ')'
                    : $application->applicant_name;
                $accreditationTitle = 'Certificate of Consultant Accreditation';
                $categoryBadge = 'CONSULTANT ACCREDITATION';
            } elseif (str_contains($category, 'program')) {
                $recipientName = $application->program_name ?: $application->applicant_name;
                $accreditationTitle = 'Certificate of Program Accreditation';
                $categoryBadge = 'PROGRAM ACCREDITATION';
            } else {
                $recipientName = $application->applicant_name;
                $accreditationTitle = 'Certificate of Institutional Accreditation';
                $categoryBadge = 'INSTITUTIONAL ACCREDITATION';
            }

            $accreditationStatement = 'has met all rigorous standards of quality, governance, academic excellence, and continuous improvement established by the Global Institute for Healthcare Quality & Standards (GIHQS).';

            // Dates
            $issueDate = $application->issued_at ? $application->issued_at->format('M d, Y') : now()->format('M d, Y');
            $expiryDate = $application->expires_at ? $application->expires_at->format('M d, Y') : 'Ongoing';

            // Signatures HTML
            $chairmanSigHtml = '';
            if ($setting && $setting->chairman_signature && file_exists(public_path($setting->chairman_signature))) {
                $chairmanSigHtml = '<img src="' . public_path($setting->chairman_signature) . '" height="35" style="height: 35px; width: auto; display: block; margin: 0 auto 2px;" alt="Chairman Signature">';
            }

            $execSigHtml = '';
            if ($setting && $setting->executive_director_signature && file_exists(public_path($setting->executive_director_signature))) {
                $execSigHtml = '<img src="' . public_path($setting->executive_director_signature) . '" height="35" style="height: 35px; width: auto; display: block; margin: 0 auto 2px;" alt="Executive Director Signature">';
            }

            // Seal Path
            $sealPath = resource_path('views/accreditation/accredited-seal.png');
            if (!file_exists($sealPath)) {
                $sealPath = ($setting && $setting->seal && file_exists(public_path($setting->seal))) 
                    ? public_path($setting->seal) 
                    : '';
            }

            // Replace placeholders
            $replacements = [
                '{{ACCREDITATION_CODE}}' => $application->verification_code,
                '{{ACCREDITATION_TITLE}}' => $accreditationTitle,
                '{{APPLICANT_NAME}}' => $recipientName,
                '{{ACCREDITATION_STATEMENT}}' => $accreditationStatement,
                '{{CATEGORY_BADGE}}' => $categoryBadge,
                '{{VERIFICATION_CODE}}' => $application->verification_code,
                '{{ISSUE_DATE}}' => $issueDate,
                '{{EXPIRY_DATE}}' => $expiryDate,
                '{{VERIFICATION_URL}}' => $verificationUrl,
                '{{VERIFICATION_URL_QR}}' => $qrUrlEncoded,
                '{{SEAL_PATH}}' => $sealPath,
                '{{CHAIRMAN_SIGNATURE_HTML}}' => $chairmanSigHtml,
                '{{EXECUTIVE_DIRECTOR_SIGNATURE_HTML}}' => $execSigHtml,
            ];

            $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlContent);

            // Directory save path
            $dir = public_path('uploads/accreditation-certificates');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $filename = 'accreditation_certificate_' . $application->id . '_' . time() . '.pdf';
            $relativePath = 'uploads/accreditation-certificates/' . $filename;
            $fullPath = public_path($relativePath);

            $pdf = Pdf::loadHTML($htmlContent)
                ->setPaper('a4', 'landscape')
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true);
            $pdf->save($fullPath);

            $application->certificate_pdf = $relativePath;
            $application->saveQuietly();

            return asset($relativePath);

        } catch (\Throwable $e) {
            Log::error('Accreditation Certificate generation failed: ' . $e->getMessage());
            return null;
        }
    }
}
