<?php

namespace Database\Seeders;

use App\Models\OtherPage;
use Illuminate\Database\Seeder;

class OtherPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
            ],
            [
                'slug' => 'terms-of-use',
                'title' => 'Terms of Use',
            ],
            [
                'slug' => 'terms-purchase',
                'title' => 'Terms & Conditions of Purchase',
            ],
            [
                'slug' => 'refund-policy',
                'title' => 'Refund Policy',
            ],
            [
                'slug' => 'disclaimer',
                'title' => 'Disclaimer',
            ],
        ];

        foreach ($pages as $page) {
            OtherPage::firstOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'content_file' => null,
                    'injected_status' => 1,
                ]
            );
        }
    }
}
