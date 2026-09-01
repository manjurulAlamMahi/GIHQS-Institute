<?php

namespace Database\Seeders;

use App\Models\AdvisoryHeader;
use App\Models\AdvisoryFocus;
use App\Models\AdvisoryFocusFeature;
use App\Models\AdvisoryScope;
use App\Models\AdvisoryScopeFeature;
use App\Models\AdvisoryDeliverableCard;
use App\Models\AdvisoryDeliverableCardFeature;
use App\Models\AdvisoryService;
use App\Models\AdvisoryServiceFeature;
use App\Models\AdvisoryDiscussCard;
use Illuminate\Database\Seeder;

class AdvisoryServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Advisory Header
        if (AdvisoryHeader::count() === 0) {
            AdvisoryHeader::create([
                'id' => 1,
                'title1' => 'Strategic Advisory for',
                'title2' => 'Quality, Safety, Standards & Performance',
                'tagline' => 'GIHQS ADVISORY SERVICES',
                'description' => 'GIHQS provides specialized advisory support to healthcare organizations, regulators, accreditation bodies, healthcare groups, and education providers seeking to strengthen patient safety, healthcare risk management, quality improvement, performance oversight, standards development, accreditation readiness, surveyor capability, and selected Al governance within healthcare environments.',
                'content_file' => null,
                'injected_status' => 1,
            ]);
        }

        // 2. Advisory Focus
        if (AdvisoryFocus::count() === 0) {
            $focus = AdvisoryFocus::create([
                'id' => 1,
                'title' => 'Focused Al Integration',
                'description' => 'GIHQS maintains a focused Al lens within its broader advisory work, supporting healthcare organizations that need stronger oversight for Al-enabled workflows, risk awareness, accountability, and safe implementation within healthcare quality and patient safety systems.',
            ]);

            // Seed Focus Features
            AdvisoryFocusFeature::create([
                'advisory_focus_id' => $focus->id,
                'description' => 'Al governance considerations within healthcare standards and review structures',
            ]);
            AdvisoryFocusFeature::create([
                'advisory_focus_id' => $focus->id,
                'description' => 'Quality and patient safety implications of Al-enabled processes',
            ]);
            AdvisoryFocusFeature::create([
                'advisory_focus_id' => $focus->id,
                'description' => 'Responsible adoption within high-reliability healthcare systems',
            ]);
        }

        // 3. Advisory Scope
        if (AdvisoryScope::count() === 0) {
            $scope = AdvisoryScope::create([
                'id' => 1,
                'title1' => 'Advisory',
                'title2' => 'Scope',
                'description' => 'Our advisory services are designed for organizations that need more than general consultation. GIHQS supports the design of practical, institution- level frameworks that help healthcare leaders improve performance, reduce risk, strengthen governance, and build greater confidence in quality, safety, accountability, and implementation readiness across healthcare environments.',
            ]);

            // Seed Scope Features
            AdvisoryScopeFeature::create([
                'advisory_scope_id' => $scope->id,
                'icon' => 'uploads/advisory_scope/1782741538_6a427a22daf73.svg',
                'title' => 'Patient Safety & Risk Management',
                'description' => 'Advisory support for safety priorities, incident review structures, escalation pathways, healthcare risk management frameworks, and organizational accountability systems.',
            ]);
            AdvisoryScopeFeature::create([
                'advisory_scope_id' => $scope->id,
                'icon' => 'uploads/advisory_scope/1782741538_6a427a22dc445.svg',
                'title' => 'Quality Improvement',
                'description' => 'Structured support for improvement priorities, KPI alignment, governance enhancement, operational discipline, and performance improvement initiatives.',
            ]);
            AdvisoryScopeFeature::create([
                'advisory_scope_id' => $scope->id,
                'icon' => 'uploads/advisory_scope/1782741538_6a427a22dcef5.svg',
                'title' => 'Dashboards & Performance Intelligence',
                'description' => 'Design of KPI frameworks, reporting logic, dashboard structures, and executive performance views that support better oversight and decision-making.',
            ]);
            AdvisoryScopeFeature::create([
                'advisory_scope_id' => $scope->id,
                'icon' => 'uploads/advisory_scope/1782741538_6a427a22dd915.svg',
                'title' => 'Standards, Accreditation & Surveyor Development',
                'description' => 'Development of standards frameworks, accreditation readiness structures, measurable elements, evaluation logic, and surveyor capability-building support.',
            ]);
        }

        // 4. Advisory Deliverable Card
        if (AdvisoryDeliverableCard::count() === 0) {
            $deliverable = AdvisoryDeliverableCard::create([
                'id' => 1,
                'title1' => 'Typical',
                'title2' => 'Deliverables',
                'description' => 'Engagement outputs are tailored to the organization\'s scope, maturity, and governance needs, with emphasis on clarity, executive usability, implementation readiness, and measurable system improvement.',
            ]);

            // Seed Deliverable Features
            AdvisoryDeliverableCardFeature::create([
                'advisory_deliverable_card_id' => $deliverable->id,
                'name' => 'Standards Frameworks',
            ]);
            AdvisoryDeliverableCardFeature::create([
                'advisory_deliverable_card_id' => $deliverable->id,
                'name' => 'Risk Management Models',
            ]);
            AdvisoryDeliverableCardFeature::create([
                'advisory_deliverable_card_id' => $deliverable->id,
                'name' => 'Quality Roadmaps',
            ]);
            AdvisoryDeliverableCardFeature::create([
                'advisory_deliverable_card_id' => $deliverable->id,
                'name' => 'Executive Dashboards',
            ]);
            AdvisoryDeliverableCardFeature::create([
                'advisory_deliverable_card_id' => $deliverable->id,
                'name' => 'Governance Structures',
            ]);
        }

        // 5. Advisory Service Packages
        if (AdvisoryService::count() === 0) {
            $service = AdvisoryService::create([
                'id' => 1,
                'title1' => 'Service',
                'title2' => 'Packages',
                'description' => 'GIHQS advisory engagements are structured to support healthcare organizations seeking stronger systems, clearer governance, and measurable improvement across quality, patient safety, risk management, standards, accreditation, and performance oversight.',
            ]);

            // Seed Service Features
            AdvisoryServiceFeature::create([
                'advisory_service_id' => $service->id,
                'serial_number' => '01',
                'tagline' => 'Foundational',
                'title' => 'Quality, Safety & Risk Diagnostic',
                'description' => 'A focused baseline engagement for organizations that need a clearer view of current gaps, governance weaknesses, safety priorities, and improvement opportunities.',
            ]);
            AdvisoryServiceFeature::create([
                'advisory_service_id' => $service->id,
                'serial_number' => '02',
                'tagline' => 'Advanced',
                'title' => 'Performance, Improvement & Dashboard Design',
                'description' => 'A structured engagement for healthcare organizations seeking stronger KPI architecture, executive visibility, quality improvement alignment, and more disciplined performance oversight.',
            ]);
            AdvisoryServiceFeature::create([
                'advisory_service_id' => $service->id,
                'serial_number' => '03',
                'tagline' => 'Strategic',
                'title' => 'Standards, Accreditation & System Development',
                'description' => 'A higher-level engagement for organizations seeking to build or strengthen healthcare standards, accreditation frameworks, readiness capability, surveyor development, and institutional review systems.',
            ]);
        }

        // 6. Advisory Discuss Card
        if (AdvisoryDiscussCard::count() === 0) {
            AdvisoryDiscussCard::create([
                'id' => 1,
                'title1' => 'Discuss Your',
                'title2' => 'Advisory Needs',
                'description' => 'Whether your organization is seeking stronger patient safety systems, healthcare risk management structures, performance dashboards, quality improvement guidance, standards development, or accreditation readiness support, GIHQS can help shape a more reliable and implementation-ready path forward.',
                'button_text' => 'Apply for Accreditation',
            ]);
        }
    }
}
