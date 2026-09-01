<?php

namespace Database\Seeders;

use App\Models\HomeRecognizedPathway;
use App\Models\HomeCertificate;
use Illuminate\Database\Seeder;

class HomeFlagshipCertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pathway = HomeRecognizedPathway::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1' => 'Recognized Pathways For',
                'title2' => 'Modern Healthcare Headers',
                'tagline' => null,
                'description' => 'GIHQS certifications recognize professionals who demonstrate advanced knowledge and leadership in healthcare quality, patient safety, standards, compliance, clinical documentation, and the responsible use of AI in healthcare systems.',
                'content_file' => null,
                'injected_status' => 1,
            ]
        );

        if ($pathway->certificates()->count() === 0) {
            $pathway->certificates()->createMany([
                [
                    'short_title' => 'AIHQSP',
                    'title' => 'AI Healthcare Quality & Safety Professional',
                    'icon' => 'uploads/home_certificates/1782747619_6a4291e302aa6.svg',
                    'tagline' => 'AI & Quality Focus',
                    'headline' => 'AI governance for safer, smarter healthcare',
                    'description' => "Built for professionals working at the intersection of healthcare quality, digital transformation, and responsible AI\r\nadoption. Focus: Human-AI collaboration, explainability, oversight, and digital patient safety.",
                    'audience' => 'For Quality leaders, informatics teams, patient safety professionals, and digital health specialists.',
                    'tags' => 'AI in Healthcare, Patient Safety, Quality Systems',
                    'button_text' => 'Explore AIHQSP',
                ],
                [
                    'short_title' => 'CHSCP',
                    'title' => 'Certified Healthcare Standards & Compliance Professional',
                    'icon' => 'uploads/home_certificates/1782747619_6a4291e303be6.svg',
                    'tagline' => 'Standards & Compliance',
                    'headline' => 'Standards, compliance, and accreditation readiness',
                    'description' => 'Designed for professionals responsible for healthcare standards, regulatory compliance, accreditation readiness, clinical governance, quality oversight, and the structured systems that support organizational excellence and accountability.',
                    'audience' => 'For Compliance leaders, accreditation teams, administrators, and quality oversight professionals.',
                    'tags' => 'Compliance, Accreditation, Governance',
                    'button_text' => 'Explore CHSCP',
                ],
                [
                    'short_title' => 'CCDIP',
                    'title' => 'Certified Clinical Documentation Improvement Professional',
                    'icon' => 'uploads/home_certificates/1782747619_6a4291e304675.svg',
                    'tagline' => 'Clinical Documentation',
                    'headline' => 'Clinical documentation that supports quality and clarity',
                    'description' => 'A focused pathway for professionals improving documentation integrity, coding alignment, and the quality of the medical record. Focus: Documentation improvement, record completeness, clarity, and integrity.',
                    'audience' => 'For CDI specialists, HIM professionals, quality teams, and clinical documentation leaders.',
                    'tags' => 'Clinical Documentation, Record Integrity, HIM',
                    'button_text' => 'Explore CCDIP',
                ],
            ]);
        }
    }
}
