<?php

use App\Models\User;
use App\Models\Catalogue;

test('GET /checkout/{id} requires authentication', function () {
    $catalogue = Catalogue::create([
        'title' => 'Test Catalogue',
        'short_title' => 'Test',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    $response = $this->getJson("/api/checkout/{$catalogue->id}");

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Please login first',
        ]);
});

test('GET /checkout/{id} handles non-existent catalogue', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/checkout/9999");

    $response->assertStatus(404);
});

test('GET /checkout/{id} successfully initiates Stripe checkout session', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Test Catalogue',
        'short_title' => 'Test',
        'price_regular' => 5.00,
        'service_type' => 'Course',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/checkout/{$catalogue->id}");

    // It should either return a JSON redirect_url if expectsJson() is true,
    // or return a 302 redirect to Stripe.
    // Since we used ->getJson(), expectsJson() is true.
    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'redirect_url',
                'session_id',
                'purchase_id',
                'order_id',
                'catalogue' => [
                    'id',
                    'title',
                    'price',
                    'price_type',
                ]
            ]
        ]);

    $this->assertDatabaseHas('purchases', [
        'user_id' => $user->id,
        'catalogue_id' => $catalogue->id,
        'payment_status' => 'pending',
    ]);
});

test('GET /checkout/{id} redirects directly to Stripe if not expecting json', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Test Catalogue',
        'short_title' => 'Test',
        'price_regular' => 5.00,
        'service_type' => 'Course',
    ]);

    $response = $this->actingAs($user, 'api')
        ->get("/api/checkout/{$catalogue->id}");

    // It should return 302 redirect to Stripe
    $response->assertStatus(302);
    $this->assertStringContainsString('checkout.stripe.com', $response->headers->get('Location'));
});

test('GET /checkout/{id} supports session-based authentication (web guard)', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Session Catalogue',
        'short_title' => 'Session Test',
        'price_regular' => 5.00,
        'service_type' => 'Course',
    ]);

    // Authenticate using the default web guard instead of api guard
    $response = $this->actingAs($user)
        ->get("/api/checkout/{$catalogue->id}");

    $response->assertStatus(302);
    $this->assertStringContainsString('checkout.stripe.com', $response->headers->get('Location'));
});

test('GET /checkout/{id} with free price activates immediately and returns json response if expecting json', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Free Catalogue',
        'short_title' => 'Free',
        'price_regular' => 0.00,
        'service_type' => 'Course',
    ]);

    $response = $this->actingAs($user, 'api')
        ->getJson("/api/checkout/{$catalogue->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'You have been enrolled successfully.',
        ])
        ->assertJsonStructure([
            'data' => [
                'redirect_url',
                'session_id',
                'purchase_id',
                'order_id',
                'catalogue' => [
                    'id',
                    'title',
                    'price',
                    'price_type',
                ]
            ]
        ]);

    $this->assertDatabaseHas('purchases', [
        'user_id' => $user->id,
        'catalogue_id' => $catalogue->id,
        'payment_status' => 'paid',
        'order_status' => 'completed',
        'amount' => 0.00,
        'payment_method' => 'Free',
    ]);
});

test('GET /checkout/{id} with free price activates immediately and redirects if not expecting json', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Free Catalogue',
        'short_title' => 'Free',
        'price_regular' => 0.00,
        'service_type' => 'Course',
    ]);

    $response = $this->actingAs($user, 'api')
        ->get("/api/checkout/{$catalogue->id}");

    $response->assertStatus(302);
    $response->assertRedirect(env('FRONTEND_ENROLLMENT_SUCCESS_URL', 'https://gihqs.vercel.app/enrollment/success'));

    $this->assertDatabaseHas('purchases', [
        'user_id' => $user->id,
        'catalogue_id' => $catalogue->id,
        'payment_status' => 'paid',
        'order_status' => 'completed',
        'amount' => 0.00,
        'payment_method' => 'Free',
    ]);
});

test('POST /checkout with free price activates immediately and returns success', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Free Catalogue',
        'short_title' => 'Free',
        'price_regular' => 0.00,
        'service_type' => 'Course',
    ]);

    $response = $this->actingAs($user, 'api')
        ->postJson("/api/checkout", [
            'catalogue_id' => $catalogue->id,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'You have been enrolled successfully.',
        ]);

    $this->assertDatabaseHas('purchases', [
        'user_id' => $user->id,
        'catalogue_id' => $catalogue->id,
        'payment_status' => 'paid',
        'order_status' => 'completed',
        'amount' => 0.00,
        'payment_method' => 'Free',
    ]);
});

test('GET /checkout/{id} handles premium member free price vs regular user paid price', function () {
    $catalogue = Catalogue::create([
        'title' => 'Mixed Pricing Catalogue',
        'short_title' => 'Mixed',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    // Scenario 1: Premium Member gets it free (bypasses Stripe)
    $premiumUser = User::factory()->create(['role' => 'premium_member']);
    
    // Create a 100% discount membership package and give it to the premium user
    $membershipPackage = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 95.00,
        'discount_percentage' => 100.00,
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);

    \App\Models\Purchase::create([
        'user_id' => $premiumUser->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $membershipPackage->id,
        'amount' => 95.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    $response1 = $this->actingAs($premiumUser, 'api')
        ->getJson("/api/checkout/{$catalogue->id}");

    $response1->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'You have been enrolled successfully.',
        ]);

    $this->assertDatabaseHas('purchases', [
        'user_id' => $premiumUser->id,
        'catalogue_id' => $catalogue->id,
        'payment_status' => 'paid',
        'price_type' => 'member',
        'amount' => 0.00,
        'price_regular' => 50.00,
        'price_purchased' => 0.00,
        'discount_amount' => 50.00,
        'discount_percentage' => 100.00,
    ]);

    // Scenario 2: Regular user gets charged regular price (goes to Stripe)
    $regularUser = User::factory()->create(['role' => 'customer']);
    $response2 = $this->actingAs($regularUser, 'api')
        ->getJson("/api/checkout/{$catalogue->id}");

    // Should return Stripe redirect JSON
    $response2->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'redirect_url',
                'session_id',
            ]
        ]);

    $this->assertDatabaseHas('purchases', [
        'user_id' => $regularUser->id,
        'catalogue_id' => $catalogue->id,
        'payment_status' => 'pending',
        'price_type' => 'regular',
        'amount' => 50.00,
        'price_regular' => 50.00,
        'price_purchased' => 50.00,
        'discount_amount' => 0.00,
        'discount_percentage' => 0.00,
    ]);
});

test('GET /checkout/{id} and POST /checkout rejects duplicate catalogue purchases', function () {
    $user = User::factory()->create();
    $catalogue = Catalogue::create([
        'title' => 'Duplicate Test Catalogue',
        'short_title' => 'Dup Test',
        'price_regular' => 50.00,
        'service_type' => 'Course',
    ]);

    // Create an existing paid purchase for this user
    \App\Models\Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'catalogue',
        'catalogue_id' => $catalogue->id,
        'amount' => 50.00,
        'payment_status' => 'paid',
        'order_status' => 'completed',
    ]);

    // Test GET /checkout/{id} endpoint
    $responseGet = $this->actingAs($user, 'api')
        ->getJson("/api/checkout/{$catalogue->id}");
    $responseGet->assertStatus(400)
                ->assertJson([
                    'status' => false,
                    'message' => 'You already have access to this item.',
                ]);

    // Test POST /checkout endpoint
    $responsePost = $this->actingAs($user, 'api')
        ->postJson("/api/checkout", [
            'catalogue_id' => $catalogue->id,
        ]);
    $responsePost->assertStatus(400)
                 ->assertJson([
                     'status' => false,
                     'message' => 'You already have access to this item.',
                 ]);
});

test('POST /api/membership/checkout rejects duplicate membership package subscriptions', function () {
    $user = User::factory()->create();
    
    $package = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 99.00,
        'discount_percentage' => 15.00,
        'exam_attempt_limit' => 3,
        'status' => 1,
    ]);

    // Create an existing active paid subscription
    \App\Models\Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $package->id,
        'amount' => 99.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    // Test POST /api/membership/checkout
    $response = $this->actingAs($user, 'api')
        ->postJson("/api/membership/checkout", [
            'membership_package_id' => $package->id,
        ]);

    $response->assertStatus(400)
             ->assertJson([
                 'status' => false,
                 'message' => 'You already subscribed this package.',
             ]);
});

test('user with an active membership package can switch or downgrade to a different package', function () {
    $user = User::factory()->create();

    $goldPackage = \App\Models\MembershipPackage::create([
        'name' => 'Gold Package',
        'title' => 'Gold Package',
        'price' => 199.00,
        'validity_days' => 30,
        'status' => 1,
    ]);

    $premiumPackage = \App\Models\MembershipPackage::create([
        'name' => 'Premium Package',
        'title' => 'Premium Package',
        'price' => 99.00,
        'validity_days' => 30,
        'status' => 1,
    ]);

    // Active subscription for Gold
    \App\Models\Purchase::create([
        'user_id' => $user->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $goldPackage->id,
        'amount' => 199.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'expires_at' => now()->addDays(30),
    ]);

    // Test POST /api/membership/checkout for Premium (should succeed/redirect, not get blocked)
    $response = $this->actingAs($user, 'api')
        ->postJson("/api/membership/checkout", [
            'membership_package_id' => $premiumPackage->id,
        ]);

    $response->assertStatus(200);
});

test('accreditation checkout cancel callback updates status and redirects', function () {
    $application = \App\Models\AccreditationApplication::create([
        'applicant_category' => 'University',
        'applicant_name' => 'Harvard University',
        'country' => 'USA',
        'city' => 'Cambridge',
        'program_name' => 'MD Program',
        'program_type' => 'Medical Degree',
        'program_delivery_format' => 'On Campus',
        'primary_contact_person' => 'Dr. Smith',
        'contact_title_position' => 'Dean',
        'email_address' => 'dean@harvard.edu',
        'payment_status' => 'pending',
    ]);

    // The cancel callback now requires the signed URL this application generates
    // for the Stripe session; an unsigned guess is rejected.
    $this->get(route('accreditation.checkout.cancel') . '?accreditation_application_id=' . $application->id)
        ->assertStatus(403);

    expect($application->fresh()->payment_status)->toBe('pending');

    $signedUrl = \Illuminate\Support\Facades\URL::signedRoute(
        'accreditation.checkout.cancel',
        ['accreditation_application_id' => $application->id]
    );

    $response = $this->get($signedUrl);

    $response->assertRedirect();
    $this->assertEquals(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'), $response->headers->get('Location'));

    $application->refresh();
    expect($application->payment_status)->toBe('cancelled');
});

test('GET /api/membership-packages returns relative user status metadata', function () {
    $user = User::factory()->create();

    $standard = \App\Models\MembershipPackage::create([
        'name' => 'Standard',
        'title' => 'Standard Member',
        'price' => 0.00,
        'status' => 1,
    ]);

    $premium = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 95.00,
        'status' => 1,
    ]);

    // Scenario 1: Unauthenticated user
    $responseUnauth = $this->getJson('/api/membership-packages');
    $responseUnauth->assertStatus(200);
    $packages = $responseUnauth->json('data.membership_packages');
    
    // Check that we returned the keys
    expect($packages[0])->toHaveKeys(['is_current', 'status_label', 'allowed_actions']);
    expect($packages[0]['is_current'])->toBeFalse();
    expect($packages[0]['status_label'])->toBeNull();

    // Scenario 2: Authenticated user with Premium membership
    $user->update([
        'membership_package_id' => $premium->id,
        'stripe_subscription_id' => 'sub_test123',
        'stripe_subscription_status' => 'active',
        'stripe_subscription_cancel_at_period_end' => false,
    ]);

    $responseAuth = $this->actingAs($user, 'api')->getJson('/api/membership-packages');
    $responseAuth->assertStatus(200);
    $authPackages = $responseAuth->json('data.membership_packages');

    // Standard package (index 0) should be a downgrade
    expect($authPackages[0]['is_current'])->toBeFalse();
    expect($authPackages[0]['status_label'])->toBe('downgrade');
    expect($authPackages[0]['allowed_actions'])->toContain('downgrade');

    // Premium package (index 1) should be current and cancelable
    expect($authPackages[1]['is_current'])->toBeTrue();
    expect($authPackages[1]['status_label'])->toBe('current');
    expect($authPackages[1]['allowed_actions'])->toContain('cancel');
});

test('POST /api/membership/checkout handles downgrade to Standard by cancelling Stripe subscription auto-renewal', function () {
    $user = User::factory()->create([
        'stripe_subscription_id' => 'sub_test_downgrade',
        'stripe_subscription_status' => 'active',
        'stripe_subscription_cancel_at_period_end' => false,
    ]);

    $standard = \App\Models\MembershipPackage::create([
        'name' => 'Standard',
        'title' => 'Standard Member',
        'price' => 0.00,
        'status' => 1,
    ]);

    $premium = \App\Models\MembershipPackage::create([
        'name' => 'Premium',
        'title' => 'Premium Member',
        'price' => 95.00,
        'status' => 1,
    ]);

    // Set Premium as user's current package
    $user->update(['membership_package_id' => $premium->id]);

    // Mock StripeClient subscriptions update
    $stripeClientMock = Mockery::mock(\Stripe\StripeClient::class);
    $subscriptionsMock = Mockery::mock();
    $subscriptionsMock->shouldReceive('update')
        ->with('sub_test_downgrade', ['cancel_at_period_end' => true])
        ->once()
        ->andReturn((object)['id' => 'sub_test_downgrade', 'cancel_at_period_end' => true]);
    $stripeClientMock->shouldReceive('__get')->with('subscriptions')->andReturn($subscriptionsMock);
    $stripeClientMock->shouldReceive('getService')->with('subscriptions')->andReturn($subscriptionsMock);
    $this->app->instance(\Stripe\StripeClient::class, $stripeClientMock);

    // Call checkout endpoint requesting Standard plan
    $response = $this->actingAs($user, 'api')
        ->postJson('/api/membership/checkout', [
            'membership_package_id' => $standard->id,
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('data.cancel_at_period_end', true);
    
    $user->refresh();
    expect($user->stripe_subscription_cancel_at_period_end)->toBeTrue();
});

