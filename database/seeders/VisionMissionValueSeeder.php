<?php

namespace Database\Seeders;

use App\Models\VisionMissionValue;
use Illuminate\Database\Seeder;

class VisionMissionValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (VisionMissionValue::count() === 0) {
            $vmv = new VisionMissionValue();
            $vmv->id = 1;
            $vmv->tagline = 'GIHQS FOUNDATION';
            $vmv->title1 = 'Mission, Vision &';
            $vmv->title2 = 'Core Values';
            $vmv->short_description = 'The Global Institute for Healthcare Quality & Safety (GIHQS) advances safer healthcare systems by supporting professionals, organizations, and leaders committed to healthcare quality, patient safety, high-reliability healthcare, and the responsible use of Artificial Intelligence (Al) in healthcare.';

            $vmv->mission_tagline = 'MISSION';
            $vmv->mission_title = 'Advancing professionals. Strengthening systems.';
            $vmv->mission_short_description = 'To advance healthcare quality and patient safety by developing professionals through certification, education, and accreditation of programs that strengthen high-reliability healthcare systems and the responsible use of Artificial Intelligence (AI) in healthcare.';

            $vmv->vision_tagline = 'VISION';
            $vmv->vision_title = 'Healthcare systems that are consistently safe, high- reliability, and trusted by the patients and communities they serve.';
            $vmv->vision_short_description = 'This vision reflects the future GIHQS seeks to help advance: healthcare systems that do not depend on luck, silence, or heroic recovery, but are designed for safety, trust, and reliability.';

            $vmv->value_tagline = 'CORE VALUES';
            $vmv->value_title = 'The GIHQS';
            $vmv->value_title2 = 'Values Framework';
            $vmv->value_short_description = 'GIHQS expresses its values through five foundational commitments that reinforce the institute name itself. Together, these values support ethical leadership, safer care, continuous improvement, and trusted innovation.';

            $vmv->global_perspective_tagline = 'G';
            $vmv->global_perspective_title = 'Global Perspective';
            $vmv->global_perspective_short_description = "Promoting shared learning, collaboration, and exchange across healthcare systems, disciplines, and professional communities to strengthen healthcare quality and patient safety.";

            $vmv->integrity_tagline = 'I';
            $vmv->integrity_title = 'Integrity';
            $vmv->integrity_short_description = 'Upholding ethical, transparent, and fair practices in certification, education, accreditation, leadership, and professional conduct.';

            $vmv->human_centered_tagline = 'H';
            $vmv->human_centered_title = 'Human- Centered Care';
            $vmv->human_centered_short_description = 'Ensuring that patients, families, and healthcare professionals remain at the heart of safer systems, trusted care, and meaningful improvement.';

            $vmv->quality_excellence_tagline = 'Q';
            $vmv->quality_excellence_title = 'Quality Excellence';
            $vmv->quality_excellence_short_description = 'Advancing evidence- based improvement, professional excellence, and high-reliability healthcare practices that strengthen outcomes and reduce preventable harm.';

            $vmv->safety_leadership_tagline = 'S';
            $vmv->safety_leadership_title = 'Safety Leadership';
            $vmv->safety_leadership_short_description = 'Championing cultures of safety, accountability, learning, and responsible innovation that help organizations prevent harm and build more reliable healthcare systems.';

            $vmv->save();
        }
    }
}
