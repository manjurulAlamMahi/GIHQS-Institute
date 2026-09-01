<?php

namespace Database\Seeders;

use App\Models\AccreditationFee;
use Illuminate\Database\Seeder;

class AccreditationFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fee = AccreditationFee::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1' => 'GIHQS',
                'title2' => 'Accreditation Fees',
                'description' => 'GIHQS accreditation fees support a rigorous, independent review process designed to ensure transparency, integrity, and alignment with recognized healthcare quality and patient safety standards.',
            ]
        );

        if ($fee->plans()->count() === 0) {
            // Plan 1
            $plan1 = $fee->plans()->create([
                'title' => 'Pre-Application Registration',
                'price' => '150',
                'description' => 'Required to initiate the accreditation process and obtain a GIHQS Application ID.',
            ]);

            $plan1->features()->createMany([
                ['feature' => 'Application ID generation'],
                ['feature' => 'Access to submission package'],
                ['feature' => 'Eligibility verification'],
            ]);

            // Plan 2
            $plan2 = $fee->plans()->create([
                'title' => 'Accreditation Review Fee',
                'price' => '1250',
                'description' => 'Covers the formal evaluation conducted by the independent GIHQS expert review panel.',
            ]);

            $plan2->features()->createMany([
                ['feature' => 'Standards-based review'],
                ['feature' => 'Expert evaluation panel'],
                ['feature' => 'Accreditation decision report'],
            ]);

            // Plan 3
            $plan3 = $fee->plans()->create([
                'title' => 'Renewal / Re-Accreditation',
                'price' => '750',
                'description' => 'Applies to accredited programs undergoing periodic review to maintain accreditation status.',
            ]);

            $plan3->features()->createMany([
                ['feature' => 'Evidence update review'],
                ['feature' => 'Outcome assessment'],
                ['feature' => 'Renewed accreditation decision'],
            ]);
        }
    }
}
