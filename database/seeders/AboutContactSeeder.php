<?php

namespace Database\Seeders;

use App\Models\AboutContact;
use Illuminate\Database\Seeder;

class AboutContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (AboutContact::count() === 0) {
            $aboutContact = new AboutContact();
            $aboutContact->id = 1;
            $aboutContact->title = 'Contact Information';
            $aboutContact->phone = '+1 (347) 763-9554';
            $aboutContact->email = 'info@gihqs.com';
            $aboutContact->address = "1209 Mountain Road PL NE, STE R\r\nAlbuquerque, NM 87110\r\nUnited States";
            $aboutContact->working_hours = '<p>Monday – Friday<br>9:00 AM – 5:00 PM (EST)</p>';
            $aboutContact->mission = 'To advance healthcare quality and patient safety by developing professionals through certification, education, and accreditation of programs that strengthen high-reliability healthcare systems and the responsible use of Artificial Intelligence (AI) in healthcare.';
            $aboutContact->content_file = null;
            $aboutContact->injected_status = 0;
            $aboutContact->save();
        }
    }
}
