<?php

namespace Database\Seeders;

use App\Models\AccreditationApplyHero;
use App\Models\AccreditationEligibilitySnapshot;
use Illuminate\Database\Seeder;

class AccreditationApplyHeroSeeder extends Seeder
{
    public function run(): void
    {
        AccreditationApplyHero::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1'      => 'GIHQS Accreditation',
                'title2'      => 'Application',
                'tagline'     => 'Accreditation',
                'description' => 'Complete the information below to initiate your accreditation application. This form is suitable for independent trainers, training companies, academic institutions, healthcare organizations, professional associations, and certification bodies seeking accreditation of an eligible education or training program.',
                'note'        => 'Please provide accurate applicant and program information. Supporting materials such as a curriculum, brochure, program outline, or governance document may help GIHQS assess eligibility more efficiently.',
            ]
        );

        $snapshot = AccreditationEligibilitySnapshot::firstOrCreate(
            [],
            [
                'id' => 1,
                'title'       => 'Eligibility Snapshot',
                'description' => 'GIHQS accreditation may be requested by a range of eligible applicants seeking recognition of a specific education or training program in healthcare quality, patient safety, or high-reliability healthcare.',
            ]
        );

        if ($snapshot->features()->count() === 0) {
            $snapshot->features()->createMany([
                [
                    'keypoints' => 'Who may apply',
                    'details' => 'Independent trainers, training providers, institutions, and eligible organizations.',
                ],
                [
                    'keypoints' => 'Program-focused review',
                    'details' => 'Accreditation review is based on the program submitted, not only the applicant entity.',
                ],
                [
                    'keypoints' => 'Helpful documents',
                    'details' => 'Program overviews, curriculum outlines, policies, and governance materials may be attached.',
                ],
            ]);
        }
    }
}
