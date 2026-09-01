<?php

namespace Database\Seeders;

use App\Models\Catalogue;
use App\Models\CatalogueFeature;
use Illuminate\Database\Seeder;

class CatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatalogueFeature::query()->delete();
        Catalogue::query()->delete();

        $items = [
            [
                'title' => 'AIHQSP — AI Healthcare Quality & Safety Professional',
                'short_title' => 'AIHQSP',
                'short_description' => 'Templates and tools that support more structured root cause analysis action planning and follow-through.',
                'price_regular' => 45.00,
                'service_type' => 'Certification',
                'is_feature' => true,
                'is_trending' => false,
                'is_popular' => false,
                'features' => ['Downloadable resources', 'Practical templates', 'Implementation support'],
            ],
            [
                'title' => 'Root Cause Analysis (RCA) - 1',
                'short_title' => 'RCA Course',
                'short_description' => 'Root Cause Analysis (RCA) teaches healthcare professionals how to systematically investigate adverse...',
                'price_regular' => 45.00,
                'service_type' => 'Course',
                'is_feature' => false,
                'is_trending' => false,
                'is_popular' => false,
                'features' => ['Downloadable resources', 'Practical templates', 'Implementation support'],
            ],
            [
                'title' => 'Future Webinar Example - 1',
                'short_title' => 'Future Webinar',
                'short_description' => 'This shows how the design can support future product types with their own visual color and badge.',
                'price_regular' => 45.00,
                'service_type' => 'Webinar',
                'is_feature' => false,
                'is_trending' => false,
                'is_popular' => false,
                'features' => ['Expendable type system', 'Separate badge color', 'Future-ready structure'],
            ],
            [
                'title' => 'Patient Experience Excellence - 1',
                'short_title' => 'Patient Experience',
                'short_description' => 'Focused module supporting stronger patient-centered care and experience improvement strategies.',
                'price_regular' => 45.00,
                'service_type' => 'Module',
                'is_feature' => false,
                'is_trending' => true,
                'is_popular' => false,
                'features' => ['Downloadable resources', 'Practical templates', 'Implementation support'],
            ],
            [
                'title' => 'Root Cause Analysis (RCA) - 2',
                'short_title' => 'RCA Toolkit',
                'short_description' => 'Root Cause Analysis (RCA) teaches healthcare professionals how to systematically investigate adverse...',
                'price_regular' => 45.00,
                'service_type' => 'Toolkit',
                'is_feature' => false,
                'is_trending' => false,
                'is_popular' => false,
                'features' => ['Downloadable resources', 'Practical templates', 'Implementation support'],
            ],
            [
                'title' => 'Patient Experience Excellence - 2',
                'short_title' => 'Patient Experience',
                'short_description' => 'Focused module supporting stronger patient-centered care and experience improvement strategies.',
                'price_regular' => 45.00,
                'service_type' => 'Module',
                'is_feature' => false,
                'is_trending' => false,
                'is_popular' => true,
                'features' => ['Downloadable resources', 'Practical templates', 'Implementation support'],
            ],
            [
                'title' => 'Root Cause Analysis (RCA) - 3',
                'short_title' => 'RCA Toolkit',
                'short_description' => 'Root Cause Analysis (RCA) teaches healthcare professionals how to systematically investigate adverse...',
                'price_regular' => 45.00,
                'service_type' => 'Toolkit',
                'is_feature' => false,
                'is_trending' => false,
                'is_popular' => false,
                'features' => ['Downloadable resources', 'Practical templates', 'Implementation support'],
            ],
            [
                'title' => 'Future Webinar Example - 2',
                'short_title' => 'Future Webinar',
                'short_description' => 'This shows how the design can support future product types with their own visual color and badge.',
                'price_regular' => 45.00,
                'service_type' => 'Webinar',
                'is_feature' => false,
                'is_trending' => false,
                'is_popular' => false,
                'features' => ['Expendable type system', 'Separate badge color', 'Future-ready structure'],
            ],
            [
                'title' => 'RCA Action Planning Toolkit',
                'short_title' => 'RCA Action Planning',
                'short_description' => 'Templates and tools that support more structured root cause analysis action planning and follow-through.',
                'price_regular' => 45.00,
                'service_type' => 'Course',
                'is_feature' => true,
                'is_trending' => false,
                'is_popular' => false,
                'features' => ['Downloadable resources', 'Practical templates', 'Implementation support'],
            ],
        ];

        foreach ($items as $itemData) {
            $features = $itemData['features'];
            unset($itemData['features']);

            $itemData['price_final'] = $itemData['price_final'] ?? $itemData['price_regular'];
            $itemData['catalogue_type'] = $itemData['catalogue_type'] ?? 'paid';
            $catalogue = Catalogue::create($itemData);

            foreach ($features as $featureDesc) {
                $catalogue->features()->create([
                    'description' => $featureDesc,
                ]);
            }
        }
    }
}
