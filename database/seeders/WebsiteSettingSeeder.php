<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WebsiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WebsiteSetting::firstOrCreate(
            ['id' => 1], // ensures only one default record
            [
                'logo'            => null,
                'favicon'         => null,
                'company_name'    => 'Company Name',
                'tag_line'        => 'Tag Line Here',
                'phone_number'    => '+39 366 2270888',
                'whatsapp_number' => '+39 366 2270888',
                'email'           => 'info@email.net',
                'support_email'   => 'support@email.net',
                'company_address' => '1234 Street Name, City, Country',
                'copyright_text'  => '© ' . date('Y') . ' System Panel . All rights reserved.',
            ]
        );
    }
}
