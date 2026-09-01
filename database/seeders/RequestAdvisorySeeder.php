<?php

namespace Database\Seeders;

use App\Models\RequestAdvisory;
use Illuminate\Database\Seeder;

class RequestAdvisorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RequestAdvisory::firstOrCreate(
            [],
            [
                'id' => 1,
                'title1' => 'Request',
                'title2' => 'Advisory Services',
                'tagline' => 'GIHQS ADVISORY SERVICES',
                'description' => 'Please complete the form below to help us understand your organization\'s needs. A member of the GIHQS team will review your request and respond with the most appropriate next steps.',
            ]
        );
    }
}
