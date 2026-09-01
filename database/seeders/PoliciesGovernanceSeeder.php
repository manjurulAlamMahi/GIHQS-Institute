<?php

namespace Database\Seeders;

use App\Models\PoliciesGovernance;
use App\Models\PoliciesGovernanceDocument;
use Illuminate\Database\Seeder;

class PoliciesGovernanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (PoliciesGovernance::count() === 0) {
            $pg = new PoliciesGovernance();
            $pg->id = 1;
            $pg->title1 = 'Policies &';
            $pg->title2 = 'Governance';
            $pg->tagline = 'GIHQS GOVERNANCE FRAMWORK';
            $pg->description = "GIHQS maintains a structured framework of policies and governance documents that support the integrity, transparency, and credibility of its certification, education, and accreditation activities.\r\n\r\nThis page serves as the institutional gateway for core policies that guide candidates, accredited organizations, partners, and stakeholders in understanding the standards and expectations that govern the Institute's operations.";
            
            $pg->inst_title = 'Institutional Policies';
            $pg->inst_tag = 'I';
            $pg->inst_description = 'Core policies governing the operation, public-facing practices, and digital presence of the Institute.';
            
            $pg->cert_title = 'Certification Policies';
            $pg->cert_tag = 'C';
            $pg->cert_description = 'Policies governing eligibility, examination, candidate responsibilities, and ongoing certification requirements.';
            
            $pg->acc_title = 'Accreditation Policies';
            $pg->acc_tag = 'A';
            $pg->acc_description = 'Policies governing standards, evaluation methodology, review processes, and accreditation decision pathways.';
            
            $pg->commitment_title1 = 'Governance';
            $pg->commitment_title2 = 'Commitment';
            $pg->commitment_description = 'GIHQS reviews and updates its policies periodically to maintain alignment with evolving healthcare quality, patient safety, ethical governance, and international best practices in certification and accreditation.';
            
            $pg->save();

            $documents = [
                ['id' => 1, 'policies_governance_id' => 1, 'type' => 'inst', 'title' => 'Privacy Policy', 'file' => null],
                ['id' => 2, 'policies_governance_id' => 1, 'type' => 'inst', 'title' => 'Terms of Use', 'file' => null],
                ['id' => 3, 'policies_governance_id' => 1, 'type' => 'inst', 'title' => 'Terms & Conditions of Purchase', 'file' => null],
                ['id' => 4, 'policies_governance_id' => 1, 'type' => 'inst', 'title' => 'Refund Policy', 'file' => null],
                ['id' => 5, 'policies_governance_id' => 1, 'type' => 'inst', 'title' => 'Disclaimer', 'file' => null],
                ['id' => 6, 'policies_governance_id' => 1, 'type' => 'cert', 'title' => 'Candidate Handbook', 'file' => null],
                ['id' => 7, 'policies_governance_id' => 1, 'type' => 'cert', 'title' => 'Eligibility Requirements', 'file' => null],
                ['id' => 8, 'policies_governance_id' => 1, 'type' => 'cert', 'title' => 'Examination Policies', 'file' => null],
                ['id' => 9, 'policies_governance_id' => 1, 'type' => 'cert', 'title' => 'Retake Policy', 'file' => null],
                ['id' => 10, 'policies_governance_id' => 1, 'type' => 'cert', 'title' => 'Certification Renewal Policy', 'file' => null],
                ['id' => 11, 'policies_governance_id' => 1, 'type' => 'acc', 'title' => 'Accreditation Standards', 'file' => null],
                ['id' => 12, 'policies_governance_id' => 1, 'type' => 'acc', 'title' => 'Accreditation Process', 'file' => null],
                ['id' => 13, 'policies_governance_id' => 1, 'type' => 'acc', 'title' => 'Accreditation Decision Policy', 'file' => null],
                ['id' => 14, 'policies_governance_id' => 1, 'type' => 'acc', 'title' => 'Appeals & Complaints Policy', 'file' => null],
            ];

            foreach ($documents as $doc) {
                $docModel = new PoliciesGovernanceDocument();
                $docModel->id = $doc['id'];
                $docModel->policies_governance_id = $doc['policies_governance_id'];
                $docModel->type = $doc['type'];
                $docModel->title = $doc['title'];
                $docModel->file = $doc['file'];
                $docModel->save();
            }
        }
    }
}
