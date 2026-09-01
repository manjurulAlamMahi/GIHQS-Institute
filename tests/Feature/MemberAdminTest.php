<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    // Create Standard and Premium packages
    $this->standardPackage = \App\Models\MembershipPackage::create([
        'name' => 'Standard',
        'title' => 'Standard Member',
        'price' => 50.00,
        'discount_percentage' => 0.00,
        'exam_attempt_limit' => 1,
        'status' => 1,
    ]);

    $this->premiumPackage = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 99.00,
        'discount_percentage' => 15.00,
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);

    // Create some members (all have role 'user')
    $this->normalUser = User::factory()->create([
        'role' => 'user',
        'first_name' => 'Normal',
        'last_name' => 'User',
        'status' => 1,
    ]);

    $this->standardMember = User::factory()->create([
        'role' => 'user',
        'first_name' => 'Standard',
        'last_name' => 'Member',
        'status' => 1,
    ]);

    $this->premiumMember = User::factory()->create([
        'role' => 'user',
        'first_name' => 'Premium',
        'last_name' => 'Member',
        'status' => 1,
    ]);
});

it('requires admin or manager role to access members list', function () {
    $guest = User::factory()->create([
        'role' => 'user',
    ]);

    $response = $this->actingAs($guest)->get(route('admin.members.index'));
    $response->assertStatus(403);
});

it('can render members index page for admin', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.members.index'));
    $response->assertStatus(200);
});

it('can fetch members list ajax request', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.members.index'), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['data']);
    
    $data = $response->json('data');
    expect(count($data))->toBeGreaterThanOrEqual(3);
});

it('can filter members list by membership', function () {
    // Create active membership purchase for standard member
    \App\Models\Purchase::create([
        'user_id' => $this->standardMember->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $this->standardPackage->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    // Filter by standard_member
    $response = $this->actingAs($this->admin)->get(route('admin.members.index', ['role' => 'standard_member']), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $data = $response->json('data');
    
    // Check if the only returned member is the standard member
    expect(count($data))->toBe(1);
    expect($data[0]['name'])->toContain('Standard Member');
});

it('can block and unblock a user via status update route', function () {
    // Block user
    $response = $this->actingAs($this->admin)->post(route('admin.status.update'), [
        'type' => 'user',
        'id' => $this->normalUser->id,
        'status' => '0',
    ]);

    $response->assertStatus(200);
    $this->normalUser->refresh();
    expect($this->normalUser->status)->toBe(0);

    // Unblock user
    $response = $this->actingAs($this->admin)->post(route('admin.status.update'), [
        'type' => 'user',
        'id' => $this->normalUser->id,
        'status' => '1',
    ]);

    $response->assertStatus(200);
    $this->normalUser->refresh();
    expect($this->normalUser->status)->toBe(1);
});

it('returns correct expiry_date values in the ajax response', function () {
    // 1. Create a purchase with expires_at for premium member
    \App\Models\Purchase::create([
        'user_id' => $this->premiumMember->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $this->premiumPackage->id,
        'amount' => 99.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    // 2. Create a purchase with null expires_at (lifetime) for standard member
    \App\Models\Purchase::create([
        'user_id' => $this->standardMember->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $this->standardPackage->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => null,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.members.index'), [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $data = $response->json('data');

    // Find the premium member row
    $premiumRow = collect($data)->firstWhere('email', $this->premiumMember->email);
    expect($premiumRow['expiry_date'])->toBe(now()->addDays(30)->format('M d, Y'));

    // Find the standard member row
    $standardRow = collect($data)->firstWhere('email', $this->standardMember->email);
    expect($standardRow['expiry_date'])->toContain('Lifetime');

    // Find the normal user row
    $normalRow = collect($data)->firstWhere('email', $this->normalUser->email);
    expect($normalRow['expiry_date'])->toContain('N/A');
});
