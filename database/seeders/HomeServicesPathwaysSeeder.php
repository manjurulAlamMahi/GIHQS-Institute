<?php

namespace Database\Seeders;

use App\Models\HomeGihq;
use App\Models\HomeServicesPathway;
use App\Models\HomeProfessionalPathway;
use App\Models\HomeNextStep;
use Illuminate\Database\Seeder;

class HomeServicesPathwaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $homeGihq = HomeGihq::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1' => 'Advancing Healthcare Professionals for',
                'title2' => 'High Reliability Healthcare Systems',
                'tagline' => 'GLOBAL INSTITUTE FOR HEALTHCARE QUALITY & SAFETY',
                'description' => 'GIHQS advances healthcare quality and patient safety through certification, professional education, and accreditation pathways designed to strengthen safer systems, stronger leadership, and responsible innovation in healthcare.',
                'certificate_btn_text' => 'Get Certified',
                'learning_btn_text' => 'Explore Learning',
                'advisory_btn_text' => 'Advisory Services',
                'member_btn_text' => 'Become a Premium Member',
                'professional_ecosystem_title' => 'GIHQS Professional Ecosystem',
                'learning_tagline' => 'Professional Learning',
                'learning_title' => 'Learn',
                'learning_details' => 'Courses, toolkits, and structured development pathways',
                'certificate_tagline' => 'Professional Certification',
                'certificate_title' => 'Certify',
                'certificate_details' => 'AIHQSP, CHSCP, and CCDIP credentials',
                'lead_tagline' => 'Accreditation & Advisory',
                'lead_title' => 'Lead',
                'lead_details' => 'Accreditation and advisory pathways for education providers and healthcare organizations',
                'content_file' => null,
                'injected_status' => 1,
            ]
        );

        if ($homeGihq->servicesPathways()->count() === 0) {
            $homeGihq->servicesPathways()->createMany([
                [
                    'serial' => '01',
                    'target_audience' => 'For professionals',
                    'title' => 'Certification',
                    'description' => 'Earn professional credentials in healthcare quality, patient safety, standards, compliance, and responsible AI in healthcare.',
                    'link_text' => 'Explore Certifications',
                ],
                [
                    'serial' => '02',
                    'target_audience' => 'For learners',
                    'title' => 'Professional Learning',
                    'description' => 'Access courses, toolkits, and structured learning designed for real healthcare system improvement and safer operational performance.',
                    'link_text' => 'View Professional Catalogue',
                ],
                [
                    'serial' => '03',
                    'target_audience' => 'For programs & providers',
                    'title' => 'Accreditation',
                    'description' => 'Apply for accreditation for healthcare education programs and training providers through a structured review and recognition pathway.',
                    'link_text' => 'Start Accreditation',
                ],
                [
                    'serial' => '04',
                    'target_audience' => 'For organizations',
                    'title' => 'Advisory Services',
                    'description' => 'Engage GIHQS advisory services to support healthcare quality, patient safety, accreditation readiness, and system-level performance improvement.',
                    'link_text' => 'Explore Advisory Services',
                ],
            ]);
        }

        if ($homeGihq->professionalPathways()->count() === 0) {
            $homeGihq->professionalPathways()->createMany([
                [
                    'serial' => '01',
                    'title' => 'Learn',
                    'description' => 'Build practical knowledge through education in healthcare quality, patient safety, leadership, and system improvement.',
                    'link_text' => null,
                ],
                [
                    'serial' => '02',
                    'title' => 'Get Certified',
                    'description' => 'Earn professional credentials that validate capability in modern healthcare quality, compliance, and responsible innovation.',
                    'link_text' => null,
                ],
                [
                    'serial' => '03',
                    'title' => 'Maintain Competence',
                    'description' => 'Sustain professional credibility through continuing education, renewal cycles, and ongoing development.',
                    'link_text' => null,
                ],
                [
                    'serial' => '04',
                    'title' => 'Lead Quality Systems',
                    'description' => 'Support stronger organizations and safer systems through leadership, advisory engagement, accreditation readiness, quality oversight, and professional membership.',
                    'link_text' => 'Become a Member',
                ],
            ]);
        }

        if (!$homeGihq->nextStep) {
            $homeGihq->nextStep()->create([
                'title1' => 'Begin Your Professional Pathway with',
                'title2' => 'GIHQS',
                'tagline' => 'Choose your next step',
                'certificate_btn_text' => 'Get Certified',
                'learning_btn_text' => 'Explore Learning',
                'advisory_btn_text' => 'Advisory Services',
                'member_btn_text' => 'Become a Premium Member',
            ]);
        }
    }
}
