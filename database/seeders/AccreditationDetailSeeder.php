<?php

namespace Database\Seeders;

use App\Models\AccreditationEligibility;
use App\Models\AccreditationProcess;
use App\Models\AccreditationDomain;
use App\Models\AccreditationInsight;
use Illuminate\Database\Seeder;

class AccreditationDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Eligibility
        $eligibility = AccreditationEligibility::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1' => 'Who Should',
                'title2' => 'Apply',
                'description' => 'Organizations delivering education or credentials in healthcare quality and patient safety.',
            ]
        );
        if ($eligibility->features()->count() === 0) {
            $eligibility->features()->createMany([
                [
                    'title' => 'Universities & Academic Institutions',
                    'description' => 'Degree-linked programs, certificates, and academic training pathways.',
                ],
                [
                    'title' => 'Training Providers & Healthcare Organizations',
                    'description' => 'Workforce programs, patient safety training, quality improvement education.',
                ],
                [
                    'title' => 'Certification & Professional Bodies',
                    'description' => 'Externally issued certifications with defined assessments and governance.',
                ],
            ]);
        }

        // 2. Process
        $process = AccreditationProcess::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1' => 'GIHQS Accreditation',
                'title2' => 'Process',
                'description' => 'The process below gives applicants a clear, structured path from registration through final determination.',
            ]
        );
        if ($process->features()->count() === 0) {
            $process->features()->createMany([
                [
                    'serial' => '01',
                    'title' => 'Register',
                    'subtitle' => 'Formal entry point into the process',
                    'description' => 'Build practical knowledge through education in healthcare quality, patient safety, leadership, and system improvement.',
                ],
                [
                    'serial' => '02',
                    'title' => 'Pay & Receive ID',
                    'subtitle' => 'Activation and reference tracking',
                    'description' => 'After payment, the applicant receives an official GIHQS Accreditation Application ID, submission instructions, and the next procedural requirements.',
                ],
                [
                    'serial' => '03',
                    'title' => 'Self-Assessment',
                    'subtitle' => 'Internal review against standards',
                    'description' => 'The applicant completes a structured self-assessment covering governance, curriculum, instruction, assessment, quality systems, and supporting evidence.',
                ],
                [
                    'serial' => '04',
                    'title' => 'Upload Submission',
                    'subtitle' => 'Evidence package and documentation',
                    'description' => 'The full application and documentation package are submitted for review, including evidence aligned to the standards framework.',
                ],
                [
                    'serial' => '05',
                    'title' => 'Expert Review',
                    'subtitle' => 'Independent evaluation',
                    'description' => 'Qualified reviewers assess the submission for completeness, quality, credibility, transparency, and standards alignment.',
                ],
                [
                    'serial' => '06',
                    'title' => 'Decision',
                    'subtitle' => 'Outcome and feedback',
                    'description' => 'A final accreditation determination is issued and may include feedback, conditions, commendations, or recommendations for future improvement.',
                ],
            ]);
        }

        // 3. Domain
        $domain = AccreditationDomain::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1' => 'Accreditation',
                'title2' => 'Standards Domains',
                'description' => 'These expandable summaries introduce the structure of the framework while reserving full standards detail for the official manual.',
            ]
        );
        if ($domain->features()->count() === 0) {
            $domain->features()->createMany([
                [
                    'domain_serial' => 'Domain 1',
                    'title' => 'Governance, Independence & Ethical Integrity',
                    'description' => 'Establishes institutional structures, oversight practices, and ethical safeguards to support impartiality, accountability, and public trust in the program\'s integrity.',
                ],
                [
                    'domain_serial' => 'Domain 2',
                    'title' => 'Program Purpose, Scope & Population Alignment',
                    'description' => 'Reviews whether the program and verification fulfill a clearly defined public purpose, appropriate scope, and relevance to workforce or professional practice contexts.',
                ],
                [
                    'domain_serial' => 'Domain 3',
                    'title' => 'Curriculum Design & Competency Mapping',
                    'description' => 'Defines curriculum structure, learning pathways, sequencing, learning objectives, and coherence between content and intended outcomes.',
                ],
                [
                    'domain_serial' => 'Domain 4',
                    'title' => 'Faculty, Instructional Capability & Learning Support',
                    'description' => 'Reviews the qualifications, training, and ongoing capacity of instructors, support systems, and adequacy of learner resources.',
                ],
                [
                    'domain_serial' => 'Domain 5',
                    'title' => 'Assessment, Measurement & Credential Reliability',
                    'description' => 'Reviews whether assessment methods are valid, reliable, defensible, and fair—and whether credentials meaningfully reflect demonstrated competence.',
                ],
                [
                    'domain_serial' => 'Domain 6',
                    'title' => 'Quality Improvement, Outcomes & Impact',
                    'description' => 'Considers evidence of performance monitoring, quality improvement cycles, stakeholder feedback, learner outcomes, and employer relevance.',
                ],
                [
                    'domain_serial' => 'Domain 7',
                    'title' => 'Physical Safety & Human Factors Integration',
                    'description' => 'Evaluates how the program integrates personal safety principles, systems thinking, risk awareness, and factors that can reduce human error in workplace settings.',
                ],
                [
                    'domain_serial' => 'Domain 8',
                    'title' => 'Data Accessibility & Learner-Centered Design',
                    'description' => 'Reviews accessibility, fairness, usability, and accommodations to diverse learner needs, and innovation and learner participation.',
                ],
                [
                    'domain_serial' => 'Domain 9',
                    'title' => 'Digital Delivery, Security & Academic Integrity',
                    'description' => 'Examines digital platform technology, privacy safeguards, learner verification, secure authentication, and technical standards for remote participation.',
                ],
                [
                    'domain_serial' => 'Domain 10',
                    'title' => 'Transparency, Public Information & Credential Portability',
                    'description' => 'Considers transparency, public transparency around outcomes, credential representation, and portability or interoperability for employers and other institutions.',
                ],
            ]);
        }

        // 4. Insights
        $insight = AccreditationInsight::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1' => 'GIHQS Accreditation',
                'title2' => 'Insights',
                'description' => 'A deeper explanation of what the accreditation framework is designed to achieve.',
            ]
        );
        if ($insight->features()->count() === 0) {
            $insight->features()->createMany([
                [
                    'title' => 'What does GIHQS accreditation signal?',
                    'tagline' => 'Insight',
                    'description' => 'It signals that a program or certification has been reviewed against a structured standards framework focused on quality, rigor, transparency, and patient safety relevance. It does not simply indicate participation — it indicates demonstrated alignment with defined expectations related to governance, curriculum, assessment, integrity, and public-facing credibility.',
                ],
                [
                    'title' => 'Why are standards domains important?',
                    'tagline' => 'Insight',
                    'description' => 'Domains organize review across governance, delivery, outcomes, safety, ethics, accessibility, and public accountability in a consistent structure. A domain-based structure helps ensure that accreditation review is transparent, comparable, and defensible rather than arbitrary, fragmented, or overly subjective. Structured review strengthens credibility.',
                ],
                [
                    'title' => 'High-level summaries only',
                    'tagline' => null,
                    'description' => 'This page provides a framework overview. Full standards interpretation and scoring remain in the official manual.',
                ],
                [
                    'title' => 'Designed for credibility',
                    'tagline' => null,
                    'description' => 'The structure supports transparent review of programs and certifications in healthcare quality and patient safety.',
                ],
                [
                    'title' => 'Built for modern delivery',
                    'tagline' => null,
                    'description' => 'Digital integrity, learner support, accessibility, and public transparency are directly reflected in the framework.',
                ],
            ]);
        }
    }
}
