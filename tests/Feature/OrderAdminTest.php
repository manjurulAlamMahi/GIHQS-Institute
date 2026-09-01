<?php

use App\Models\User;
use App\Models\Purchase;
use App\Models\MembershipPackage;
use App\Models\Catalogue;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $this->member = User::factory()->create([
        'role' => 'user',
        'email_verified_at' => now(),
    ]);

    $this->package = MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Package',
        'price' => 99.00,
        'status' => 1,
    ]);

    $this->catalogue = Catalogue::create([
        'title' => 'Test Course',
        'short_title' => 'Course',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    // Create a pending membership order
    $this->membershipOrder = Purchase::create([
        'user_id' => $this->member->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $this->package->id,
        'amount' => 99.00,
        'payment_status' => 'pending',
        'order_status' => 'pending',
    ]);

    // Create a pending catalogue order
    $this->catalogueOrder = Purchase::create([
        'user_id' => $this->member->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $this->catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'pending',
        'order_status' => 'pending',
    ]);
});

it('requires admin or manager role to view orders page', function () {
    $guest = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($guest)->get(route('admin.orders.index'));
    $response->assertStatus(403);
});

it('can render orders index page for admin', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));
    $response->assertStatus(200);
});

it('can fetch orders list ajax request', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.orders.index'), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['data']);
    
    $data = $response->json('data');
    expect(count($data))->toBeGreaterThanOrEqual(2);
});

it('can update order status and verify validation', function () {
    // Attempt status update with invalid statuses
    $response = $this->actingAs($this->admin)->patch(route('admin.orders.update-status', $this->catalogueOrder->id), [
        'payment_status' => 'invalid_payment_status',
        'order_status' => 'invalid_order_status',
    ]);
    $response->assertStatus(302); // fails validation, redirects back

    // Update status successfully
    $response = $this->actingAs($this->admin)->patch(route('admin.orders.update-status', $this->catalogueOrder->id), [
        'payment_status' => 'paid',
        'order_status' => 'completed',
    ]);

    $response->assertStatus(200);
    $this->catalogueOrder->refresh();
    expect($this->catalogueOrder->payment_status)->toBe('paid');
    expect($this->catalogueOrder->order_status)->toBe('completed');
});

it('automatically promotes member role on paid membership order', function () {
    expect($this->member->role)->toBe('user');

    // Update status of membership order to paid / active
    $response = $this->actingAs($this->admin)->patch(route('admin.orders.update-status', $this->membershipOrder->id), [
        'payment_status' => 'paid',
        'order_status' => 'active',
    ]);

    $response->assertStatus(200);
    $this->member->refresh();
    
    // Role should remain 'user'
    expect($this->member->role)->toBe('user');

    // Active membership package should resolve to Premium
    expect($this->member->active_membership)->not->toBeNull();
    expect($this->member->active_membership->id)->toBe($this->package->id);
});

it('automatically revokes membership access if order payment status is changed from paid', function () {
    // Manually set as paid first (pre-condition)
    $this->membershipOrder->update([
        'payment_status' => 'paid',
        'order_status' => 'active',
    ]);

    // Demote standard order back to unpaid / cancelled
    $response = $this->actingAs($this->admin)->patch(route('admin.orders.update-status', $this->membershipOrder->id), [
        'payment_status' => 'cancelled',
        'order_status' => 'cancelled',
    ]);

    $response->assertStatus(200);
    $this->member->refresh();
    
    // Active membership package should now be null
    expect($this->member->active_membership)->toBeNull();
    expect($this->member->role)->toBe('user');
});

it('calculates expires_at based on membership package validity_days when activated', function () {
    // Set validity days for package
    $this->package->update(['validity_days' => 30]);

    // Activate membership order
    $response = $this->actingAs($this->admin)->patch(route('admin.orders.update-status', $this->membershipOrder->id), [
        'payment_status' => 'paid',
        'order_status' => 'active',
    ]);

    $response->assertStatus(200);
    $this->membershipOrder->refresh();

    expect($this->membershipOrder->expires_at)->not->toBeNull();
    $expectedExpiry = now()->addDays(30);
    expect($this->membershipOrder->expires_at->format('Y-m-d'))->toBe($expectedExpiry->format('Y-m-d'));
});

it('completes order when check expired memberships command runs', function () {
    // Set up active subscription that has expired
    $this->package->update(['validity_days' => 30]);
    $this->membershipOrder->update([
        'user_id'        => $this->member->id,
        'purchase_type'  => 'membership',
        'membership_package_id' => $this->package->id,
        'amount'         => 99.00,
        'payment_status' => 'paid',
        'order_status'   => 'active',
        'expires_at'     => now()->subDays(1), // expired yesterday
    ]);

    // Run the artisan command
    $this->artisan('app:check-expired-memberships')
        ->expectsOutput('Checking for expired memberships...')
        ->expectsOutput('Successfully processed 1 expired memberships.')
        ->assertExitCode(0);

    $this->member->refresh();
    $this->membershipOrder->refresh();

    expect($this->member->active_membership)->toBeNull();
    expect($this->membershipOrder->order_status)->toBe('completed');
});

it('does not throw an error when fetching ajax list with a pending refund request', function () {
    // Make catalogue order paid so it can have a refund request
    $this->catalogueOrder->update([
        'payment_status' => 'paid',
        'order_status' => 'completed',
        'refund_request_status' => 'pending',
        'refund_requested_at' => now(),
        'refund_request_reason' => 'Duplicate purchase',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.orders.index'), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['data']);
    
    $data = $response->json('data');
    expect(count($data))->toBeGreaterThanOrEqual(2);
    
    // Check that one of the actions includes the refund details
    $hasRefundDetails = false;
    foreach ($data as $row) {
        if (str_contains($row['action'], 'Duplicate purchase')) {
            $hasRefundDetails = true;
        }
    }
    expect($hasRefundDetails)->toBeTrue();
});

it('requires admin or manager role to view refund requests page', function () {
    $guest = User::factory()->create(['role' => 'user']);
    $this->actingAs($guest)
        ->get(route('admin.orders.refund-requests'))
        ->assertStatus(403);
});

it('can render refund requests page for admin', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.orders.refund-requests'))
        ->assertStatus(200)
        ->assertSee('Refund Requests List');
});

it('can fetch refund requests list ajax request and filters only refund requests', function () {
    // 1. Create a paid catalogue order with NO refund request
    $normalOrder = Purchase::create([
        'user_id' => $this->member->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $this->catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
        'order_status' => 'completed',
        'order_id' => 'ORD-CAT-NORMAL-123',
    ]);

    // 2. Create a paid catalogue order WITH refund request
    $refundOrder = Purchase::create([
        'user_id' => $this->member->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $this->catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
        'order_status' => 'completed',
        'refund_request_status' => 'pending',
        'refund_requested_at' => now(),
        'refund_request_reason' => 'Duplicate purchase',
        'order_id' => 'ORD-CAT-REFUND-123',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.orders.refund-requests'), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['data']);
    
    $data = $response->json('data');
    
    // Check that only the refund request is returned, not the normal order
    $hasRefundOrder = false;
    $hasNormalOrder = false;
    
    foreach ($data as $row) {
        if (str_contains($row['order_id'], $refundOrder->order_id)) {
            $hasRefundOrder = true;
        }
        if (str_contains($row['order_id'], $normalOrder->order_id)) {
            $hasNormalOrder = true;
        }
    }
    
    expect($hasRefundOrder)->toBeTrue();
    expect($hasNormalOrder)->toBeFalse();
});

it('requires admin or manager role to view admin dashboard', function () {
    $guest = User::factory()->create(['role' => 'user']);
    $this->actingAs($guest)
        ->get(route('admin.dashboard'))
        ->assertStatus(403);
});

it('can render admin dashboard with all metrics and data variables', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertStatus(200)
        ->assertViewHas([
            'totalUsers',
            'totalMessages',
            'totalCatalogues',
            'totalOrders',
            'netRevenue',
            'pendingRefunds',
            'chartData',
            'recentOrders',
            'recentMessages'
        ]);
});



