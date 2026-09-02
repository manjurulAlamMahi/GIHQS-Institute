<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Catalogue;
use App\Models\UserExamResult;
use App\Services\CertificateRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

trait GeneratesCertificates
{
    /**
     * Generate a PDF certificate for custom or external exams.
     *
     * Template filling lives in CertificateRenderer so the certificate a
     * recipient actually receives can be asserted against as HTML rather than
     * inferred from a binary.
     */
    protected function generateLocalCertificate(UserExamResult $examResult, Catalogue $catalogue, User $user)
    {
        try {
            ini_set('memory_limit', '512M');

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

            $htmlContent = app(CertificateRenderer::class)
                ->render($examResult, $catalogue, $user, $certificateId);

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
