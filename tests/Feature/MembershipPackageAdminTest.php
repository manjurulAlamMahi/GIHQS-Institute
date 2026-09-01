<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MembershipPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipPackageAdminTest extends TestCase
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

    public function test_it_requires_admin_or_manager_role_to_view_membership_packages_page()
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user, 'web')
            ->get(route('admin.membership-packages.index'))
            ->assertStatus(403);

        $this->actingAs($this->adminUser, 'web')
            ->get(route('admin.membership-packages.index'))
            ->assertStatus(200);
    }

    public function test_it_can_fetch_membership_packages_list_ajax_request()
    {
        MembershipPackage::create([
            'name' => 'Premium Plus',
            'title' => 'Premium Plus Membership',
            'price' => 199.00,
            'discount_percentage' => 20.00,
            'exam_attempt_limit' => 5,
            'status' => 1,
        ]);

        $response = $this->actingAs($this->adminUser, 'web')
            ->getJson(route('admin.membership-packages.index'), [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', '<strong>Premium Plus</strong>');
    }

    public function test_it_can_validate_and_store_a_new_membership_package()
    {
        // Test successful creation
        $responseSuccess = $this->actingAs($this->adminUser, 'web')
            ->post(route('admin.membership-packages.store'), [
                'name' => 'Standard Plus',
                'title' => 'Standard Plus Title',
                'price' => 49.00,
                'discount_percentage' => 5.00,
                'exam_attempt_limit' => 2,
                'status' => 1,
                'features' => [
                    ['description' => 'Feature 1', 'badge' => 'New', 'note' => 'Some note']
                ]
            ]);

        $responseSuccess->assertRedirect(route('admin.membership-packages.index'));

        $this->assertDatabaseHas('membership_packages', [
            'name' => 'Standard Plus',
        ]);

        $this->assertDatabaseHas('membership_package_features', [
            'description' => 'Feature 1',
            'badge' => 'New',
        ]);
    }

    public function test_it_can_update_an_existing_membership_package()
    {
        $package = MembershipPackage::create([
            'name' => 'Standard Plus',
            'title' => 'Standard Plus Title',
            'price' => 49.00,
            'discount_percentage' => 5.00,
            'exam_attempt_limit' => 2,
            'status' => 1,
        ]);

        $response = $this->actingAs($this->adminUser, 'web')
            ->put(route('admin.membership-packages.update', $package->id), [
                'name' => 'Standard Super Plus',
                'title' => 'Standard Plus Title',
                'price' => 49.00,
                'discount_percentage' => 5.00,
                'exam_attempt_limit' => 2,
                'status' => 1,
            ]);

        $response->assertRedirect(route('admin.membership-packages.index'));

        $this->assertDatabaseHas('membership_packages', [
            'id' => $package->id,
            'name' => 'Standard Super Plus',
        ]);
    }

    public function test_it_renders_clone_and_edit_views_with_package_context()
    {
        $package = MembershipPackage::create([
            'name' => 'Cloneable Pack',
            'title' => 'Cloneable Pack Title',
            'price' => 10.00,
            'discount_percentage' => 0.00,
            'exam_attempt_limit' => 1,
            'status' => 1,
        ]);

        $this->actingAs($this->adminUser, 'web')
            ->get(route('admin.membership-packages.edit', $package->id))
            ->assertStatus(200)
            ->assertSee('Cloneable Pack');

        $this->actingAs($this->adminUser, 'web')
            ->get(route('admin.membership-packages.clone', $package->id))
            ->assertStatus(200)
            ->assertSee('Cloneable Pack');
    }

    public function test_it_does_not_render_delete_button_for_any_package()
    {
        $standard = MembershipPackage::create([
            'id' => 1,
            'name' => 'Standard',
            'title' => 'Standard Member',
            'price' => 0.00,
            'discount_percentage' => 0.00,
            'exam_attempt_limit' => 1,
            'status' => 1,
        ]);

        $custom = MembershipPackage::create([
            'id' => 3,
            'name' => 'Gold VIP',
            'title' => 'Gold VIP Member',
            'price' => 299.00,
            'discount_percentage' => 25.00,
            'exam_attempt_limit' => 10,
            'status' => 1,
        ]);

        $response = $this->actingAs($this->adminUser, 'web')
            ->getJson(route('admin.membership-packages.index'), [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        $data = $response->json('data');

        foreach ($data as $row) {
            $this->assertStringNotContainsString('delete-button', $row['action']);
            $this->assertStringNotContainsString('fa-trash-can', $row['action']);
        }
    }

    public function test_it_prevents_deletion_of_protected_standard_and_premium_packages()
    {
        $standard = MembershipPackage::create([
            'id' => 1,
            'name' => 'Standard',
            'title' => 'Standard Member',
            'price' => 0.00,
            'discount_percentage' => 0.00,
            'exam_attempt_limit' => 1,
            'status' => 1,
        ]);

        $premium = MembershipPackage::create([
            'id' => 2,
            'name' => 'Premium',
            'title' => 'Premium Member',
            'price' => 95.00,
            'discount_percentage' => 15.00,
            'exam_attempt_limit' => 3,
            'status' => 1,
        ]);

        // Attempt deleting Standard
        $this->actingAs($this->adminUser, 'web')
            ->delete(route('admin.membership-packages.destroy', $standard->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('membership_packages', ['id' => $standard->id]);

        // Attempt deleting Premium
        $this->actingAs($this->adminUser, 'web')
            ->delete(route('admin.membership-packages.destroy', $premium->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('membership_packages', ['id' => $premium->id]);
    }
}

