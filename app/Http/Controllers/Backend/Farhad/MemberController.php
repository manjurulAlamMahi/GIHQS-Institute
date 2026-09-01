<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MembershipPackage;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('role', 'user')
                ->with(['activeMembershipPurchase.membershipPackage']);

            if ($request->filled('role')) {
                $role = $request->role;
                if ($role === 'user') {
                    $query->whereDoesntHave('activeMembershipPurchase', function ($q) {
                        $q->where(function ($q2) {
                            $q2->whereNull('expires_at')
                               ->orWhere('expires_at', '>=', now());
                        });
                    });
                } elseif ($role === 'standard_member' || $role === 'premium_member') {
                    $packageName = ($role === 'premium_member') ? 'Premium' : 'Standard';
                    $query->whereHas('activeMembershipPurchase', function ($q) use ($packageName) {
                        $q->where(function ($q2) {
                            $q2->whereNull('expires_at')
                               ->orWhere('expires_at', '>=', now());
                        })->whereHas('membershipPackage', function ($qp) use ($packageName) {
                            $qp->where('name', $packageName);
                        });
                    });
                } else {
                    $query->whereHas('activeMembershipPurchase', function ($q) use ($role) {
                        $q->where(function ($q2) {
                            $q2->whereNull('expires_at')
                               ->orWhere('expires_at', '>=', now());
                        })->where('membership_package_id', $role);
                    });
                }
            }

            $members = $query->latest();

            return DataTables::of($members)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->full_name ?: trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('role', function ($row) {
                    $activePackage = $row->active_membership;
                    if ($activePackage) {
                        if ($activePackage->name === 'Premium') {
                            return '<span class="badge bg-danger">Premium Member</span>';
                        } elseif ($activePackage->name === 'Standard') {
                            return '<span class="badge bg-warning">Standard Member</span>';
                        }
                        return '<span class="badge bg-success">' . e($activePackage->title ?: $activePackage->name) . '</span>';
                    } else {
                        return '<span class="badge bg-info">Normal User</span>';
                    }
                })
                ->addColumn('joined_date', function ($row) {
                    return $row->created_at ? $row->created_at->format('M d, Y') : '-';
                })
                ->addColumn('expiry_date', function ($row) {
                    $purchase = $row->activeMembershipPurchase;
                    if ($purchase) {
                        if ($purchase->expires_at) {
                            return $purchase->expires_at->format('M d, Y');
                        }
                        return '<span class="badge bg-success">Lifetime</span>';
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = ($row->status == 1) ? 'checked' : '';
                    return '<div class="form-check form-switch form-switch-right form-switch-md">
                            <input class="form-check-input status-switch" type="checkbox" data-id="' . $row->id . '" data-type="user" ' . $checked . '>
                        </div>';
                })
                ->addColumn('action', function ($row) {
                    $purchase = $row->activeMembershipPurchase;
                    $packageId = $purchase ? $purchase->membership_package_id : '';
                    $expiresAt = ($purchase && $purchase->expires_at) ? $purchase->expires_at->format('Y-m-d') : '';
                    $name = e($row->full_name ?: trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')));

                    return '<button type="button" class="btn btn-sm btn-primary edit-member-btn"
                        data-id="' . $row->id . '"
                        data-name="' . $name . '"
                        data-email="' . e($row->email) . '"
                        data-status="' . $row->status . '"
                        data-package-id="' . $packageId . '"
                        data-expires-at="' . $expiresAt . '"
                        data-stripe-sub-id="' . e($row->stripe_subscription_id) . '"
                        data-stripe-sub-status="' . e($row->stripe_subscription_status) . '"
                        data-stripe-next-renewal="' . ($row->stripe_next_renewal_at ? \Carbon\Carbon::parse($row->stripe_next_renewal_at)->format('Y-m-d') : '') . '"
                        data-stripe-cancel-at-period-end="' . ($row->stripe_subscription_cancel_at_period_end ? '1' : '0') . '"
                        title="Edit Member Status & Expiry">
                        <i class="ri-edit-line me-1"></i> Edit
                    </button>';
                })
                ->rawColumns(['role', 'expiry_date', 'status', 'action'])
                ->make(true);
        }

        $membershipPackages = MembershipPackage::where('status', 1)->get();

        return view('backend.layouts.members.index', compact('membershipPackages'));
    }

    /**
     * Update member status and membership expiry date.
     */
    public function updateMembership(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'status'                => 'required|in:0,1',
            'membership_package_id' => 'nullable|string',
            'expires_at'            => 'nullable|date',
        ]);

        // Update user status
        $user->status = (int) $request->status;
        $user->save();

        // Cancel Stripe subscription if package is removed or changed
        if (!empty($user->stripe_subscription_id) && 
            ($request->membership_package_id === 'none' || 
             ($request->filled('membership_package_id') && $request->membership_package_id != $user->membership_package_id))) {
            try {
                $stripeSecret = config('services.stripe.secret');
                $stripe = new \Stripe\StripeClient($stripeSecret);
                $stripe->subscriptions->cancel($user->stripe_subscription_id);
                \Illuminate\Support\Facades\Log::info("Admin manually changed membership. Cancelled Stripe subscription ID: {$user->stripe_subscription_id}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to cancel Stripe subscription during admin membership manual update: " . $e->getMessage());
            }
            
            // Clear subscription fields on user
            $user->stripe_subscription_status = 'canceled';
            $user->stripe_subscription_id = null;
            $user->membership_package_id = null;
            $user->stripe_subscription_period_start = null;
            $user->stripe_subscription_period_end = null;
            $user->stripe_next_renewal_at = null;
            $user->stripe_subscription_cancel_at_period_end = false;
            $user->save();
        }

        // Update membership details if a package is selected or removed
        if ($request->filled('membership_package_id') && $request->membership_package_id !== 'none') {
            $packageId = $request->membership_package_id;
            $expiresAt = $request->filled('expires_at') ? \Carbon\Carbon::parse($request->expires_at)->endOfDay() : null;

            $purchase = $user->activeMembershipPurchase;

            if ($purchase) {
                $purchase->update([
                    'membership_package_id' => $packageId,
                    'expires_at'            => $expiresAt,
                    'payment_status'        => 'paid',
                ]);
            } else {
                Purchase::create([
                    'user_id'               => $user->id,
                    'purchase_type'         => 'membership',
                    'membership_package_id' => $packageId,
                    'order_id'              => Purchase::generateUniqueOrderId('membership'),
                    'payment_status'        => 'paid',
                    'amount'                => 0.00,
                    'expires_at'            => $expiresAt,
                ]);
            }
            
            // Sync database column on user table
            $user->update(['membership_package_id' => $packageId]);

        } elseif ($request->membership_package_id === 'none') {
            // Cancel active membership by setting expiry date in past
            $purchase = $user->activeMembershipPurchase;
            if ($purchase) {
                $purchase->update([
                    'expires_at' => now()->subDay(),
                ]);
            }
            
            // Sync database column on user table
            $user->update(['membership_package_id' => null]);

        } elseif ($request->filled('expires_at')) {
            // Update expiry date on existing purchase if package unchanged
            $purchase = $user->activeMembershipPurchase;
            if ($purchase) {
                $purchase->update([
                    'expires_at' => \Carbon\Carbon::parse($request->expires_at)->endOfDay(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Member status and membership expiry date updated successfully.',
        ]);
    }

    /**
     * Cancel a member's Stripe subscription from Admin panel.
     */
    public function cancelSubscription(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'cancel_mode' => 'required|in:at_period_end,immediately',
            ]);

            if (empty($user->stripe_subscription_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active Stripe subscription found for this user.',
                ], 400);
            }

            $stripeSecret = config('services.stripe.secret');
            if (empty($stripeSecret)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe secret key configuration is missing.',
                ], 500);
            }

            $stripe = new \Stripe\StripeClient($stripeSecret);

            if ($request->cancel_mode === 'at_period_end') {
                $stripe->subscriptions->update($user->stripe_subscription_id, [
                    'cancel_at_period_end' => true
                ]);

                $user->update([
                    'stripe_subscription_cancel_at_period_end' => true,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Auto-renewal cancelled successfully. The membership will remain active until the end of the billing period.',
                ]);
            } else { // immediately
                $stripe->subscriptions->cancel($user->stripe_subscription_id);

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

                return response()->json([
                    'success' => true,
                    'message' => 'Subscription cancelled immediately. Default Standard membership assigned.',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription: ' . $th->getMessage(),
            ], 500);
        }
    }
}
