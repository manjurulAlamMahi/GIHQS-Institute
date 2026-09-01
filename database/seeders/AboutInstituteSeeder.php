<?php

namespace Database\Seeders;

use App\Models\AboutInstitute;
use App\Models\AboutInstituteFaq;
use Illuminate\Database\Seeder;

class AboutInstituteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (AboutInstitute::count() === 0) {
            $aboutInstitute = new AboutInstitute();
            $aboutInstitute->id = 1;
            $aboutInstitute->title1 = 'Building the Future of  Healthcare Quality,';
            $aboutInstitute->title2 = 'Safety &  Responsible Innovation';
            $aboutInstitute->tag_line = 'ABOUT THE INSTITUTE';
            $aboutInstitute->description = '<p>Healthcare systems around the world share a common responsibility: to deliver safe, high-quality care to every patient, every time. Yet preventable harm, system failures, and uneven outcomes continue to challenge healthcare organizations across diverse settings.</p><p>&nbsp;</p><p>The Global Institute for Healthcare Quality &amp; Safety (GIHQS) was established in response to this challenge — with a vision of healthcare systems that are consistently safe, high-reliability, and trusted by the patients and communities they serve.</p><p>&nbsp;</p><p>GIHQS is founded on a simple but powerful belief:&nbsp; safer healthcare systems are built by knowledgeable, skilled, and<br>courageous professionals equipped to improve them.</p><p>&nbsp;</p><p>Through professional certification, education, accreditation, and advisory services, GIHQS supports individuals and organizations committed to advancing excellence in healthcare quality, patient safety, high-reliability healthcare systems, and the responsible use of Artificial Intelligence (AI) in healthcare.</p>';
            $aboutInstitute->image = 'uploads/about_institute/1782296888_6a3bb138846f8.png';
            $aboutInstitute->save();

            $faqs = [
                [
                    'id'                    => 1,
                    'about_institute_id'    => 1,
                    'faq_title'             => 'Healthcare Quality & Performance Improvement',
                    'faq_short_description' => 'Focuses on equipping professionals with the knowledge and practical methods needed to improve care processes, reduce unwanted variation, strengthen outcomes, and build healthcare systems that continuously learn and improve.',
                ],
                [
                    'id'                    => 2,
                    'about_institute_id'    => 1,
                    'faq_title'             => 'Patient Safety Science & Systems Thinking',
                    'faq_short_description' => 'Emphasizes the prevention of avoidable harm through safety culture, human factors awareness, systemsthinking, root cause analysis, and the design of safer healthcare environments for patients and care teams.',
                ],
                [
                    'id'                    => 3,
                    'about_institute_id'    => 1,
                    'faq_title'             => 'Clinical Governance & Accreditation Leadership',
                    'faq_short_description' => 'Supports leadership in governance, standards interpretation, compliance readiness, accreditation preparation, and organizational accountability to promote trustworthy, high-performing healthcare systems.',
                ],
                [
                    'id'                    => 4,
                    'about_institute_id'    => 1,
                    'faq_title'             => 'Advisory Services for Healthcare Transformation',
                    'faq_short_description' => 'Provides strategic advisory support to healthcare organizations seeking to strengthen quality, patient safety, healthcare standards, accreditation readiness, operational performance, and leadership capability in complex healthcare environments.',
                ],
                [
                    'id'                    => 5,
                    'about_institute_id'    => 1,
                    'faq_title'             => 'Responsible Artificial Intelligence (Al) in Healthcare',
                    'faq_short_description' => "Prepares professionals to guide the safe, ethical, and effective adoption of Artificial Intelligence (Al) in healthcare - with attention to governance, bias,\r\ntransparency, human oversight, workflow integration, and patient safety.",
                ],
                [
                    'id'                    => 6,
                    'about_institute_id'    => 1,
                    'faq_title'             => 'High-Reliability Healthcare Systems & Leadership',
                    'faq_short_description' => 'Promotes the principles and leadership practices required to build resilient healthcare organizations that anticipate risk, learn from failure, respond effectively, and advance toward zero preventable harm.',
                ],
            ];

            foreach ($faqs as $faq) {
                $faqModel = new AboutInstituteFaq();
                $faqModel->id = $faq['id'];
                $faqModel->about_institute_id = $faq['about_institute_id'];
                $faqModel->faq_title = $faq['faq_title'];
                $faqModel->faq_short_description = $faq['faq_short_description'];
                $faqModel->save();
            }
        }
    }
}
