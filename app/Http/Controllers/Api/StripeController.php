<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Cart;
use App\Models\Purchase;
use App\Models\AdvisoryRequest;
use App\Models\PurchaseRefund;
use Stripe\StripeClient;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    use ApiResponse;

    public function intent(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string|max:50',
        ]);

        $user = Auth::guard('api')->user();

        $order = Order::find($request->order_id);
        $orderTotal = $order->total;

        $stripe = new StripeClient(config('services.stripe.secret'));
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $orderTotal * 100,
            'currency' => 'usd',
            'metadata' => [
                'order_id' => $order->id,
                'user_id'  => $user->id,
            ],
        ]);
        $order->transaction_id = $paymentIntent->id;
        $order->payment_method = $request->payment_method;
        $order->payment_status = 'pending';
        $order->save();
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'client_intent' => $paymentIntent->client_secret,
        ], 200);
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        // Without a configured secret this endpoint used to accept any unsigned
        // JSON body, so anyone could POST a forged checkout.session.completed and
        // have a purchase marked paid. A missing secret is now a hard failure.
        if (!$secret) {
            Log::error('Stripe Webhook Error: STRIPE_WEBHOOK_SECRET is not configured; refusing unsigned payload.');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        try {
            $event = \Stripe\Webhook::constructEvent($payload, (string) $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Signature Verification Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $object = $event->data->object ?? null;

        if (!$object) {
            return response()->json(['ok' => true]);
        }

        switch ($event->type ?? '') {
            case 'checkout.session.completed':
                $session = $object;
                $metadata = $session->metadata ?? null;
                $type = $metadata->type ?? null;

                if ($type === 'advisory_request' || isset($metadata->advisory_request_id)) {
                    $advisoryReqId = $metadata->advisory_request_id ?? null;
                    $advisoryReq = $advisoryReqId ? AdvisoryRequest::find($advisoryReqId) : AdvisoryRequest::where('stripe_session_id', $session->id)->first();

                    if ($advisoryReq && $advisoryReq->payment_status !== 'paid') {
                        $validityDays = $advisoryReq->validity_days ?: 30;
                        $advisoryReq->update([
                            'payment_status'           => 'paid',
                            'status'                   => 'accepted',
                            'stripe_payment_intent_id' => $session->payment_intent ?? ($session->id ?? null),
                            'payment_date'             => now(),
                            'expires_at'               => now()->addDays($validityDays),
                        ]);
                        Log::info("Advisory Request #{$advisoryReq->id} marked as Paid via Webhook.");
                    }

                    if ($advisoryReq && $advisoryReq->user_id) {
                        Purchase::firstOrCreate(
                            ['stripe_session_id' => $session->id],
                            [
                                'user_id'             => $advisoryReq->user_id,
                                'purchase_type'       => 'advisory',
                                'advisory_request_id' => $advisoryReq->id,
                                'amount'              => $advisoryReq->payment_amount,
                                'price_regular'       => $advisoryReq->payment_amount,
                                'price_purchased'     => $advisoryReq->payment_amount,
                                'payment_status'      => 'paid',
                                'order_status'        => 'completed',
                                'payment_method'      => $session->payment_method_types[0] ?? 'Card',
                                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                            ]
                        );
                    }
                } elseif ($type === 'membership') {
                    $stripeSecret = config('services.stripe.secret');
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
                        } catch (\Exception $e) {
                            Log::error('Stripe Webhook Retrieve Sub Error: ' . $e->getMessage());
                        }
                    }

                    $purchaseId = $metadata->purchase_id ?? null;
                    $purchase = $purchaseId ? Purchase::find($purchaseId) : Purchase::where('stripe_session_id', $session->id)->first();
                    
                    $packageId = $metadata->membership_package_id ?? ($purchase ? $purchase->membership_package_id : null);
                    $package = $packageId ? \App\Models\MembershipPackage::withTrashed()->find($packageId) : null;
                    
                    $finalPeriodStart = $periodStart ?? now();
                    $finalPeriodEnd = $periodEnd ?? (($package && $package->validity_days) ? now()->addDays($package->validity_days) : null);

                    if ($purchase && $purchase->payment_status !== 'paid') {
                        $purchase->update([
                            'payment_status' => 'paid',
                            'order_status'   => 'active',
                            'expires_at'     => $finalPeriodEnd,
                            'stripe_payment_intent_id' => $session->payment_intent ?? null,
                        ]);
                    }

                    $userId = $metadata->user_id ?? ($purchase ? $purchase->user_id : null);
                    
                    if ($userId) {
                        $user = \App\Models\User::find($userId);
                        if ($user) {
                            $user->update([
                                'stripe_customer_id'                       => $session->customer,
                                'stripe_subscription_id'                   => $subscriptionId,
                                'stripe_subscription_status'               => $subStatus,
                                'stripe_subscription_period_start'         => $finalPeriodStart,
                                'stripe_subscription_period_end'           => $finalPeriodEnd,
                                'stripe_next_renewal_at'                   => $finalPeriodEnd,
                                'stripe_subscription_cancel_at_period_end' => $cancelAtPeriodEnd,
                                'membership_package_id'                    => $packageId,
                            ]);
                        }
                    }
                } elseif ($type === 'catalogue') {
                    $purchase = Purchase::where('stripe_session_id', $session->id)->first();
                    if ($purchase && $purchase->payment_status !== 'paid') {
                        $purchase->update([
                            'payment_status' => 'paid',
                            'order_status'   => 'completed',
                            'stripe_payment_intent_id' => $session->payment_intent ?? null,
                        ]);
                    }
                }
                break;

            case 'invoice.payment_succeeded':
                $invoice = $object;
                if ($invoice->subscription) {
                    $stripeSecret = config('services.stripe.secret');
                    $stripe = new \Stripe\StripeClient($stripeSecret);
                    
                    try {
                        $sub = $stripe->subscriptions->retrieve($invoice->subscription);
                        
                        // Verify this is a membership subscription
                        if (($sub->metadata->type ?? '') === 'membership' || isset($sub->metadata->membership_package_id)) {
                            $packageId = $sub->metadata->membership_package_id;
                            $userId = $sub->metadata->user_id;
                            
                            $user = \App\Models\User::find($userId) ?? \App\Models\User::where('stripe_subscription_id', $invoice->subscription)->first();
                            
                            if ($user) {
                                $periodStartVal = $sub->current_period_start ?? ($sub->items->data[0]->current_period_start ?? null);
                                $periodEndVal = $sub->current_period_end ?? ($sub->items->data[0]->current_period_end ?? null);
                                
                                $periodStart = $periodStartVal ? \Carbon\Carbon::createFromTimestamp($periodStartVal) : null;
                                $periodEnd = $periodEndVal ? \Carbon\Carbon::createFromTimestamp($periodEndVal) : null;
                                
                                $subStatus = $sub->status;
                                $cancelAtPeriodEnd = (bool) $sub->cancel_at_period_end;
                                
                                $package = \App\Models\MembershipPackage::withTrashed()->find($packageId);
                                $finalPeriodStart = $periodStart ?? now();
                                $finalPeriodEnd = $periodEnd ?? (($package && $package->validity_days) ? now()->addDays($package->validity_days) : null);

                                $user->update([
                                    'stripe_subscription_status'               => $subStatus,
                                    'stripe_subscription_period_start'         => $finalPeriodStart,
                                    'stripe_subscription_period_end'           => $finalPeriodEnd,
                                    'stripe_next_renewal_at'                   => $finalPeriodEnd,
                                    'stripe_subscription_cancel_at_period_end' => $cancelAtPeriodEnd,
                                    'membership_package_id'                    => $packageId,
                                ]);
                                
                                // Prevent duplicate logs for the same invoice
                                $exists = Purchase::where('stripe_session_id', $invoice->id)->exists();
                                if (!$exists) {
                                    Purchase::create([
                                        'user_id'               => $user->id,
                                        'purchase_type'         => 'membership',
                                        'membership_package_id' => $packageId,
                                        'order_id'              => Purchase::generateUniqueOrderId('membership'),
                                        'stripe_session_id'     => $invoice->id, // Store Invoice ID as session_id
                                        'stripe_payment_intent_id' => $invoice->payment_intent,
                                        'amount'                => $invoice->amount_paid / 100,
                                        'price_regular'         => $package ? $package->price : ($invoice->amount_paid / 100),
                                        'price_purchased'       => $invoice->amount_paid / 100,
                                        'payment_status'        => 'paid',
                                        'order_status'          => 'active',
                                        'payment_method'        => 'Stripe Auto-Renew',
                                        'expires_at'            => $finalPeriodEnd,
                                    ]);
                                    
                                    Log::info("Membership auto-renewal logged successfully for User ID: {$user->id}, Subscription: {$invoice->subscription}");
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Stripe Webhook invoice.payment_succeeded Error: ' . $e->getMessage());
                    }
                }
                break;

            case 'invoice.payment_failed':
                $invoice = $object;
                if ($invoice->subscription) {
                    $user = \App\Models\User::where('stripe_subscription_id', $invoice->subscription)->first();
                    if ($user) {
                        $stripeSecret = config('services.stripe.secret');
                        $stripe = new \Stripe\StripeClient($stripeSecret);
                        try {
                            $sub = $stripe->subscriptions->retrieve($invoice->subscription);
                            $user->update([
                                'stripe_subscription_status' => $sub->status, // e.g. past_due, unpaid
                            ]);
                            Log::warning("Stripe Sub Renewal Payment Failed for User ID: {$user->id}, Status: {$sub->status}");
                        } catch (\Exception $e) {
                            Log::error('Stripe Webhook invoice.payment_failed Error: ' . $e->getMessage());
                        }
                    }
                }
                break;

            case 'customer.subscription.updated':
                $subscription = $object;
                if (($subscription->metadata->type ?? '') === 'membership' || isset($subscription->metadata->membership_package_id)) {
                    $user = \App\Models\User::where('stripe_subscription_id', $subscription->id)->first();
                    if ($user) {
                        $periodStartVal = $subscription->current_period_start ?? ($subscription->items->data[0]->current_period_start ?? null);
                        $periodEndVal = $subscription->current_period_end ?? ($subscription->items->data[0]->current_period_end ?? null);
                        
                        $periodStart = $periodStartVal ? \Carbon\Carbon::createFromTimestamp($periodStartVal) : null;
                        $periodEnd = $periodEndVal ? \Carbon\Carbon::createFromTimestamp($periodEndVal) : null;
                        
                        $subStatus = $subscription->status;
                        $cancelAtPeriodEnd = (bool) $subscription->cancel_at_period_end;
                        
                        $packageId = $subscription->metadata->membership_package_id ?? $user->membership_package_id;
                        $package = \App\Models\MembershipPackage::withTrashed()->find($packageId);
                        $finalPeriodStart = $periodStart ?? now();
                        $finalPeriodEnd = $periodEnd ?? (($package && $package->validity_days) ? now()->addDays($package->validity_days) : null);
                        
                        $user->update([
                            'stripe_subscription_status'               => $subStatus,
                            'stripe_subscription_period_start'         => $finalPeriodStart,
                            'stripe_subscription_period_end'           => $finalPeriodEnd,
                            'stripe_next_renewal_at'                   => $finalPeriodEnd,
                            'stripe_subscription_cancel_at_period_end' => $cancelAtPeriodEnd,
                            'membership_package_id'                    => $packageId,
                        ]);
                        
                        Log::info("Stripe Sub updated via Webhook for User ID: {$user->id}, Status: {$subStatus}, Package ID: {$packageId}");
                    }
                }
                break;

            case 'customer.subscription.deleted':
                $subscription = $object;
                $user = \App\Models\User::where('stripe_subscription_id', $subscription->id)->first();
                if ($user) {
                    $user->update([
                        'stripe_subscription_status'               => 'canceled',
                        'stripe_subscription_id'                   => null,
                        'membership_package_id'                    => null,
                        'stripe_subscription_period_start'         => null,
                        'stripe_subscription_period_end'           => null,
                        'stripe_next_renewal_at'                   => null,
                        'stripe_subscription_cancel_at_period_end' => false,
                    ]);
                    
                    // Assign default Standard package
                    $user->assignDefaultMembership();
                    Log::info("Stripe Sub deleted/expired via Webhook for User ID: {$user->id}. Default package assigned.");
                }
                break;

            case 'payment_intent.succeeded':
                $purchase = Purchase::where('stripe_payment_intent_id', $object->id)->first();
                if ($purchase && $purchase->payment_status !== 'paid') {
                    $purchase->update([
                        'payment_status' => 'paid',
                        'order_status'   => $purchase->purchase_type === 'membership' ? 'active' : 'completed',
                    ]);
                }
                break;

            case 'payment_intent.payment_failed':
                $purchase = Purchase::where('stripe_payment_intent_id', $object->id)->first();
                if ($purchase) {
                    $purchase->update(['payment_status' => 'failed']);
                }
                break;

            case 'charge.refunded':
                $charge = $object;
                
                // Find purchase by payment intent or invoice session ID
                $purchase = null;
                if (!empty($charge->payment_intent)) {
                    $purchase = Purchase::where('stripe_payment_intent_id', $charge->payment_intent)->first();
                }
                if (!$purchase && !empty($charge->invoice)) {
                    $purchase = Purchase::where('stripe_session_id', $charge->invoice)->first();
                }
                
                if ($purchase) {
                    $refundedAmount = floatval($charge->amount_refunded) / 100;
                    
                    $purchase->refunded_amount = $refundedAmount;
                    $isFullyRefunded = (bool) $charge->refunded;
                    
                    $purchase->payment_status = $isFullyRefunded ? 'refunded' : 'partially_refunded';
                    if ($isFullyRefunded) {
                        $purchase->order_status = 'cancelled';
                    }
                    $purchase->save();

                    if ($isFullyRefunded) {
                        if ($purchase->purchase_type === 'membership') {
                            $user = \App\Models\User::find($purchase->user_id);
                            if ($user) {
                                if (!empty($user->stripe_subscription_id)) {
                                    try {
                                        $stripe = app(\Stripe\StripeClient::class);
                                        $stripe->subscriptions->cancel($user->stripe_subscription_id);
                                    } catch (\Exception $ex) {
                                        Log::error("Failed to cancel Stripe subscription during webhook charge.refunded: " . $ex->getMessage());
                                    }
                                }

                                $user->update([
                                    'stripe_subscription_status'               => 'canceled',
                                    'stripe_subscription_id'                   => null,
                                    'membership_package_id'                    => null,
                                    'stripe_subscription_period_start'         => null,
                                    'stripe_subscription_period_end'           => null,
                                    'stripe_next_renewal_at'                   => null,
                                    'stripe_subscription_cancel_at_period_end' => false,
                                ]);

                                $user->assignDefaultMembership();
                            }
                        }
                    }
                    
                    Log::info("Webhook charge.refunded: Updated Purchase ID {$purchase->id} status to {$purchase->payment_status}, refunded amount: {$refundedAmount}");
                    
                    // Sync refund objects from Stripe payload to local database
                    if (isset($charge->refunds->data) && is_array($charge->refunds->data)) {
                        foreach ($charge->refunds->data as $refund) {
                            // Check if this refund is already recorded
                            $exists = PurchaseRefund::where('stripe_refund_id', $refund->id)->exists();
                            if (!$exists) {
                                PurchaseRefund::create([
                                    'purchase_id' => $purchase->id,
                                    'stripe_refund_id' => $refund->id,
                                    'amount' => floatval($refund->amount) / 100,
                                    'reason' => $refund->reason ?? $refund->description ?? 'Refunded via Stripe Dashboard',
                                    'admin_id' => null, // Dashboard refund
                                    'status' => $refund->status ?? 'succeeded',
                                    'created_at' => \Carbon\Carbon::createFromTimestamp($refund->created),
                                ]);
                            } else {
                                // Update status if it changed
                                PurchaseRefund::where('stripe_refund_id', $refund->id)->update([
                                    'status' => $refund->status ?? 'succeeded',
                                ]);
                            }
                        }
                    }
                } else {
                    Log::warning("Webhook charge.refunded: No matching purchase found for Payment Intent: {$charge->payment_intent} or Invoice: {$charge->invoice}");
                }
                break;
        }

        return response()->json(['ok' => true]);
    }
}
