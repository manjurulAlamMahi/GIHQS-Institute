<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catalogue;
use App\Models\MembershipPackage;
use App\Models\Purchase;
use App\Models\AdvisoryRequest;
use App\Models\AccreditationApplication;
use App\Models\User;
use App\Traits\ApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Traits\LogsEmails;
use Stripe\Stripe;
use Throwable;

class CheckoutController extends Controller
{
    use ApiResponse, LogsEmails;

    /**
     * Create a Stripe Checkout Session for a Catalogue item based on User's role (GET version).
     *
     * GET /api/checkout/{id}
     */
    public function checkoutGet(Request $request, $id)
    {
        try {
            // Authenticated User Check
            $user = Auth::guard('api')->user() ?? Auth::guard('web')->user() ?? auth()->user();
            if (!$user) {
                if ($request->expectsJson()) {
                    return $this->errorResponse([], 'User not authenticated.', 401);
                }
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel') . '&error=unauthenticated');
            }

            // Retrieve Catalogue item
            $catalogue = Catalogue::find($id);
            if (!$catalogue) {
                if ($request->expectsJson()) {
                    return $this->errorResponse([], 'Catalogue item not found.', 404);
                }
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel') . '&error=not_found');
            }

            // Check if the user has access via active membership or has already successfully purchased this catalogue item
            $hasAccess = false;
            if ($user->active_paid_membership && $catalogue->catalogue_type === 'members only') {
                $hasAccess = true;
            } else {
                if ($catalogue->service_type === 'Certification') {
                    $hasAccess = $user->hasValidCertification($catalogue->id);
                } else {
                    $hasAccess = Purchase::where('user_id', $user->id)
                        ->where('purchase_type', 'catalogue')
                        ->where('catalogue_id', $catalogue->id)
                        ->where('payment_status', 'paid')
                        ->exists();
                }
            }

            if ($hasAccess) {
                if ($request->expectsJson()) {
                    return $this->errorResponse([], 'You already have access to this item.', 400);
                }
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel') . '&error=already_purchased');
            }

            // If it's a members only course but the user doesn't have active paid membership
            if ($catalogue->catalogue_type === 'members only' && !$user->active_paid_membership) {
                if ($request->expectsJson()) {
                    return $this->errorResponse([], 'This course is for premium/paid members only. Please subscribe to a paid membership package to access it.', 403);
                }
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel') . '&error=members_only_required');
            }

            // Determine price, regular price, and discounts dynamically
            $price = $catalogue->calculateFinalPriceForUser($user);
            $priceRegular = (float) $catalogue->price_regular;
            $discountAmount = max(0.00, $priceRegular - $price);
            $discountPercent = $priceRegular > 0 ? ($discountAmount / $priceRegular) * 100 : 0.00;
            $priceType = $user->active_paid_membership ? 'member' : 'regular';

            // If the catalogue is free ($0.00), purchase immediately without Stripe
            if (floatval($price) <= 0) {
                $purchase = Purchase::create([
                    'user_id'             => $user->id,
                    'purchase_type'       => 'catalogue',
                    'catalogue_id'        => $catalogue->id,
                    'amount'              => 0.00,
                    'price_regular'       => $priceRegular,
                    'price_purchased'     => 0.00,
                    'discount_amount'     => $priceRegular,
                    'discount_percentage' => 100.00,
                    'price_type'          => $priceType,
                    'payment_status'      => 'paid',
                    'order_status'        => 'completed',
                    'payment_method'      => 'Free',
                ]);

                $redirectUrl = env('FRONTEND_ENROLLMENT_SUCCESS_URL', 'https://gihqs.vercel.app/enrollment/success');

                if ($request->expectsJson()) {
                    $response = [
                        'redirect_url' => $redirectUrl,
                        'session_id'   => null,
                        'purchase_id'  => $purchase->id,
                        'order_id'     => $purchase->order_id,
                        'catalogue'    => [
                            'id'                  => $catalogue->id,
                            'title'               => $catalogue->title,
                            'price'               => 0.00,
                            'price_type'          => $priceType,
                            'price_regular'       => (float) $priceRegular,
                            'price_purchased'     => 0.00,
                            'discount_amount'     => (float) $priceRegular,
                            'discount_percentage' => 100.00,
                        ],
                    ];

                    return $this->successResponse($response, 'You have been enrolled successfully.', 200);
                }

                return redirect()->away($redirectUrl);
            }

            // Set Stripe Secret Key
            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                if ($request->expectsJson()) {
                    return $this->errorResponse([], 'Stripe secret key configuration is missing.', 500);
                }
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel') . '&error=missing_stripe_key');
            }

            // 1. Create a pending purchase log in the database
            $purchase = Purchase::create([
                'user_id'             => $user->id,
                'purchase_type'       => 'catalogue',
                'catalogue_id'        => $catalogue->id,
                'amount'              => $price,
                'price_regular'       => $priceRegular,
                'price_purchased'     => $price,
                'discount_amount'     => $discountAmount,
                'discount_percentage' => $discountPercent,
                'price_type'          => $priceType,
                'payment_status'      => 'pending',
            ]);

            Stripe::setApiKey($stripeSecret);

            // 2. Generate callback URLs (success gets Stripe Session ID, cancel gets local Purchase ID)
            $successUrl = route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl  = URL::signedRoute('checkout.cancel', ['purchase_id' => $purchase->id]);

            // 3. Create Stripe Checkout Session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name'        => $catalogue->title,
                            'description' => $catalogue->short_description ?? 'Catalogue Purchase',
                        ],
                        'unit_amount' => intval($price * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => $successUrl,
                'cancel_url'  => $cancelUrl,
                'metadata'    => [
                    'type'         => 'catalogue',
                    'catalogue_id' => $catalogue->id,
                    'user_id'      => $user->id,
                    'price_type'   => $priceType,
                    'purchase_id'  => $purchase->id,
                ],
            ]);

            // 4. Update the purchase log with the generated Stripe Session ID
            $purchase->update([
                'stripe_session_id' => $session->id,
            ]);

            if ($request->expectsJson()) {
                $response = [
                    'redirect_url' => $session->url,
                    'session_id'   => $session->id,
                    'purchase_id'  => $purchase->id,
                    'order_id'     => $purchase->order_id,
                    'catalogue'    => [
                        'id'                  => $catalogue->id,
                        'title'               => $catalogue->title,
                        'price'               => (float) $price,
                        'price_type'          => $priceType,
                        'price_regular'       => (float) $priceRegular,
                        'price_purchased'     => (float) $price,
                        'discount_amount'     => (float) $discountAmount,
                        'discount_percentage' => (float) $discountPercent,
                    ],
                ];

                return $this->successResponse($response, 'Stripe checkout session created successfully.', 200);
            }

            return redirect()->away($session->url);

        } catch (Throwable $th) {
            if ($request->expectsJson()) {
                return $this->errorResponse([], 'Failed to initiate checkout: ' . $th->getMessage(), 500);
            }
            return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel') . '&error=' . urlencode($th->getMessage()));
        }
    }

    public function checkout(Request $request)
    {
        try {
            // Validate the request body
            $request->validate([
                'catalogue_id' => 'required|exists:catalogues,id',
            ]);

            // Authenticated User Check
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not authenticated.', 401);
            }

            // Retrieve Catalogue item
            $catalogue = Catalogue::find($request->catalogue_id);
            if (!$catalogue) {
                return $this->errorResponse([], 'Catalogue item not found.', 404);
            }

            // Check if the user has access via active membership or has already successfully purchased this catalogue item
            $hasAccess = false;
            if ($user->active_paid_membership && $catalogue->catalogue_type === 'members only') {
                $hasAccess = true;
            } else {
                if ($catalogue->service_type === 'Certification') {
                    $hasAccess = $user->hasValidCertification($catalogue->id);
                } else {
                    $hasAccess = Purchase::where('user_id', $user->id)
                        ->where('purchase_type', 'catalogue')
                        ->where('catalogue_id', $catalogue->id)
                        ->where('payment_status', 'paid')
                        ->exists();
                }
            }

            if ($hasAccess) {
                return $this->errorResponse([], 'You already have access to this item.', 400);
            }

            // If it's a members only course but the user doesn't have active paid membership
            if ($catalogue->catalogue_type === 'members only' && !$user->active_paid_membership) {
                return $this->errorResponse([], 'This course is for premium/paid members only. Please subscribe to a paid membership package to access it.', 403);
            }

            // Determine price, regular price, and discounts dynamically
            $price = $catalogue->calculateFinalPriceForUser($user);
            $priceRegular = (float) $catalogue->price_regular;
            $discountAmount = max(0.00, $priceRegular - $price);
            $discountPercent = $priceRegular > 0 ? ($discountAmount / $priceRegular) * 100 : 0.00;
            $priceType = $user->active_paid_membership ? 'member' : 'regular';

            // If the catalogue is free ($0.00), purchase immediately without Stripe
            if (floatval($price) <= 0) {
                $purchase = Purchase::create([
                    'user_id'             => $user->id,
                    'purchase_type'       => 'catalogue',
                    'catalogue_id'        => $catalogue->id,
                    'amount'              => 0.00,
                    'price_regular'       => $priceRegular,
                    'price_purchased'     => 0.00,
                    'discount_amount'     => $priceRegular,
                    'discount_percentage' => 100.00,
                    'price_type'          => $priceType,
                    'payment_status'      => 'paid',
                    'order_status'        => 'completed',
                    'payment_method'      => 'Free',
                ]);

                $redirectUrl = env('FRONTEND_ENROLLMENT_SUCCESS_URL', 'https://gihqs.vercel.app/enrollment/success');

                $response = [
                    'redirect_url' => $redirectUrl,
                    'session_id'   => null,
                    'purchase_id'  => $purchase->id,
                    'order_id'     => $purchase->order_id,
                    'catalogue'    => [
                        'id'                  => $catalogue->id,
                        'title'               => $catalogue->title,
                        'price'               => 0.00,
                        'price_type'          => $priceType,
                        'price_regular'       => (float) $priceRegular,
                        'price_purchased'     => 0.00,
                        'discount_amount'     => (float) $priceRegular,
                        'discount_percentage' => 100.00,
                    ],
                ];

                return $this->successResponse($response, 'You have been enrolled successfully.', 200);
            }

            // Set Stripe Secret Key
            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                return $this->errorResponse([], 'Stripe secret key configuration is missing.', 500);
            }

            // 1. Create a pending purchase log in the database
            $purchase = Purchase::create([
                'user_id'             => $user->id,
                'purchase_type'       => 'catalogue',
                'catalogue_id'        => $catalogue->id,
                'amount'              => $price,
                'price_regular'       => $priceRegular,
                'price_purchased'     => $price,
                'discount_amount'     => $discountAmount,
                'discount_percentage' => $discountPercent,
                'price_type'          => $priceType,
                'payment_status'      => 'pending',
            ]);

            Stripe::setApiKey($stripeSecret);

            // 2. Generate callback URLs (success gets Stripe Session ID, cancel gets local Purchase ID)
            $successUrl = route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl  = URL::signedRoute('checkout.cancel', ['purchase_id' => $purchase->id]);

            // 3. Create Stripe Checkout Session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name'        => $catalogue->title,
                            'description' => $catalogue->short_description ?? 'Catalogue Purchase',
                        ],
                        'unit_amount' => intval($price * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => $successUrl,
                'cancel_url'  => $cancelUrl,
                'metadata'    => [
                    'type'         => 'catalogue',
                    'catalogue_id' => $catalogue->id,
                    'user_id'      => $user->id,
                    'price_type'   => $priceType,
                    'purchase_id'  => $purchase->id,
                ],
            ]);

            // 4. Update the purchase log with the generated Stripe Session ID
            $purchase->update([
                'stripe_session_id' => $session->id,
            ]);

            $response = [
                'redirect_url' => $session->url,
                'session_id'   => $session->id,
                'purchase_id'  => $purchase->id,
                'order_id'     => $purchase->order_id,
                'catalogue'    => [
                    'id'                  => $catalogue->id,
                    'title'               => $catalogue->title,
                    'price'               => (float) $price,
                    'price_type'          => $priceType,
                    'price_regular'       => (float) $priceRegular,
                    'price_purchased'     => (float) $price,
                    'discount_amount'     => (float) $discountAmount,
                    'discount_percentage' => (float) $discountPercent,
                ],
            ];

            return $this->successResponse($response, 'Stripe checkout session created successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to initiate checkout: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Handle Stripe Checkout Success Callback.
     *
     * GET /api/checkout/success
     */
    public function success(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');
            if (empty($sessionId)) {
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            Stripe::setApiKey($stripeSecret);

            // Retrieve Session details from Stripe
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (!$session || $session->payment_status !== 'paid') {
                // If payment failed or session invalid, find purchase and mark as failed
                $purchase = Purchase::where('stripe_session_id', $sessionId)->first();
                if ($purchase) {
                    $purchase->update([
                        'payment_status' => 'unpaid',
                        'order_status'   => 'cancelled',
                    ]);
                }
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            // Find the pending purchase record and mark it as paid
            $purchase = Purchase::where('stripe_session_id', $sessionId)
                ->where('payment_status', 'pending')
                ->first();

            if ($purchase) {
                $paymentMethod = $this->getPaymentMethodFromStripe($session);
                $purchase->update([
                    'payment_status' => 'paid',
                    'order_status'   => 'completed',
                    'payment_method' => $paymentMethod,
                    'stripe_payment_intent_id' => $session->payment_intent,
                ]);
            }

            // Payment is successful, redirect to frontend success url
            $redirectUrl = env('FRONTEND_PAYMENT_SUCCESS_URL', 'https://gihqs.vercel.app/payment/success');
            return redirect()->away($redirectUrl);

        } catch (Throwable $th) {
            return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
        }
    }

    /**
     * Handle Stripe Checkout Cancel Callback.
     *
     * GET /api/checkout/cancel
     */
    public function cancel(Request $request)
    {
        try {
            $purchaseId = $request->query('purchase_id');
            if ($purchaseId) {
                // Find and update the purchase log to cancelled
                $purchase = Purchase::find($purchaseId);
                if ($purchase && $purchase->payment_status === 'pending') {
                    $purchase->update([
                        'payment_status' => 'cancelled',
                        'order_status'   => 'cancelled',
                    ]);
                }
            }
        } catch (Throwable $th) {
            // Log error or ignore quietly during redirect
        }

        return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
    }

    /**
     * Create a Stripe Checkout Session for a Membership Package.
     *
     * POST /api/membership/checkout
     */
    public function membershipCheckout(Request $request)
    {
        try {
            // Validate the request body
            $request->validate([
                'membership_package_id' => 'required|exists:membership_packages,id',
            ]);

            // Authenticated User Check
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not authenticated.', 401);
            }

            // Retrieve Membership Package
            $package = MembershipPackage::find($request->membership_package_id);
            if (!$package) {
                return $this->errorResponse([], 'Membership package not found.', 404);
            }

            // Check if this package is the user's current active membership
            $activeMembership = $user->active_membership;
            if ($activeMembership) {
                if ($activeMembership->id === $package->id) {
                    return $this->errorResponse([], 'You already subscribed this package.', 400);
                }

                // If user is downgrading to the free Standard package
                if (floatval($package->price) <= 0) {
                    if (!empty($user->stripe_subscription_id)) {
                        $stripeSecret = config('services.stripe.secret');
                        if (empty($stripeSecret)) {
                            return $this->errorResponse([], 'Stripe secret key configuration is missing.', 500);
                        }
                        try {
                            $stripe = app(\Stripe\StripeClient::class);
                            $stripe->subscriptions->update($user->stripe_subscription_id, [
                                'cancel_at_period_end' => true
                            ]);

                            $user->update([
                                'stripe_subscription_cancel_at_period_end' => true,
                            ]);

                            $response = [
                                'has_active_subscription' => true,
                                'stripe_subscription_id'  => $user->stripe_subscription_id,
                                'cancel_at_period_end'    => true,
                            ];
                            return $this->successResponse(
                                $response, 
                                'You have downgraded to the Standard plan. Your Premium benefits will remain active until the end of your billing cycle, after which your account will revert to the Standard plan.', 
                                200
                            );
                        } catch (Throwable $e) {
                            return $this->errorResponse([], 'Failed to cancel subscription for downgrade: ' . $e->getMessage(), 500);
                        }
                    } else {
                        // User has manual/admin assigned paid membership. Downgrade immediately.
                        $activePurchase = $user->activeMembershipPurchase;
                        if ($activePurchase) {
                            $activePurchase->update([
                                'order_status' => 'cancelled',
                                'expires_at' => now(),
                            ]);
                        }
                        $user->update([
                            'membership_package_id' => $package->id,
                            'stripe_subscription_status' => null,
                            'stripe_subscription_id' => null,
                        ]);
                        $user->assignDefaultMembership();

                        return $this->successResponse([
                            'package_id' => $package->id,
                        ], 'Downgraded to Standard plan successfully.', 200);
                    }
                }

                // Upgrade / Downgrade between paid packages for Stripe subscriptions
                if (!empty($user->stripe_subscription_id)) {
                    $stripeSecret = config('services.stripe.secret');
                    if (empty($stripeSecret)) {
                        return $this->errorResponse([], 'Stripe secret key configuration is missing.', 500);
                    }
                    try {
                        $stripe = app(\Stripe\StripeClient::class);
                        $sub = $stripe->subscriptions->retrieve($user->stripe_subscription_id);
                        if ($sub && count($sub->items->data) > 0) {
                            $subItemId = $sub->items->data[0]->id;
                            
                            $updatedSub = $stripe->subscriptions->update($user->stripe_subscription_id, [
                                'metadata' => [
                                    'type'                  => 'membership',
                                    'membership_package_id' => $package->id,
                                    'user_id'               => $user->id,
                                ],
                                'items' => [
                                    [
                                        'id' => $subItemId,
                                        'price_data' => [
                                            'currency' => 'usd',
                                            'product_data' => [
                                                'name' => $package->title ?: $package->name,
                                                'description' => $package->short_description ?? 'Membership Package Subscription Update',
                                            ],
                                            'unit_amount' => intval($package->price * 100),
                                            'recurring' => [
                                                'interval' => 'day',
                                                'interval_count' => $package->validity_days ?: 30,
                                            ]
                                        ]
                                    ]
                                ],
                                'proration_behavior' => 'always_invoice',
                            ]);

                            $periodStart = $updatedSub->current_period_start ? \Carbon\Carbon::createFromTimestamp($updatedSub->current_period_start) : now();
                            $periodEnd = $updatedSub->current_period_end ? \Carbon\Carbon::createFromTimestamp($updatedSub->current_period_end) : now()->addDays($package->validity_days ?: 30);

                            Purchase::where('user_id', $user->id)
                                ->where('purchase_type', 'membership')
                                ->where('order_status', 'active')
                                ->update(['order_status' => 'completed']);

                            $purchase = Purchase::create([
                                'user_id'               => $user->id,
                                'purchase_type'         => 'membership',
                                'membership_package_id' => $package->id,
                                'order_id'              => Purchase::generateUniqueOrderId('membership'),
                                'amount'                => $package->price,
                                'price_regular'         => $package->price,
                                'price_purchased'       => $package->price,
                                'discount_amount'       => 0.00,
                                'discount_percentage'   => 0.00,
                                'payment_status'        => 'paid',
                                'order_status'          => 'active',
                                'payment_method'        => 'Stripe Swap',
                                'expires_at'            => $periodEnd,
                            ]);

                            $user->update([
                                'stripe_subscription_status'               => $updatedSub->status,
                                'stripe_subscription_period_start'         => $periodStart,
                                'stripe_subscription_period_end'           => $periodEnd,
                                'stripe_next_renewal_at'                   => $periodEnd,
                                'stripe_subscription_cancel_at_period_end' => (bool) $updatedSub->cancel_at_period_end,
                                'membership_package_id'                    => $package->id,
                            ]);

                            $msg = ($package->price > $activeMembership->price)
                                ? 'Membership upgraded successfully.'
                                : 'Membership downgraded successfully.';

                            return $this->successResponse([
                                'redirect_url' => env('FRONTEND_PAYMENT_SUCCESS_URL', 'https://gihqs.vercel.app/payment/success'),
                                'session_id'   => null,
                                'purchase_id'  => $purchase->id,
                                'order_id'     => $purchase->order_id,
                                'package'      => [
                                    'id'    => $package->id,
                                    'name'  => $package->name,
                                    'price' => (float) $package->price,
                                ],
                            ], $msg, 200);
                        }
                    } catch (Throwable $swapEx) {
                        Log::error('Stripe swap subscription failed: ' . $swapEx->getMessage());
                    }
                }
            }

            // If the package is free ($0.00), activate immediately without Stripe
            if (floatval($package->price) <= 0) {
                $purchase = Purchase::create([
                    'user_id'               => $user->id,
                    'purchase_type'         => 'membership',
                    'membership_package_id' => $package->id,
                    'amount'                => 0.00,
                    'price_regular'         => 0.00,
                    'price_purchased'       => 0.00,
                    'discount_amount'       => 0.00,
                    'discount_percentage'   => 0.00,
                    'payment_status'        => 'paid',
                    'order_status'          => 'active',
                    'payment_method'        => 'Free',
                    'expires_at'            => $package->validity_days ? now()->addDays($package->validity_days) : null,
                ]);


                $redirectUrl = env('FRONTEND_PAYMENT_SUCCESS_URL', 'https://gihqs.vercel.app/payment/success');

                $response = [
                    'redirect_url' => $redirectUrl,
                    'session_id'   => null,
                    'purchase_id'  => $purchase->id,
                    'order_id'     => $purchase->order_id,
                    'package'      => [
                        'id'    => $package->id,
                        'name'  => $package->name,
                        'price' => 0.00,
                    ],
                ];

                return $this->successResponse($response, 'Membership activated successfully.', 200);
            }

            // Set Stripe Secret Key
            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                return $this->errorResponse([], 'Stripe secret key configuration is missing.', 500);
            }

            Stripe::setApiKey($stripeSecret);

            // Create or retrieve Stripe Customer ID
            if (empty($user->stripe_customer_id)) {
                try {
                    $stripe = new \Stripe\StripeClient($stripeSecret);
                    $customer = $stripe->customers->create([
                        'email' => $user->email,
                        'name'  => $user->full_name ?: trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                        'metadata' => [
                            'user_id' => $user->id,
                        ],
                    ]);
                    $user->update(['stripe_customer_id' => $customer->id]);
                } catch (Throwable $customerEx) {
                    return $this->errorResponse([], 'Failed to create Stripe customer: ' . $customerEx->getMessage(), 500);
                }
            }

            // 1. Create a pending purchase log in the database
            $purchase = Purchase::create([
                'user_id'               => $user->id,
                'purchase_type'         => 'membership',
                'membership_package_id' => $package->id,
                'amount'                => $package->price,
                'price_regular'         => $package->price,
                'price_purchased'       => $package->price,
                'discount_amount'       => 0.00,
                'discount_percentage'   => 0.00,
                'payment_status'        => 'pending',
            ]);

            // 2. Generate callback URLs
            $successUrl = route('membership.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl  = URL::signedRoute('membership.checkout.cancel', ['purchase_id' => $purchase->id]);

            // 3. Create Stripe Checkout Session in subscription mode
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'customer'             => $user->stripe_customer_id,
                'line_items' => [[
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name'        => $package->title ?: $package->name,
                            'description' => $package->short_description ?? 'Membership Package Subscription',
                        ],
                        'unit_amount' => intval($package->price * 100),
                        'recurring'   => [
                            'interval'       => 'day',
                            'interval_count' => $package->validity_days ?: 30,
                        ]
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'subscription',
                'success_url' => $successUrl,
                'cancel_url'  => $cancelUrl,
                'subscription_data' => [
                    'metadata' => [
                        'type'                  => 'membership',
                        'membership_package_id' => $package->id,
                        'user_id'               => $user->id,
                        'purchase_id'           => $purchase->id,
                    ],
                ],
                'metadata'    => [
                    'type'                  => 'membership',
                    'membership_package_id' => $package->id,
                    'user_id'               => $user->id,
                    'purchase_id'           => $purchase->id,
                ],
            ]);

            // 4. Update the purchase log with the generated Stripe Session ID
            $purchase->update([
                'stripe_session_id' => $session->id,
            ]);

            $response = [
                'redirect_url' => $session->url,
                'session_id'   => $session->id,
                'purchase_id'  => $purchase->id,
                'order_id'     => $purchase->order_id,
                'package'      => [
                    'id'    => $package->id,
                    'name'  => $package->name,
                    'price' => (float) $package->price,
                ],
            ];

            return $this->successResponse($response, 'Stripe checkout session created successfully for membership package.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to initiate membership checkout: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Handle Stripe Membership Checkout Success Callback.
     *
     * GET /api/membership/checkout/success
     */
    public function membershipSuccess(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');
            if (empty($sessionId)) {
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            Stripe::setApiKey($stripeSecret);

            // Retrieve Session details from Stripe
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (!$session || $session->payment_status !== 'paid') {
                $purchase = Purchase::where('stripe_session_id', $sessionId)->first();
                if ($purchase) {
                    $purchase->update([
                        'payment_status' => 'unpaid',
                        'order_status'   => 'cancelled',
                    ]);
                }
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            // Find the pending purchase record and mark it as paid
            $purchase = Purchase::where('stripe_session_id', $sessionId)
                ->where('payment_status', 'pending')
                ->first();

            if ($purchase) {
                $stripe = new \Stripe\StripeClient($stripeSecret);
                $subscriptionId = $session->subscription;
                
                $periodStart = null;
                $periodEnd = null;
                $subStatus = 'active';
                $cancelAtPeriodEnd = false;

                if ($subscriptionId) {
                    try {
                        $sub = $stripe->subscriptions->retrieve($subscriptionId);
                        
                        $periodStartVal = $sub->current_period_start ?? ($sub->items->data[0]->current_period_start ?? null);
                        $periodEndVal = $sub->current_period_end ?? ($sub->items->data[0]->current_period_end ?? null);
                        
                        $periodStart = $periodStartVal ? \Carbon\Carbon::createFromTimestamp($periodStartVal) : null;
                        $periodEnd = $periodEndVal ? \Carbon\Carbon::createFromTimestamp($periodEndVal) : null;
                        
                        $subStatus = $sub->status;
                        $cancelAtPeriodEnd = (bool) $sub->cancel_at_period_end;
                    } catch (Throwable $e) {
                        Log::error('Stripe Retrieve Subscription Error: ' . $e->getMessage());
                    }
                }

                $paymentIntentId = $session->payment_intent;
                if (empty($paymentIntentId) && $subscriptionId && isset($sub) && !empty($sub->latest_invoice)) {
                    try {
                        $invoice = $stripe->invoices->retrieve($sub->latest_invoice);
                        $paymentIntentId = $invoice->payment_intent;
                    } catch (Throwable $e) {
                        Log::error('Stripe Retrieve Latest Invoice Error: ' . $e->getMessage());
                    }
                }

                $paymentMethod = $this->getPaymentMethodFromStripe($session);
                $package = MembershipPackage::withTrashed()->find($purchase->membership_package_id);
                $expiresAt = $periodEnd ?: (($package && $package->validity_days) ? now()->addDays($package->validity_days) : null);

                $purchase->update([
                    'payment_status' => 'paid',
                    'order_status'   => 'active',
                    'payment_method' => $paymentMethod,
                    'expires_at'     => $expiresAt,
                    'stripe_payment_intent_id' => $paymentIntentId,
                ]);

                // Update User Stripe Fields
                $user = User::find($purchase->user_id);
                if ($user) {
                    $user->update([
                        'stripe_customer_id'                       => $session->customer,
                        'stripe_subscription_id'                   => $subscriptionId,
                        'stripe_subscription_status'               => $subStatus,
                        'stripe_subscription_period_start'         => $periodStart ?? now(),
                        'stripe_subscription_period_end'           => $periodEnd ?? $expiresAt,
                        'stripe_next_renewal_at'                   => $periodEnd ?? $expiresAt,
                        'stripe_subscription_cancel_at_period_end' => $cancelAtPeriodEnd,
                        'membership_package_id'                    => $purchase->membership_package_id,
                    ]);
                }
            }

            $redirectUrl = env('FRONTEND_PAYMENT_SUCCESS_URL', 'https://gihqs.vercel.app/payment/success');
            return redirect()->away($redirectUrl);

        } catch (Throwable $th) {
            return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
        }
    }

    /**
     * Handle Stripe Membership Checkout Cancel Callback.
     *
     * GET /api/membership/checkout/cancel
     */
    public function membershipCancel(Request $request)
    {
        try {
            $purchaseId = $request->query('purchase_id');
            if ($purchaseId) {
                $purchase = Purchase::find($purchaseId);
                if ($purchase && $purchase->payment_status === 'pending') {
                    $purchase->update([
                        'payment_status' => 'cancelled',
                        'order_status'   => 'cancelled',
                    ]);
                }
            }
        } catch (Throwable $th) {
            // Log error or ignore quietly
        }

        return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
    }

    /**
     * Handle Stripe Advisory Checkout Success Callback.
     *
     * GET /api/advisory/checkout/success
     */
    public function advisorySuccess(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');
            if (empty($sessionId)) {
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            Stripe::setApiKey($stripeSecret);

            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (!$session || $session->payment_status !== 'paid') {
                $advisoryReq = AdvisoryRequest::where('stripe_session_id', $sessionId)->first();
                if ($advisoryReq && $advisoryReq->payment_status === 'pending') {
                    $advisoryReq->update(['payment_status' => 'cancelled']);
                }
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            $advisoryRequestId = $session->metadata->advisory_request_id ?? null;
            $advisoryReq = null;

            if ($advisoryRequestId) {
                $advisoryReq = AdvisoryRequest::find($advisoryRequestId);
            }

            if (!$advisoryReq) {
                $advisoryReq = AdvisoryRequest::where('stripe_session_id', $sessionId)->first();
            }

            if ($advisoryReq) {
                if ($advisoryReq->payment_status !== 'paid') {
                    $validityDays = $advisoryReq->validity_days ?: 30;
                    $advisoryReq->update([
                        'payment_status'           => 'paid',
                        'status'                   => 'accepted',
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'payment_date'             => now(),
                        'expires_at'               => now()->addDays($validityDays),
                    ]);
                }

                if ($advisoryReq->user_id) {
                    Purchase::firstOrCreate(
                        ['stripe_session_id' => $sessionId],
                        [
                            'user_id'             => $advisoryReq->user_id,
                            'purchase_type'       => 'advisory',
                            'advisory_request_id' => $advisoryReq->id,
                            'amount'              => $advisoryReq->payment_amount,
                            'price_regular'       => $advisoryReq->payment_amount,
                            'price_purchased'     => $advisoryReq->payment_amount,
                            'payment_status'      => 'paid',
                            'order_status'        => 'completed',
                            'payment_method'      => $this->getPaymentMethodFromStripe($session),
                            'stripe_payment_intent_id' => $session->payment_intent,
                        ]
                    );
                }
            }

            $successUrl = env('FRONTEND_PAYMENT_SUCCESS_URL', 'https://gihqs.vercel.app/payment/success');
            if (str_contains($successUrl, '?')) {
                $redirectUrl = $successUrl . '&advisory_id=' . ($advisoryReq ? $advisoryReq->id : '');
            } else {
                $redirectUrl = $successUrl . '?payment_success=true&advisory_id=' . ($advisoryReq ? $advisoryReq->id : '');
            }
            return redirect()->away($redirectUrl);

        } catch (Throwable $th) {
            return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
        }
    }

    /**
     * Handle Stripe Advisory Checkout Cancel Callback.
     *
     * GET /api/advisory/checkout/cancel
     */
    public function advisoryCancel(Request $request)
    {
        try {
            $advisoryRequestId = $request->query('advisory_request_id');
            if ($advisoryRequestId) {
                $advisoryReq = AdvisoryRequest::find($advisoryRequestId);
                if ($advisoryReq && $advisoryReq->payment_status === 'pending') {
                    $advisoryReq->update([
                        'payment_status' => 'cancelled',
                    ]);
                }
            }
        } catch (Throwable $th) {
            // Ignore error during cancellation redirect
        }

        return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
    }

    /**
     * Handle Stripe Accreditation Checkout Success Callback.
     *
     * GET /api/accreditation/checkout/success
     */
    public function accreditationSuccess(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');
            if (empty($sessionId)) {
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            Stripe::setApiKey($stripeSecret);

            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (!$session || $session->payment_status !== 'paid') {
                $app = AccreditationApplication::where('stripe_session_id', $sessionId)->first();
                if ($app && $app->payment_status === 'pending') {
                    $app->update(['payment_status' => 'cancelled']);
                }
                return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
            }

            $applicationId = $session->metadata->accreditation_application_id ?? null;
            $app = null;

            if ($applicationId) {
                $app = AccreditationApplication::find($applicationId);
            }

            if (!$app) {
                $app = AccreditationApplication::where('stripe_session_id', $sessionId)->first();
            }

            if ($app) {
                if ($app->payment_status !== 'paid') {
                    $validityDays = $app->validity_days ?: 365;
                    $app->update([
                        'payment_status'           => 'paid',
                        'status'                   => 'valid',
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'payment_date'             => now(),
                        'issued_at'                => now(),
                        'expires_at'               => now()->addDays($validityDays),
                    ]);

                    // Generate the PDF certificate now that it is valid and paid
                    \App\Services\AccreditationCertificateService::generatePdf($app);

                    // Send status email to client with certificate attached
                    try {
                        $actionLink = env('FRONTEND_URL', 'https://gihqs.vercel.app') . '/dashboard';
                        $mail = new \App\Mail\AccreditationStatusMail(
                            $app,
                            'valid',
                            'Thank you for your payment. Your accreditation has been approved and your certificate is attached to this email.',
                            $actionLink
                        );
                        Mail::to($app->email_address)->send($mail);

                        // Log email to database
                        $this->logEmail(
                            $app->user_id,
                            $app->email_address,
                            'user',
                            $mail->envelope()->subject,
                            'accreditation_valid',
                            $app
                        );
                    } catch (\Throwable $mailEx) {
                        Log::error('Failed to send accreditation status email on checkout success: ' . $mailEx->getMessage());
                    }
                }

                if ($app->user_id) {
                    Purchase::firstOrCreate(
                        ['stripe_session_id' => $sessionId],
                        [
                            'user_id'                      => $app->user_id,
                            'purchase_type'                => 'accreditation',
                            'accreditation_application_id' => $app->id,
                            'amount'                       => $app->payment_amount,
                            'price_regular'                => $app->payment_amount,
                            'price_purchased'              => $app->payment_amount,
                            'payment_status'               => 'paid',
                            'order_status'                 => 'completed',
                            'payment_method'               => $this->getPaymentMethodFromStripe($session),
                            'stripe_payment_intent_id' => $session->payment_intent,
                        ]
                    );
                }
            }

            $successUrl = env('FRONTEND_PAYMENT_SUCCESS_URL', 'https://gihqs.vercel.app/payment/success');
            if (str_contains($successUrl, '?')) {
                $redirectUrl = $successUrl . '&accreditation_id=' . ($app ? $app->id : '');
            } else {
                $redirectUrl = $successUrl . '?payment_success=true&accreditation_id=' . ($app ? $app->id : '');
            }
            return redirect()->away($redirectUrl);

        } catch (Throwable $th) {
            Log::error('Accreditation checkout success callback error: ' . $th->getMessage());
            return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
        }
    }

    /**
     * Handle Stripe Accreditation Checkout Cancel Callback.
     *
     * GET /api/accreditation/checkout/cancel
     */
    public function accreditationCancel(Request $request)
    {
        try {
            $applicationId = $request->query('accreditation_application_id');
            if ($applicationId) {
                $app = AccreditationApplication::find($applicationId);
                if ($app && $app->payment_status === 'pending') {
                    $app->update([
                        'payment_status' => 'cancelled',
                    ]);
                }
            }
        } catch (Throwable $th) {
            // Ignore error during cancellation redirect
        }

        return redirect()->away(env('FRONTEND_PAYMENT_FAIL_URL', 'https://gihqs.vercel.app/payment/cancel'));
    }

    /**
     * Retrieve payment method details from Stripe.
     */
    private function getPaymentMethodFromStripe($session)
    {
        try {
            if ($session && $session->payment_intent) {
                $stripeSecret = config('services.stripe.secret');
                Stripe::setApiKey($stripeSecret);
                
                $paymentIntent = \Stripe\PaymentIntent::retrieve([
                    'id' => $session->payment_intent,
                    'expand' => ['payment_method']
                ]);

                if ($paymentIntent && $paymentIntent->payment_method && $paymentIntent->payment_method->type === 'card') {
                    $card = $paymentIntent->payment_method->card;
                    $brand = ucfirst($card->brand);
                    $last4 = $card->last4;
                    return $brand . ' ••' . $last4;
                }
            }
        } catch (Throwable $th) {
            // Ignore error and fall back to general string
        }
        return 'Card';
    }

    /**
     * Fetch user's order and payment history.
     *
     * GET /api/profile/orders
     */
    public function orderHistory()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not authenticated.', 401);
            }

            // Retrieve Purchases
            $purchases = Purchase::where('user_id', $user->id)
                ->with(['catalogue', 'membershipPackage', 'advisoryRequest', 'accreditationApplication'])
                ->orderBy('created_at', 'desc')
                ->get();

            $history = [];

            foreach ($purchases as $p) {
                if ($p->purchase_type === 'catalogue') {
                    $itemName = $p->catalogue->title ?? 'Catalogue Item';
                } elseif ($p->purchase_type === 'advisory') {
                    $itemName = 'Advisory: ' . ($p->advisoryRequest->service_of_interest ?? 'Consultation');
                } elseif ($p->purchase_type === 'accreditation') {
                    $itemName = 'Accreditation: ' . ($p->accreditationApplication->program_name ?? 'Program');
                } else {
                    $itemName = ($p->membershipPackage->title ?? 'Membership Package') . ' — Subscription';
                }

                $history[] = [
                    'id'                  => $p->id,
                    'order_id'            => '#' . $p->order_id,
                    'item'                => $itemName,
                    'date'                => \Carbon\Carbon::parse($p->created_at)->format('M d, Y'),
                    'amount'              => '$' . number_format($p->amount, 2),
                    'raw_amount'          => (float) $p->amount,
                    'price_regular'       => (float) ($p->price_regular ?? $p->amount),
                    'price_purchased'     => (float) ($p->price_purchased ?? $p->amount),
                    'discount_amount'     => (float) ($p->discount_amount ?? 0.00),
                    'discount_percentage' => (float) ($p->discount_percentage ?? 0.00),
                    'method'              => $p->payment_method ?? 'Card',
                    'status'              => ucfirst($p->payment_status),
                    'type'                => $p->purchase_type,
                    'invoice_url'         => route('membership.checkout.invoice', ['orderId' => $p->order_id]),
                ];
            }

            if (empty($history)) {
                return $this->errorResponse([], 'No order history found.', 404);
            }

            $response = [
                'order_history' => $history,
            ];

            return $this->successResponse($response, 'Order history retrieved successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch order history: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Download Invoice as PDF.
     *
     * GET /api/profile/orders/{orderId}/invoice
     */
    public function downloadInvoice($orderIdOrId)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not authenticated.', 401);
            }

            // Remove '#' if it is passed in the URL (e.g. #ORD-CAT-XXXXXXXX or #3)
            $identifier = str_replace('#', '', $orderIdOrId);

            $query = Purchase::where('user_id', $user->id)
                ->with(['catalogue', 'membershipPackage', 'advisoryRequest', 'accreditationApplication']);

            if (is_numeric($identifier)) {
                $purchase = $query->where('id', $identifier)->first();
            } else {
                $purchase = $query->where('order_id', $identifier)->first();
            }

            if (!$purchase) {
                return $this->errorResponse([], 'Invoice not found or unauthorized access.', 404);
            }

            $itemName = '';
            $itemDescription = '';

            if ($purchase->purchase_type === 'catalogue') {
                if ($purchase->catalogue) {
                    $itemName = $purchase->catalogue->title;
                    $itemDescription = $purchase->catalogue->short_description ?? 'Catalogue Purchase';
                }
            } elseif ($purchase->purchase_type === 'membership') {
                if ($purchase->membershipPackage) {
                    $itemName = $purchase->membershipPackage->title;
                    $itemDescription = $purchase->membershipPackage->short_description ?? 'Membership Subscription';
                }
            } elseif ($purchase->purchase_type === 'advisory') {
                if ($purchase->advisoryRequest) {
                    $itemName = 'Advisory Service';
                    $itemDescription = 'Advisory Consultation: ' . ($purchase->advisoryRequest->service_of_interest ?? 'Consultation') . ' [Ref: ' . ($purchase->advisoryRequest->reference_number ?? '') . ']';
                } else {
                    $itemName = 'Advisory Service';
                    $itemDescription = 'Advisory Consultation';
                }
            } elseif ($purchase->purchase_type === 'accreditation') {
                if ($purchase->accreditationApplication) {
                    $itemName = 'Accreditation Service';
                    $itemDescription = 'Accreditation Program: ' . ($purchase->accreditationApplication->program_name ?? 'Program') . ' [Ref: ' . ($purchase->accreditationApplication->reference_number ?? '') . ']';
                } else {
                    $itemName = 'Accreditation Service';
                    $itemDescription = 'Accreditation Program';
                }
            }

            $data = [
                'purchase'         => $purchase,
                'user'             => $user,
                'item_name'        => $itemName,
                'item_description' => $itemDescription,
                'type'             => $purchase->purchase_type,
            ];

            $pdf = Pdf::loadView('invoices.invoice', $data);

            return $pdf->download('invoice-' . $purchase->order_id . '.pdf');

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to generate invoice PDF: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get user's current Stripe subscription details.
     *
     * GET /api/profile/subscription
     */
    public function getSubscriptionDetails(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not authenticated.', 401);
            }

            if (empty($user->stripe_subscription_id)) {
                $response = [
                    'has_active_subscription' => false,
                    'subscription'            => null,
                ];
                return $this->successResponse($response, 'No active Stripe subscription found.', 200);
            }

            $package = $user->membershipPackage;
            $activePurchase = $user->activeMembershipPurchase;

            // Fallback to active purchase dates if Stripe dates are null
            $periodStart = $user->stripe_subscription_period_start 
                ?? ($activePurchase ? $activePurchase->created_at : null);

            $periodEnd = $user->stripe_subscription_period_end 
                ?? ($activePurchase ? $activePurchase->expires_at : null);

            $nextRenewalDate = $user->stripe_next_renewal_at 
                ?? ($activePurchase ? $activePurchase->expires_at : null);

            $response = [
                'has_active_subscription' => in_array($user->stripe_subscription_status, ['active', 'trialing']),
                'subscription' => [
                    'subscription_id'        => $user->stripe_subscription_id,
                    'status'                 => $user->stripe_subscription_status,
                    'period_start'           => $periodStart ? \Carbon\Carbon::parse($periodStart)->format('M d, Y, h:i A') : null,
                    'period_end'             => $periodEnd ? \Carbon\Carbon::parse($periodEnd)->format('M d, Y, h:i A') : null,
                    'next_renewal_date'      => $nextRenewalDate ? \Carbon\Carbon::parse($nextRenewalDate)->format('M d, Y, h:i A') : null,
                    'cancel_at_period_end'   => (bool) $user->stripe_subscription_cancel_at_period_end,
                    'package'                => $package ? [
                        'id'    => $package->id,
                        'name'  => $package->name,
                        'title' => $package->title,
                        'price' => (float) $package->price,
                    ] : null,
                ]
            ];

            return $this->successResponse($response, 'Subscription details fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch subscription details: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Cancel user's auto-renewal at period end.
     *
     * POST /api/profile/subscription/cancel
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not authenticated.', 401);
            }

            if (empty($user->stripe_subscription_id)) {
                return $this->errorResponse([], 'No active Stripe subscription found to cancel.', 400);
            }

            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                return $this->errorResponse([], 'Stripe secret key configuration is missing.', 500);
            }

            $stripe = new \Stripe\StripeClient($stripeSecret);
            $stripe->subscriptions->update($user->stripe_subscription_id, [
                'cancel_at_period_end' => true
            ]);

            $user->update([
                'stripe_subscription_cancel_at_period_end' => true,
            ]);

            $response = [
                'stripe_subscription_id' => $user->stripe_subscription_id,
                'cancel_at_period_end'   => true,
            ];

            return $this->successResponse($response, 'Auto-renewal cancelled successfully. Access will end at the end of the billing period.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to cancel subscription: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Request refund for a purchase.
     *
     * POST /api/profile/orders/{id}/request-refund
     */
    public function requestRefund(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
            ]);

            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not authenticated.', 401);
            }

            $purchase = Purchase::where('user_id', $user->id)->findOrFail($id);

            if (!in_array($purchase->payment_status, ['paid', 'partially_refunded'])) {
                return $this->errorResponse([], 'Only paid or partially refunded orders are eligible for refund requests.', 400);
            }

            if ($purchase->refund_request_status === 'pending') {
                return $this->errorResponse([], 'A refund request is already pending for this order.', 400);
            }

            $purchase->update([
                'refund_request_status' => 'pending',
                'refund_request_reason' => $request->reason,
                'refund_requested_at'   => now(),
            ]);

            return $this->successResponse([], 'Your refund request has been submitted successfully and is under review.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to request refund: ' . $th->getMessage(), 500);
        }
    }
}
