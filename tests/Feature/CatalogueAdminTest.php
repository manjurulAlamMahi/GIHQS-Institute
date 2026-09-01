<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Catalogue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CatalogueAdminTest extends TestCase
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

    public function test_it_preserves_credit_and_validity_fields_for_certification_catalogue()
    {
        $response = $this->actingAs($this->adminUser, 'web')
            ->post(route('admin.catalogues.store'), [
                'title' => 'Test Certification',
                'short_title' => 'TC',
                'short_description' => 'Description',
                'price_regular' => 100.00,
                'price_final' => 100.00,
                'catalogue_type' => 'paid',
                'service_type' => 'Certification',
                'status' => 1,
                'ce_credit_total_required' => 15.50,
                'validity_years' => 3,
                'credential_statement' => 'This is a credential statement.',
            ]);

        $response->assertRedirect(route('admin.catalogues.index'));

        $this->assertDatabaseHas('catalogues', [
            'title' => 'Test Certification',
            'service_type' => 'Certification',
            'ce_credit_total_required' => 15.50,
            'validity_years' => 3,
            'credential_statement' => 'This is a credential statement.',
        ]);
    }

    public function test_it_resets_credit_and_preserves_validity_fields_for_non_certification_catalogue()
    {
        $response = $this->actingAs($this->adminUser, 'web')
            ->post(route('admin.catalogues.store'), [
                'title' => 'Test Course',
                'short_title' => 'TCourse',
                'short_description' => 'Description',
                'price_regular' => 50.00,
                'price_final' => 50.00,
                'catalogue_type' => 'paid',
                'service_type' => 'Course',
                'status' => 1,
                'ce_credit_total_required' => 15.50, // Should be reset to 0.00
                'validity_years' => 3,               // Should be preserved
            ]);

        $response->assertRedirect(route('admin.catalogues.index'));

        $this->assertDatabaseHas('catalogues', [
            'title' => 'Test Course',
            'service_type' => 'Course',
            'ce_credit_total_required' => 0.00,
            'validity_years' => 3,
        ]);
    }

    public function test_it_saves_and_updates_certification_seal_image()
    {
        $file = UploadedFile::fake()->image('seal.png');

        $response = $this->actingAs($this->adminUser, 'web')
            ->post(route('admin.catalogues.store'), [
                'title' => 'Seal Test Certification',
                'short_title' => 'STC',
                'short_description' => 'Description',
                'price_regular' => 100.00,
                'price_final' => 100.00,
                'catalogue_type' => 'paid',
                'service_type' => 'Certification',
                'status' => 1,
                'ce_credit_total_required' => 15.50,
                'validity_years' => 3,
                'certification_seal' => $file,
            ]);

        $response->assertRedirect(route('admin.catalogues.index'));

        $catalogue = Catalogue::where('title', 'Seal Test Certification')->first();
        $this->assertNotNull($catalogue->certification_seal);
        $firstSealPath = $catalogue->certification_seal;
        $this->assertFileExists(public_path($firstSealPath));

        // Update the seal image
        $newFile = UploadedFile::fake()->image('new_seal.png');
        $response = $this->actingAs($this->adminUser, 'web')
            ->put(route('admin.catalogues.update', $catalogue->id), [
                'title' => 'Seal Test Certification Updated',
                'price_regular' => 100.00,
                'price_final' => 100.00,
                'catalogue_type' => 'paid',
                'service_type' => 'Certification',
                'status' => 1,
                'certification_seal' => $newFile,
            ]);

        $response->assertRedirect(route('admin.catalogues.index'));
        $catalogue->refresh();
        $this->assertNotNull($catalogue->certification_seal);
        $this->assertNotEquals($firstSealPath, $catalogue->certification_seal);
        $this->assertFileExists(public_path($catalogue->certification_seal));

        // Clean up the first uploaded file and the updated file
        $secondSealPath = $catalogue->certification_seal;

        // Remove the seal image
        $response = $this->actingAs($this->adminUser, 'web')
            ->put(route('admin.catalogues.update', $catalogue->id), [
                'title' => 'Seal Test Certification Updated',
                'price_regular' => 100.00,
                'price_final' => 100.00,
                'catalogue_type' => 'paid',
                'service_type' => 'Certification',
                'status' => 1,
                'remove_certification_seal' => 1,
            ]);

        $response->assertRedirect(route('admin.catalogues.index'));
        $catalogue->refresh();
        $this->assertNull($catalogue->certification_seal);

        // Assert files are deleted from the disk
        $this->assertFileDoesNotExist(public_path($firstSealPath));
        $this->assertFileDoesNotExist(public_path($secondSealPath));
    }

    public function test_webinar_or_workshop_requires_date_and_times()
    {
        // Try creating Webinar without date/time - should fail validation
        $response = $this->actingAs($this->adminUser, 'web')
            ->from(route('admin.catalogues.create'))
            ->post(route('admin.catalogues.store'), [
                'title' => 'Test Webinar',
                'short_title' => 'TW',
                'short_description' => 'Description',
                'price_regular' => 100.00,
                'price_final' => 100.00,
                'catalogue_type' => 'paid',
                'service_type' => 'Webinar',
                'status' => 1,
            ]);

        $response->assertRedirect(route('admin.catalogues.create'));
        $response->assertSessionHasErrors(['fixed_date', 'start_time', 'end_time']);

        // Try creating Webinar with valid date/time - should succeed
        $response = $this->actingAs($this->adminUser, 'web')
            ->post(route('admin.catalogues.store'), [
                'title' => 'Test Webinar Success',
                'short_title' => 'TW',
                'short_description' => 'Description',
                'price_regular' => 100.00,
                'price_final' => 100.00,
                'catalogue_type' => 'paid',
                'service_type' => 'Webinar',
                'status' => 1,
                'fixed_date' => '2026-09-10',
                'start_time' => '14:00',
                'end_time' => '16:00',
            ]);

        $response->assertRedirect(route('admin.catalogues.index'));
        $catalogue = Catalogue::where('title', 'Test Webinar Success')->first();
        $this->assertNotNull($catalogue);
        $this->assertEquals('Webinar', $catalogue->service_type);
        $this->assertEquals('2026-09-10', $catalogue->fixed_date);
        $this->assertEquals('14:00', date('H:i', strtotime($catalogue->start_time)));
        $this->assertEquals('16:00', date('H:i', strtotime($catalogue->end_time)));
    }

    public function test_non_webinar_workshop_resets_date_and_times()
    {
        $response = $this->actingAs($this->adminUser, 'web')
            ->post(route('admin.catalogues.store'), [
                'title' => 'Test Course Resets',
                'short_title' => 'TCR',
                'short_description' => 'Description',
                'price_regular' => 100.00,
                'price_final' => 100.00,
                'catalogue_type' => 'paid',
                'service_type' => 'Course',
                'status' => 1,
                'fixed_date' => '2026-09-10',
                'start_time' => '14:00',
                'end_time' => '16:00',
            ]);

        $response->assertRedirect(route('admin.catalogues.index'));
        $this->assertDatabaseHas('catalogues', [
            'title' => 'Test Course Resets',
            'service_type' => 'Course',
            'fixed_date' => null,
            'start_time' => null,
            'end_time' => null,
        ]);
    }
}
