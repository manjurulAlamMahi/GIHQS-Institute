<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CertificateSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CertificateSettingTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_it_displays_certificate_settings_edit_page()
    {
        $response = $this->actingAs($this->adminUser, 'web')
            ->get(route('admin.certificate-settings.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('backend.layouts.settings.certificate_settings');
        $response->assertViewHas('setting');
    }

    public function test_it_updates_certificate_settings_files()
    {
        $template = UploadedFile::fake()->create('template.html', 500, 'text/html');
        $chairmanSig = UploadedFile::fake()->image('chairman.png');
        $directorSig = UploadedFile::fake()->image('director.png');

        $response = $this->actingAs($this->adminUser, 'web')
            ->post(route('admin.certificate-settings.update'), [
                'certificate_template'          => $template,
                'chairman_name'                  => 'John Doe',
                'chairman_signature'             => $chairmanSig,
                'executive_director_name'        => 'Jane Smith',
                'executive_director_signature'   => $directorSig,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('t-success');

        $setting = CertificateSetting::first();
        $this->assertNotNull($setting);
        $this->assertEquals('John Doe', $setting->chairman_name);
        $this->assertEquals('Jane Smith', $setting->executive_director_name);
        $this->assertNotNull($setting->certificate_template);
        $this->assertNotNull($setting->chairman_signature);
        $this->assertNotNull($setting->executive_director_signature);

        $this->assertFileExists(public_path($setting->certificate_template));
        $this->assertFileExists(public_path($setting->chairman_signature));
        $this->assertFileExists(public_path($setting->executive_director_signature));

        $oldTemplate = $setting->certificate_template;
        $oldChairman = $setting->chairman_signature;
        $oldDirector = $setting->executive_director_signature;

        // Test removal
        $response = $this->actingAs($this->adminUser, 'web')
            ->post(route('admin.certificate-settings.update'), [
                'chairman_name'                        => null,
                'executive_director_name'              => null,
                'remove_certificate_template'          => 1,
                'remove_chairman_signature'             => 1,
                'remove_executive_director_signature'   => 1,
            ]);

        $response->assertRedirect();
        $setting->refresh();

        $this->assertNull($setting->chairman_name);
        $this->assertNull($setting->executive_director_name);
        $this->assertNull($setting->certificate_template);
        $this->assertNull($setting->chairman_signature);
        $this->assertNull($setting->executive_director_signature);

        $this->assertFileDoesNotExist(public_path($oldTemplate));
        $this->assertFileDoesNotExist(public_path($oldChairman));
        $this->assertFileDoesNotExist(public_path($oldDirector));
    }
}
