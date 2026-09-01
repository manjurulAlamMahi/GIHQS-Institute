<?php

namespace Database\Seeders;

use App\Models\AccreditationReview;
use App\Models\AccreditationReviewFeature;
use Illuminate\Database\Seeder;

class AccreditationReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (AccreditationReview::count() === 0) {
            $ar = new AccreditationReview();
            $ar->id = 1;
            $ar->title1 = 'Accreditation';
            $ar->title2 = 'Review Panel';
            $ar->tagline = 'GIHQS ACCREDITATION GOVERNANCE';
            $ar->short_description = 'The Accreditation Review Panel conducts independent technical evaluation of accreditation applications submitted to the Global Institute for Healthcare Quality and Safety (GIHQS). GIHQS review panels bring together experienced leaders from healthcare systems, accreditation bodies, academia, and healthcare quality organizations.';

            $ar->purpose_tagline = 'Purpose';
            $ar->purpose_title = 'Role of the Review Panel';
            $ar->purpose_short_description = 'The Accreditation Review Panel supports the integrity and credibility of GIHQS accreditation activities through independent technical evaluation of submitted applications, supporting evidence, and program documentation.';

            $ar->review_title = 'Evaluation Responsibilities';

            $ar->panel_title = 'Panel Formation in Progress';
            $ar->panel_short_description = 'The GIHQS Accreditation Review Panel is currently being established. Appointments are underway with several experienced professionals in healthcare quality, patient safety, accreditation, and healthcare leadership. Additional panel members will be announced as appointments are finalized.';

            $ar->appointment_title = 'Appointment Terms';
            $ar->appointment_short_description = "Members of the Accreditation Review Panel are appointed by GIHQS based on demonstrated expertise in accreditation, healthcare quality, regulation, education, or evaluation.\r\n\r\nAppointments are typically for a renewable two-year term.\r\n\r\nPanel members may participate in application review, documentation evaluation, peer consultation, and accreditation recommendation activities.";

            $ar->conflict_title = 'Conflict of Interest';
            $ar->conflict_short_description = "GIHQS is committed to maintaining impartiality and integrity in its accreditation processes.\r\n\r\nPanel members are expected to disclose any actual, potential, or perceived conflicts of interest that may influence or appear to influence their independent judgment.\r\n\r\nWhere appropriate, GIHQS may request formal declarations of interest and may restrict participation in specific reviews to protect the integrity of accreditation decisions.";

            $ar->expression_title = 'Expressions of Interest';
            $ar->expression_description = '<p>Experienced professionals with expertise in healthcare accreditation, standards development, quality improvement, clinical governance, regulatory oversight, or program evaluation who are interested in contributing to the Accreditation Review Panel may contact GIHQS.<br>&nbsp;</p><p>To express interest, please send a current CV and a short professional biography to &nbsp;<a href="mailto:info@gihqs.com">info@gihqs.com</a><br>&nbsp;</p><p>Additional appointments will be announced as the panel is finalized.</p>';

            $ar->save();

            $features = [
                [
                    'id'                      => 1,
                    'accreditation_review_id' => 1,
                    'description'            => 'Review accreditation applications and supporting documentation',
                ],
                [
                    'id'                      => 2,
                    'accreditation_review_id' => 1,
                    'description'            => 'Evaluate compliance with GIHQS accreditation standards',
                ],
                [
                    'id'                      => 3,
                    'accreditation_review_id' => 1,
                    'description'            => 'Assess program governance, curriculum, and quality systems',
                ],
                [
                    'id'                      => 4,
                    'accreditation_review_id' => 1,
                    'description'            => 'Review evidence of learning outcomes and program impact',
                ],
                [
                    'id'                      => 5,
                    'accreditation_review_id' => 1,
                    'description'            => 'Provide technical review recommendations',
                ],
                [
                    'id'                      => 6,
                    'accreditation_review_id' => 1,
                    'description'            => 'Support accreditation decision-making processes',
                ],
            ];

            foreach ($features as $feature) {
                $featureModel = new AccreditationReviewFeature();
                $featureModel->id = $feature['id'];
                $featureModel->accreditation_review_id = $feature['accreditation_review_id'];
                $featureModel->description = $feature['description'];
                $featureModel->save();
            }
        }
    }
}
