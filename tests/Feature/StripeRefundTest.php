<?php

use App\Models\User;
use App\Models\Purchase;
use App\Models\PurchaseRefund;
use App\Models\MembershipPackage;
use Illuminate\Support\Facades\Log;

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

    $this->standardPackage = MembershipPackage::create([
        'name' => 'Standard',
        'title' => 'Standard Package',
        'price' => 0.00,
        'status' => 1,
    ]);

    // Create a paid membership order
    $this->paidOrder = Purchase::create([
        'user_id' => $this->member->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $this->package->id,
        'amount' => 99.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'stripe_session_id' => 'cs_test_123456789',
        'stripe_payment_intent_id' => 'pi_test_123456789',
    ]);
});

test('refund endpoints require authentication and admin/manager role', function () {
    $guest = User::factory()->create(['role' => 'user']);

    // Attempt refund
    $this->actingAs($guest)
        ->postJson(route('admin.orders.refund', $this->paidOrder->id), [
            'refund_type' => 'full',
        ])
        ->assertStatus(403);

    // Attempt refund history
    $this->actingAs($guest)
        ->getJson(route('admin.orders.refund-history', $this->paidOrder->id))
        ->assertStatus(403);
});

test('refund validation prevents invalid inputs', function () {
    $this->actingAs($this->admin)
        ->postJson(route('admin.orders.refund', $this->paidOrder->id), [
            'refund_type' => 'partial',
            'refund_amount' => -10.00, // Negative amount
        ])
        ->assertStatus(422);

    $this->actingAs($this->admin)
        ->postJson(route('admin.orders.refund', $this->paidOrder->id), [
            'refund_type' => 'partial',
            'refund_amount' => 150.00, // Exceeds paid amount
        ])
        ->assertStatus(400)
        ->assertJsonFragment(['success' => false]);
});

test('refund on free orders is blocked', function () {
    $freeOrder = Purchase::create([
        'user_id' => $this->member->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $this->package->id,
        'amount' => 0.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
    ]);

    $this->actingAs($this->admin)
        ->postJson(route('admin.orders.refund', $freeOrder->id), [
            'refund_type' => 'full',
        ])
        ->assertStatus(400)
        ->assertJsonFragment(['success' => false, 'message' => 'Free orders cannot be refunded.']);
});

test('full refund succeeds and records details', function () {
    $this->member->update([
        'stripe_subscription_id' => 'sub_test_full_refund',
        'stripe_subscription_status' => 'active',
        'membership_package_id' => $this->package->id,
    ]);

    // We mock the StripeClient and bind it to Laravel container
    $stripeClientMock = Mockery::mock(\Stripe\StripeClient::class);
    
    $refundsMock = Mockery::mock();
    $refundsMock->shouldReceive('create')
        ->once()
        ->with(Mockery::any())
        ->andReturn((object)[
            'id' => 're_test_full_refund',
            'status' => 'succeeded',
        ]);
        
    $subscriptionsMock = Mockery::mock();
    $subscriptionsMock->shouldReceive('cancel')
        ->once()
        ->with('sub_test_full_refund')
        ->andReturn((object)[]);

    $stripeClientMock->shouldReceive('__get')
        ->with('refunds')
        ->andReturn($refundsMock);
    $stripeClientMock->shouldReceive('getService')
        ->with('refunds')
        ->andReturn($refundsMock);

    $stripeClientMock->shouldReceive('__get')
        ->with('subscriptions')
        ->andReturn($subscriptionsMock);
    $stripeClientMock->shouldReceive('getService')
        ->with('subscriptions')
        ->andReturn($subscriptionsMock);

    $this->app->instance(\Stripe\StripeClient::class, $stripeClientMock);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.orders.refund', $this->paidOrder->id), [
            'refund_type' => 'full',
            'reason' => 'Customer requested cancellation',
        ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    $this->paidOrder->refresh();
    expect($this->paidOrder->payment_status)->toBe('refunded');
    expect($this->paidOrder->order_status)->toBe('cancelled');
    expect((float) $this->paidOrder->refunded_amount)->toBe(99.00);

    $this->member->refresh();
    expect($this->member->stripe_subscription_id)->toBeNull();
    expect($this->member->stripe_subscription_status)->toBe('canceled');
    expect($this->member->membership_package_id)->toBeNull();
    expect($this->member->active_membership->id)->toBe($this->standardPackage->id);

    $this->assertDatabaseHas('purchase_refunds', [
        'purchase_id' => $this->paidOrder->id,
        'stripe_refund_id' => 're_test_full_refund',
        'amount' => 99.00,
        'reason' => 'Customer requested cancellation',
        'admin_id' => $this->admin->id,
        'status' => 'succeeded',
    ]);
});

test('partial refund updates status and tracks history', function () {
    $stripeClientMock = Mockery::mock(\Stripe\StripeClient::class);
    $refundsMock = Mockery::mock();
    
    $refundsMock->shouldReceive('create')
        ->once()
        ->with(Mockery::any())
        ->andReturn((object)[
            'id' => 're_test_partial_refund',
            'status' => 'succeeded',
        ]);
        
    $stripeClientMock->shouldReceive('__get')
        ->with('refunds')
        ->andReturn($refundsMock);

    $stripeClientMock->shouldReceive('getService')
        ->with('refunds')
        ->andReturn($refundsMock);

    $this->app->instance(\Stripe\StripeClient::class, $stripeClientMock);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.orders.refund', $this->paidOrder->id), [
            'refund_type' => 'partial',
            'refund_amount' => 40.00,
            'reason' => 'Partial discount refund',
        ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    $this->paidOrder->refresh();
    expect($this->paidOrder->payment_status)->toBe('partially_refunded');
    expect($this->paidOrder->order_status)->toBe('active'); // remains active for partial refunds
    expect((float) $this->paidOrder->refunded_amount)->toBe(40.00);

    $this->assertDatabaseHas('purchase_refunds', [
        'purchase_id' => $this->paidOrder->id,
        'stripe_refund_id' => 're_test_partial_refund',
        'amount' => 40.00,
        'reason' => 'Partial discount refund',
        'admin_id' => $this->admin->id,
        'status' => 'succeeded',
    ]);

    // Fetch refund history and check structure
    $historyResponse = $this->actingAs($this->admin)
        ->getJson(route('admin.orders.refund-history', $this->paidOrder->id));

    $historyResponse->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'refunds' => [
                '*' => [
                    'id',
                    'stripe_refund_id',
                    'amount',
                    'reason',
                    'admin',
                    'date',
                    'status',
                ]
            ]
        ]);
});

test('charge.refunded webhook updates platform', function () {
    $this->member->update([
        'stripe_subscription_id' => 'sub_test_webhook_refund',
        'stripe_subscription_status' => 'active',
        'membership_package_id' => $this->package->id,
    ]);

    // Mock StripeClient and bind it to Laravel container for subscription cancellation in webhook
    $stripeClientMock = Mockery::mock(\Stripe\StripeClient::class);
    $subscriptionsMock = Mockery::mock();
    $subscriptionsMock->shouldReceive('cancel')
        ->once()
        ->with('sub_test_webhook_refund')
        ->andReturn((object)[]);

    $stripeClientMock->shouldReceive('__get')
        ->with('subscriptions')
        ->andReturn($subscriptionsMock);
    $stripeClientMock->shouldReceive('getService')
        ->with('subscriptions')
        ->andReturn($subscriptionsMock);

    $this->app->instance(\Stripe\StripeClient::class, $stripeClientMock);

    // Generate signature payload
    $payload = [
        'id' => 'evt_test_refund',
        'type' => 'charge.refunded',
        'data' => [
            'object' => [
                'payment_intent' => 'pi_test_123456789',
                'amount_refunded' => 9900,
                'refunded' => true,
                'refunds' => [
                    'data' => [
                        [
                            'id' => 're_webhook_full_refund',
                            'amount' => 9900,
                            'reason' => 'requested_by_customer',
                            'status' => 'succeeded',
                            'created' => time(),
                        ]
                    ]
                ]
            ]
        ]
    ];

    // The webhook no longer accepts unsigned payloads when the secret is missing,
    // so the request is signed the way Stripe signs it.
    $secret = 'whsec_test_secret';
    config(['services.stripe.webhook_secret' => $secret]);

    $json      = json_encode($payload);
    $timestamp = time();
    $signature = hash_hmac('sha256', $timestamp . '.' . $json, $secret);

    // Send post request to webhook route
    $response = $this->call(
        'POST',
        '/api/stripe/webhook',
        [],
        [],
        [],
        [
            'CONTENT_TYPE'          => 'application/json',
            'HTTP_ACCEPT'           => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
        ],
        $json
    );

    $response->assertStatus(200);

    $this->paidOrder->refresh();
    expect($this->paidOrder->payment_status)->toBe('refunded');
    expect($this->paidOrder->order_status)->toBe('cancelled');
    expect((float) $this->paidOrder->refunded_amount)->toBe(99.00);

    $this->member->refresh();
    expect($this->member->stripe_subscription_id)->toBeNull();
    expect($this->member->stripe_subscription_status)->toBe('canceled');
    expect($this->member->membership_package_id)->toBeNull();
    expect($this->member->active_membership->id)->toBe($this->standardPackage->id);

    $this->assertDatabaseHas('purchase_refunds', [
        'purchase_id' => $this->paidOrder->id,
        'stripe_refund_id' => 're_webhook_full_refund',
        'amount' => 99.00,
        'status' => 'succeeded',
    ]);
});

test('customer can request refund on their paid purchase', function () {
    $response = $this->actingAs($this->member, 'api')
        ->postJson("/api/profile/orders/{$this->paidOrder->id}/request-refund", [
            'reason' => 'I no longer need this membership.',
        ]);

    $response->assertStatus(200);
    $this->paidOrder->refresh();
    expect($this->paidOrder->refund_request_status)->toBe('pending');
    expect($this->paidOrder->refund_request_reason)->toBe('I no longer need this membership.');
    expect($this->paidOrder->refund_requested_at)->not->toBeNull();
});

test('customer cannot request refund on unpaid purchase', function () {
    $unpaidOrder = Purchase::create([
        'user_id' => $this->member->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $this->package->id,
        'amount' => 99.00,
        'payment_status' => 'pending',
    ]);

    $response = $this->actingAs($this->member, 'api')
        ->postJson("/api/profile/orders/{$unpaidOrder->id}/request-refund", [
            'reason' => 'I want a refund',
        ]);

    $response->assertStatus(400);
});

test('customer cannot duplicate refund requests', function () {
    $this->paidOrder->update([
        'refund_request_status' => 'pending',
        'refund_request_reason' => 'First request',
        'refund_requested_at' => now(),
    ]);

    $response = $this->actingAs($this->member, 'api')
        ->postJson("/api/profile/orders/{$this->paidOrder->id}/request-refund", [
            'reason' => 'Second request',
        ]);

    $response->assertStatus(400);
});

test('admin can reject refund request', function () {
    $this->paidOrder->update([
        'refund_request_status' => 'pending',
        'refund_request_reason' => 'Please refund me',
        'refund_requested_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.orders.reject-refund', $this->paidOrder->id));

    $response->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    $this->paidOrder->refresh();
    expect($this->paidOrder->refund_request_status)->toBe('rejected');
});

test('admin processing refund updates request status to approved', function () {
    $this->paidOrder->update([
        'refund_request_status' => 'pending',
        'refund_request_reason' => 'Please refund me',
        'refund_requested_at' => now(),
    ]);

    $stripeClientMock = Mockery::mock(\Stripe\StripeClient::class);
    $refundsMock = Mockery::mock();
    
    $refundsMock->shouldReceive('create')
        ->once()
        ->with(Mockery::any())
        ->andReturn((object)[
            'id' => 're_test_approved_refund',
            'status' => 'succeeded',
        ]);
        
    $stripeClientMock->shouldReceive('__get')
        ->with('refunds')
        ->andReturn($refundsMock);

    $stripeClientMock->shouldReceive('getService')
        ->with('refunds')
        ->andReturn($refundsMock);

    $this->app->instance(\Stripe\StripeClient::class, $stripeClientMock);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.orders.refund', $this->paidOrder->id), [
            'refund_type' => 'full',
            'reason' => 'Customer requested cancel',
        ]);

    $response->assertStatus(200);
    $this->paidOrder->refresh();
    expect($this->paidOrder->refund_request_status)->toBe('approved');
    expect($this->paidOrder->payment_status)->toBe('refunded');
});

test('refund fetches payment intent from checkout session invoice if payment_intent_id is missing', function () {
    $orderWithoutPi = Purchase::create([
        'user_id' => $this->member->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $this->package->id,
        'amount' => 99.00,
        'payment_status' => 'paid',
        'order_status' => 'active',
        'stripe_session_id' => 'cs_subscription_session',
        'stripe_payment_intent_id' => null, // empty!
    ]);

    $stripeClientMock = Mockery::mock(\Stripe\StripeClient::class);
    
    // Mock checkout sessions retrieve
    $checkoutMock = Mockery::mock();
    $checkoutMock->shouldReceive('retrieve')
        ->once()
        ->with('cs_subscription_session')
        ->andReturn((object)[
            'payment_intent' => null,
            'invoice' => 'in_test_invoice_id',
        ]);
        
    // Mock invoices retrieve
    $invoicesMock = Mockery::mock();
    $invoicesMock->shouldReceive('retrieve')
        ->once()
        ->with('in_test_invoice_id')
        ->andReturn((object)[
            'payment_intent' => 'pi_test_fetched_from_invoice',
        ]);

    // Mock refunds create
    $refundsMock = Mockery::mock();
    $refundsMock->shouldReceive('create')
        ->once()
        ->with([
            'payment_intent' => 'pi_test_fetched_from_invoice',
            'amount' => 9900,
        ])
        ->andReturn((object)[
            'id' => 're_test_fetched_pi',
            'status' => 'succeeded',
        ]);

    $stripeClientMock->shouldReceive('__get')->with('checkout')->andReturn((object)['sessions' => $checkoutMock]);
    $stripeClientMock->shouldReceive('getService')->with('checkout')->andReturn((object)['sessions' => $checkoutMock]);

    $stripeClientMock->shouldReceive('__get')->with('invoices')->andReturn($invoicesMock);
    $stripeClientMock->shouldReceive('getService')->with('invoices')->andReturn($invoicesMock);

    $stripeClientMock->shouldReceive('__get')->with('refunds')->andReturn($refundsMock);
    $stripeClientMock->shouldReceive('getService')->with('refunds')->andReturn($refundsMock);
    
    // Yajra DataTables/Laravel container resolving for StripeClient
    $this->app->instance(\Stripe\StripeClient::class, $stripeClientMock);

    // Make the request
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.orders.refund', $orderWithoutPi->id), [
            'refund_type' => 'full',
        ]);

    $response->assertStatus(200);
    $orderWithoutPi->refresh();
    expect($orderWithoutPi->stripe_payment_intent_id)->toBe('pi_test_fetched_from_invoice');
    expect($orderWithoutPi->payment_status)->toBe('refunded');
});

test('admin can process a manual refund inside the system if Stripe transaction ID is missing', function () {
    // Create a purchase record with NO Stripe session/payment intent IDs (e.g. manual order)
    $manualOrder = Purchase::create([
        'user_id' => $this->member->id,
        'purchase_type' => 'membership',
        'membership_package_id' => $this->package->id,
        'amount' => 150.00,
        'payment_status' => 'paid',
        'order_status' => 'completed',
        'order_id' => 'ORD-MEM-MANUAL-789',
        'stripe_session_id' => null,
        'stripe_payment_intent_id' => null,
    ]);

    // Make the request to refund it
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.orders.refund', $manualOrder->id), [
            'refund_type' => 'full',
            'reason' => 'Offline refund requested',
        ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);
    expect($response->json('message'))->toContain('Manual refund processed successfully');

    $manualOrder->refresh();
    expect($manualOrder->payment_status)->toBe('refunded');
    expect($manualOrder->order_status)->toBe('cancelled');
    expect($manualOrder->refunded_amount)->toEqual(150.00);

    // Verify it created a PurchaseRefund log starting with MANUAL-
    $refundRecord = PurchaseRefund::where('purchase_id', $manualOrder->id)->first();
    expect($refundRecord)->not->toBeNull();
    expect($refundRecord->stripe_refund_id)->toStartWith('MANUAL-');
    expect($refundRecord->amount)->toEqual(150.00);
    expect($refundRecord->reason)->toBe('Offline refund requested');
});


