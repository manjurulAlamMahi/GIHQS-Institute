<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User;
use App\Models\MembershipPackage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\PurchaseRefund;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Purchase::with(['user', 'catalogue', 'membershipPackage'])->latest();
            return $this->compileOrdersDatatable($orders);
        }

        return view('backend.layouts.orders.index');
    }

    /**
     * Display a listing of refund requests.
     */
    public function refundRequests(Request $request)
    {
        if ($request->ajax()) {
            $orders = Purchase::with(['user', 'catalogue', 'membershipPackage'])
                ->whereNotNull('refund_request_status')
                ->latest();
            return $this->compileOrdersDatatable($orders);
        }

        return view('backend.layouts.orders.refund_requests');
    }

    /**
     * Compile Orders Datatable JSON response from query.
     */
    private function compileOrdersDatatable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('order_id', function ($row) {
                return '<strong>' . e($row->order_id) . '</strong>';
            })
            ->addColumn('user_info', function ($row) {
                if (!$row->user) {
                    return '<span class="text-muted">N/A</span>';
                }
                $name = $row->user->full_name ?: trim(($row->user->first_name ?? '') . ' ' . ($row->user->last_name ?? ''));
                return e($name) . '<br><small class="text-muted">' . e($row->user->email) . '</small>';
            })
            ->addColumn('item_info', function ($row) {
                if ($row->purchase_type === 'catalogue') {
                    $title = $row->catalogue->title ?? 'Catalogue Item';
                    return '<span class="badge bg-info">Catalogue</span><br>' . e($title);
                } else {
                    $title = $row->membershipPackage->title ?? 'Membership Package';
                    $expiryText = '';
                    if ($row->expires_at) {
                        $expiryText = '<br><small class="text-danger">Expires: ' . $row->expires_at->format('M d, Y') . '</small>';
                    }
                    return '<span class="badge bg-primary">Membership</span><br>' . e($title) . $expiryText;
                }
            })
            ->editColumn('amount', function ($row) {
                $amountText = '$' . number_format($row->amount, 2);
                if ($row->refunded_amount > 0) {
                    $amountText .= '<br><small class="text-danger">Refunded: $' . number_format($row->refunded_amount, 2) . '</small>';
                }
                return $amountText;
            })
            ->editColumn('payment_method', function ($row) {
                return e($row->payment_method ?: 'Card');
            })
            ->editColumn('payment_status', function ($row) {
                $status = $row->payment_status ? strtolower(trim($row->payment_status)) : 'pending';
                $badgeClass = 'bg-secondary';

                if ($status === 'pending') $badgeClass = 'bg-warning text-dark';
                if ($status === 'paid') $badgeClass = 'bg-success';
                if ($status === 'unpaid') $badgeClass = 'bg-danger';
                if ($status === 'cancelled') $badgeClass = 'bg-dark';
                if ($status === 'refunded') $badgeClass = 'bg-danger';
                if ($status === 'partially_refunded') $badgeClass = 'bg-warning text-dark';

                // Display friendly names
                $displayStatus = match ($status) {
                    'cancelled' => 'Cancelled',
                    'partially_refunded' => 'Partially Refunded',
                    default => ucfirst($status)
                };
                
                $badge = '<span class="badge ' . $badgeClass . '">' . $displayStatus . '</span>';
                
                if ($row->refund_request_status === 'pending') {
                    $badge .= '<br><span class="badge bg-danger mt-1">Refund Requested</span>';
                }
                
                return $badge;
            })
            ->editColumn('order_status', function ($row) {
                $status = $row->order_status ? strtolower(trim($row->order_status)) : 'pending';
                $badgeClass = 'bg-secondary';

                if ($status === 'pending') $badgeClass = 'bg-warning text-dark';
                if ($status === 'accepted') $badgeClass = 'bg-info';
                if ($status === 'active') $badgeClass = 'bg-success';
                if ($status === 'cancelled') $badgeClass = 'bg-dark';
                if ($status === 'completed') $badgeClass = 'bg-primary';

                return '<span class="badge ' . $badgeClass . '">' . ucfirst($status) . '</span>';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('M d, Y H:i A') : '-';
            })
            ->addColumn('action', function ($row) {
                $buttons = '<button class="btn btn-sm btn-primary update-status-btn me-1" 
                            data-id="' . $row->id . '" 
                            data-order-id="' . $row->order_id . '" 
                            data-payment-status="' . $row->payment_status . '"
                            data-order-status="' . $row->order_status . '">
                            <i class="ri-edit-2-line"></i> Update
                        </button>';

                if (($row->payment_status === 'paid' || $row->payment_status === 'partially_refunded' || $row->refund_request_status === 'pending') && floatval($row->amount) > 0) {
                    $buttons .= '<button class="btn btn-sm btn-danger refund-btn" 
                                data-id="' . $row->id . '" 
                                data-order-id="' . $row->order_id . '" 
                                data-amount="' . $row->amount . '" 
                                data-refunded-amount="' . ($row->refunded_amount ?? 0) . '"
                                data-refund-request-status="' . ($row->refund_request_status ?? '') . '"
                                data-refund-request-reason="' . e($row->refund_request_reason ?? '') . '"
                                data-refund-requested-at="' . ($row->refund_requested_at ? \Carbon\Carbon::parse($row->refund_requested_at)->format('M d, Y H:i A') : '') . '">
                                <i class="ri-refund-line"></i> Refund
                            </button>';
                }

                return $buttons;
            })
            ->rawColumns(['order_id', 'user_info', 'item_info', 'payment_status', 'order_status', 'amount', 'action'])
            ->make(true);
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,unpaid,cancelled,refunded,partially_refunded',
            'order_status'   => 'required|in:pending,accepted,active,cancelled,completed',
        ]);

        $purchase = Purchase::findOrFail($id);
        $oldPaymentStatus = $purchase->payment_status;
        $oldOrderStatus = $purchase->order_status;

        $newPaymentStatus = $request->payment_status;
        $newOrderStatus = $request->order_status;

        $purchase->payment_status = $newPaymentStatus;
        $purchase->order_status = $newOrderStatus;
        $purchase->save();

        // If it is a membership purchase, manage expires_at
        if ($purchase->purchase_type === 'membership') {
            $isPaidOrActiveNow = ($newPaymentStatus === 'paid' || $newOrderStatus === 'active');
            $wasPaidOrActiveBefore = ($oldPaymentStatus === 'paid' || $oldOrderStatus === 'active');

            if ($isPaidOrActiveNow && !$wasPaidOrActiveBefore) {
                $package = MembershipPackage::withTrashed()->find($purchase->membership_package_id);
                if ($package) {
                    // Set expires_at based on validity_days
                    if ($package->validity_days && $package->validity_days > 0) {
                        $purchase->update([
                            'expires_at' => now()->addDays($package->validity_days)
                        ]);
                    } else {
                        $purchase->update([
                            'expires_at' => null
                        ]);
                    }
                }
            } elseif (!$isPaidOrActiveNow && $wasPaidOrActiveBefore) {
                // Clear expires_at
                $purchase->update([
                    'expires_at' => null
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!',
        ]);
    }

    /**
     * Issue a refund for the specified purchase via Stripe.
     */
    public function refund(Request $request, $id)
    {
        $request->validate([
            'refund_type' => 'required|in:full,partial',
            'refund_amount' => 'required_if:refund_type,partial|nullable|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $purchase = Purchase::findOrFail($id);

            if ($purchase->payment_status !== 'paid' && $purchase->payment_status !== 'partially_refunded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only paid or partially refunded orders can be refunded.',
                ], 400);
            }

            if (floatval($purchase->amount) <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Free orders cannot be refunded.',
                ], 400);
            }

            $maxRefundable = floatval($purchase->amount) - floatval($purchase->refunded_amount);
            if ($maxRefundable <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order has already been fully refunded.',
                ], 400);
            }

            $refundAmount = $request->refund_type === 'full' ? $maxRefundable : floatval($request->refund_amount);

            if ($refundAmount > $maxRefundable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund amount cannot exceed the remaining refundable balance of $' . number_format($maxRefundable, 2) . '.',
                ], 400);
            }

            // Get Stripe secret and initialize Stripe client
            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe configuration is missing.',
                ], 500);
            }

            $stripe = app(\Stripe\StripeClient::class);

            // Fetch Stripe payment intent id if missing
            $paymentIntentId = $purchase->stripe_payment_intent_id;
            if (empty($paymentIntentId) && $purchase->stripe_session_id) {
                try {
                    if (str_starts_with($purchase->stripe_session_id, 'in_')) {
                        // Invoice ID from subscription renewal
                        $invoice = $stripe->invoices->retrieve($purchase->stripe_session_id);
                        $paymentIntentId = $invoice->payment_intent;
                    } else {
                        // Checkout session
                        $session = $stripe->checkout->sessions->retrieve($purchase->stripe_session_id);
                        $paymentIntentId = $session->payment_intent;

                        // Fallback: If payment_intent is null (common in subscription mode checkout sessions), try retrieving the invoice
                        if (empty($paymentIntentId)) {
                            if (!empty($session->invoice)) {
                                $invoice = $stripe->invoices->retrieve($session->invoice);
                                $paymentIntentId = $invoice->payment_intent;
                            } elseif (!empty($session->subscription)) {
                                $subscription = $stripe->subscriptions->retrieve($session->subscription);
                                if ($subscription && $subscription->latest_invoice) {
                                    $invoice = $stripe->invoices->retrieve($subscription->latest_invoice);
                                    $paymentIntentId = $invoice->payment_intent;
                                }
                            }
                        }
                    }

                    if ($paymentIntentId) {
                        $purchase->stripe_payment_intent_id = $paymentIntentId;
                        $purchase->save();
                    }
                } catch (\Exception $ex) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not retrieve payment details from Stripe: ' . $ex->getMessage(),
                    ], 500);
                }
            }

            // If Stripe transaction ID is missing, process as a manual refund
            $isManualRefund = empty($paymentIntentId);
            $stripeRefundId = 'MANUAL-' . strtoupper(\Illuminate\Support\Str::random(10));
            $stripeRefundStatus = 'succeeded';

            if (!$isManualRefund) {
                // Perform Stripe refund
                $refundParams = [
                    'payment_intent' => $paymentIntentId,
                    'amount' => intval(round($refundAmount * 100)), // Stripe expects amount in cents
                ];

                if ($request->reason) {
                    $refundParams['metadata'] = [
                        'reason' => $request->reason,
                        'admin_id' => auth()->id(),
                        'admin_email' => auth()->user()->email ?? '',
                    ];
                }

                $stripeRefund = $stripe->refunds->create($refundParams);
                $stripeRefundId = $stripeRefund->id;
                $stripeRefundStatus = $stripeRefund->status ?? 'succeeded';
            }

            // Update Purchase Record
            $newRefundedAmount = floatval($purchase->refunded_amount) + $refundAmount;
            $purchase->refunded_amount = $newRefundedAmount;

            $isFullyRefunded = abs($newRefundedAmount - floatval($purchase->amount)) < 0.01;
            $purchase->payment_status = $isFullyRefunded ? 'refunded' : 'partially_refunded';
            
            // If fully refunded, cancel order access
            if ($isFullyRefunded) {
                $purchase->order_status = 'cancelled';
            }
            if ($purchase->refund_request_status === 'pending') {
                $purchase->refund_request_status = 'approved';
            }
            $purchase->save();

            if ($isFullyRefunded) {
                if ($purchase->purchase_type === 'membership') {
                    $user = User::find($purchase->user_id);
                    if ($user) {
                        if (!empty($user->stripe_subscription_id)) {
                            try {
                                $stripe->subscriptions->cancel($user->stripe_subscription_id);
                            } catch (\Exception $ex) {
                                Log::error("Failed to cancel Stripe subscription during auto-refund cancellation: " . $ex->getMessage());
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

            // Record Refund details in DB
            PurchaseRefund::create([
                'purchase_id' => $purchase->id,
                'stripe_refund_id' => $stripeRefundId,
                'amount' => $refundAmount,
                'reason' => $request->reason ?? 'Refunded by administrator',
                'admin_id' => auth()->id(),
                'status' => $stripeRefundStatus,
            ]);

            $successMessage = $isManualRefund 
                ? 'Manual refund processed successfully in the system! Amount: $' . number_format($refundAmount, 2) . '.'
                : 'Refund processed successfully via Stripe! Amount refunded: $' . number_format($refundAmount, 2) . '.';

            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe Refund Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stripe Refund Failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get refund history for the specified purchase.
     */
    public function refundHistory($id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            $refunds = PurchaseRefund::where('purchase_id', $purchase->id)
                ->with('admin')
                ->orderBy('created_at', 'desc')
                ->get();

            $formatted = $refunds->map(function ($r) {
                $adminName = $r->admin ? ($r->admin->full_name ?: $r->admin->email) : 'System / Webhook';
                return [
                    'id' => $r->id,
                    'stripe_refund_id' => $r->stripe_refund_id ?? 'N/A',
                    'amount' => '$' . number_format($r->amount, 2),
                    'reason' => $r->reason ?? '-',
                    'admin' => $adminName,
                    'date' => $r->created_at ? $r->created_at->format('M d, Y H:i A') : '-',
                    'status' => ucfirst($r->status),
                ];
            });

            return response()->json([
                'success' => true,
                'refunds' => $formatted,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch refund history.',
            ], 500);
        }
    }

    /**
     * Reject a pending refund request.
     */
    public function rejectRefund(Request $request, $id)
    {
        try {
            $purchase = Purchase::findOrFail($id);

            if ($purchase->refund_request_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending refund request found for this order.',
                ], 400);
            }

            $purchase->refund_request_status = 'rejected';
            $purchase->save();

            return response()->json([
                'success' => true,
                'message' => 'Refund request rejected successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Reject Refund Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject refund request: ' . $e->getMessage(),
            ], 500);
        }
    }
}
