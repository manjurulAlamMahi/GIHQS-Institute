<?php

namespace Database\Seeders;

use App\Models\AccreditationHeader;
use Illuminate\Database\Seeder;

class AccreditationHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $header = AccreditationHeader::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1' => 'GIHQS',
                'title2' => 'Accreditation',
                'tagline' => 'ACCREDTATATION',
                'description' => 'Accrediting educational programs and professional certifications in healthcare quality and patient safety through transparent, evidence-based standards.',
                'note' => 'Start with a short registration. After payment, you\'ll receive an Application ID and the full submission package.',
                'apply_btn_text' => 'Apply for Accreditation',
                'download_btn_text' => 'Download Standard Manual (PDF)',
                'download_file' => null,
                'content_file' => null,
                'injected_status' => 1,
            ]
        );

        // Seed tags
        if ($header->tags()->count() === 0) {
            $header->tags()->createMany([
                ['tagname' => 'Transparent Standards'],
                ['tagname' => 'Credential Integrity'],
                ['tagname' => 'Patient Safety Alignment'],
                ['tagname' => 'Global Recognition Focus'],
            ]);
        }

        // Seed key facts
        if ($header->keyfacts()->count() === 0) {
            $header->keyfacts()->createMany([
                ['title' => '10 Standards Domains', 'subtitle' => 'Consistent, structured evaluation'],
                ['title' => 'Evidence-Based Review', 'subtitle' => 'Documented proof and validation'],
                ['title' => 'Independent Expert Panel', 'subtitle' => 'Objective assessment process'],
                ['title' => 'Decision + Final Report', 'subtitle' => 'Clear outcomes and recommendations'],
            ]);
        }
    }
}
