<?php

namespace Database\Seeders;

use App\Models\StrategicAdvisory;
use App\Models\StrategicAdvisoryFeature;
use Illuminate\Database\Seeder;

class StrategicAdvisorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (StrategicAdvisory::count() === 0) {
            $sa = new StrategicAdvisory();
            $sa->id = 1;
            $sa->title1 = 'Strategic';
            $sa->title2 = 'Advisory Panel';
            $sa->tagline = 'GIHQS GOVERNANCE';
            $sa->short_description = "The Strategic Advisory Panel provides independent guidance to support the long-term vision, strategic direction, and institutional development of the Global Institute for Healthcare Quality and Safety (GIHQS). GIHQS advisory panels bring together experienced leaders from healthcare systems, accreditation bodies, academia,and healthcare quality organizations.";

            $sa->purpose_tagline = 'Purpose';
            $sa->purpose_title = 'Purpose of the Panel';
            $sa->purpose_short_description = 'The Strategic Advisory Panel contributes high-level perspectives on healthcare quality, patient safety, high-reliability healthcare systems, digital health governance, and professional education. The panel supports GIHQS in strengthening its global positioning, institutional strategy, and alignment with international best practices.';

            $sa->advisory_title = 'Advisory Focus';

            $sa->panel_title = 'Panel Formation in Progress';
            $sa->panel_short_description = 'The GIHQS Strategic Advisory Panel is currently being established. Appointments are underway with several experienced professionals in healthcare quality, patient safety, accreditation, and healthcare leadership. Additional panel members will be announced as appointments are finalized.';

            $sa->appointment_title = 'Appointment Terms';
            $sa->appointment_short_description = "Members of the Accreditation Review Panel are appointed by GIHQS based on demonstrated expertise in accreditation, healthcare quality, regulation, education, or evaluation.\r\n\r\nAppointments are typically for a renewable two-year term.\r\n\r\nPanel members may participate in application review, documentation evaluation, peer consultation, and accreditation recommendation activities.";

            $sa->conflict_title = 'Conflict of Interest';
            $sa->conflict_short_description = "GIHQS is committed to maintaining impartiality and integrity in its accreditation processes.\r\n\r\nPanel members are expected to disclose any actual, potential, or perceived conflicts of interest that may influence or appear to influence their independent judgment.\r\n\r\nWhere appropriate, GIHQS may request formal declarations of interest and may restrict participation in specific reviews to protect the integrity of accreditation decisions.";

            $sa->expression_title = 'Expressions of Interest';
            $sa->expression_description = '<p>Experienced professionals with expertise in healthcare accreditation, standards development, quality improvement, clinical governance, regulatory oversight, or program evaluation who are interested in contributing to the Accreditation Review Panel may contact GIHQS.</p><p>&nbsp;</p><p>To express interest, please send a current CV and a short professional biography to <a href="mailto:info@gihqs.com">info@gihqs.com</a></p><p>&nbsp;</p><p>Additional appointments will be announced as the panel is finalized.</p>';

            $sa->save();

            $features = [
                [
                    'id'                    => 1,
                    'strategic_advisory_id' => 1,
                    'description'          => 'Healthcare quality and patient safety leadership',
                ],
                [
                    'id'                    => 2,
                    'strategic_advisory_id' => 1,
                    'description'          => 'High-reliability healthcare systems',
                ],
                [
                    'id'                    => 3,
                    'strategic_advisory_id' => 1,
                    'description'          => 'Artificial intelligence governance in healthcare',
                ],
                [
                    'id'                    => 4,
                    'strategic_advisory_id' => 1,
                    'description'          => 'Global healthcare policy and regulatory developments',
                ],
                [
                    'id'                    => 5,
                    'strategic_advisory_id' => 1,
                    'description'          => 'Professional certification and education strategy',
                ],
                [
                    'id'                    => 6,
                    'strategic_advisory_id' => 1,
                    'description'          => 'Institutional strategy and international collaboration',
                ],
            ];

            foreach ($features as $feature) {
                $featureModel = new StrategicAdvisoryFeature();
                $featureModel->id = $feature['id'];
                $featureModel->strategic_advisory_id = $feature['strategic_advisory_id'];
                $featureModel->description = $feature['description'];
                $featureModel->save();
            }
        }
    }
}
