<?php

use App\Models\User;
use App\Models\Catalogue;
use App\Models\MembershipPackage;
use App\Models\Purchase;

test('catalogue price_final is dynamic based on user active membership', function () {
    // 1. Create a catalogue item
    $catalogue = Catalogue::create([
        'title' => 'Dynamic Price Catalogue',
        'short_title' => 'Dynamic',
        'price_regular' => 100.00,
        'is_discount_active' => true,
        'discount_type' => 'fixed',
        'discount_value' => 10.00,
        'service_type' => 'Course',
    ]);

    // 2. Guest User requests catalogues API
    $responseGuest = $this->getJson('/api/catalogues');
    $responseGuest->assertStatus(200);
    
    // Find the catalogue in guest response and check price_final
    $guestCatalogues = $responseGuest->json('data.catalogues');
    $guestItem = collect($guestCatalogues)->firstWhere('id', $catalogue->id);
    expect($guestItem['price_final'])->toEqual(90.00); // Guest gets standard price_final

    // 3. Normal Logged In User (No membership) requests catalogues API
    $normalUser = User::factory()->create(['role' => 'customer']);
    $responseNormal = $this->actingAs($normalUser, 'api')->getJson('/api/catalogues');
    $responseNormal->assertStatus(200);

    $normalCatalogues = $responseNormal->json('data.catalogues');
    $normalItem = collect($normalCatalogues)->firstWhere('id', $catalogue->id);
    expect($normalItem['price_final'])->toEqual(90.00); // Normal user gets standard price_final

    // 4. Premium Logged In User (With 20% discount package) requests catalogues API
    $premiumUser = User::factory()->create(['role' => 'premium_member']);
    $membershipPackage = MembershipPackage::create([
        'name' => 'Premium Plus',
        'title' => 'Premium Member Plus',
        'price' => 100.00,
        'discount_percentage' => 20.00, // 20% discount
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);

    Purchase::create([
        'user_id' => $premiumUser->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $membershipPackage->id,
        'amount' => 100.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    $responsePremium = $this->actingAs($premiumUser, 'api')->getJson('/api/catalogues');
    $responsePremium->assertStatus(200);

    $premiumCatalogues = $responsePremium->json('data.catalogues');
    $premiumItem = collect($premiumCatalogues)->firstWhere('id', $catalogue->id);
    // Premium user gets 20% off the regular price (100.00 - 20% = 80.00), which is better than catalogue final price (90.00)
    expect($premiumItem['price_final'])->toEqual(80.00); 
});
