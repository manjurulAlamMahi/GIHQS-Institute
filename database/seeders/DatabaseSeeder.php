<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MembershipPackageSeeder::class,
            AdminUserSeeder::class,
            AdminSettingSeeder::class,
            WebsiteSettingSeeder::class,
            SocialSettingSeeder::class,
            PathwaySeeder::class,
            HomeServicesPathwaysSeeder::class,
            HomeFlagshipCertificationSeeder::class,
            CatalogueSeeder::class,
            AboutInstituteSeeder::class,
            AboutContactSeeder::class,
            VisionMissionValueSeeder::class,
            StrategicAdvisorySeeder::class,
            AccreditationReviewSeeder::class,
            PoliciesGovernanceSeeder::class,
            AdvisoryServicesSeeder::class,
            RequestAdvisorySeeder::class,
            AccreditationHeaderSeeder::class,
            AccreditationDetailSeeder::class,
            AccreditationFeeSeeder::class,
            AccreditationApplyHeroSeeder::class,
            OtherPagesSeeder::class,
        ]);
    }
}
