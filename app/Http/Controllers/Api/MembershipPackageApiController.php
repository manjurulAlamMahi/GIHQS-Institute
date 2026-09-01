<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipPackage;
use App\Traits\ApiResponse;
use Throwable;

class MembershipPackageApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch all membership packages.
     */
    public function index()
    {
        try {
            // Retrieve all packages with their features
            $packages = MembershipPackage::with('features')->orderBy('id', 'asc')->get();

            // Existence Check
            if ($packages->isEmpty()) {
                return $this->errorResponse([], 'No membership packages found.', 404);
            }

            // Get authenticated user if available
            $user = auth('api')->user();

            // Data Formatting & Mapping
            $formattedPackages = $packages->map(function ($package) use ($user) {
                $meta = $this->getPackageRelativeMetadata($package, $user);
                return [
                    'id'                => $package->id,
                    'name'              => $package->name,
                    'title'             => $package->title,
                    'short_description' => $package->short_description,
                    'price'             => (float) $package->price,
                    'discount_percentage' => (float) $package->discount_percentage,
                    'validity_days'     => (int) $package->validity_days,
                    'exam_attempt_limit'  => (int) $package->exam_attempt_limit,
                    'status'            => (int) $package->status,
                    'is_current'        => $meta['is_current'],
                    'status_label'      => $meta['status_label'],
                    'allowed_actions'   => $meta['allowed_actions'],
                    'features'          => $package->features->map(function ($feature) {
                        return [
                            'id'                    => $feature->id,
                            'membership_package_id' => $feature->membership_package_id,
                            'description'           => $feature->description,
                            'badge'                 => $feature->badge,
                            'note'                  => $feature->note,
                        ];
                    }),
                ];
            });

            // Response Wrapper
            $response = [
                'membership_packages' => $formattedPackages,
            ];

            // Success Response
            return $this->successResponse($response, 'Membership packages fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch membership packages.', 500);
        }
    }

    /**
     * Fetch a single membership package.
     */
    public function show($id)
    {
        try {
            // Retrieve membership package with features by id
            $package = MembershipPackage::with('features')->find($id);

            // Existence Check
            if (!$package) {
                return $this->errorResponse([], 'Membership package not found.', 404);
            }

            // Get authenticated user if available
            $user = auth('api')->user();
            $meta = $this->getPackageRelativeMetadata($package, $user);

            // Data Formatting & Mapping
            $formattedPackage = [
                'id'                => $package->id,
                'name'              => $package->name,
                'title'             => $package->title,
                'short_description' => $package->short_description,
                'price'             => (float) $package->price,
                'discount_percentage' => (float) $package->discount_percentage,
                'validity_days'     => (int) $package->validity_days,
                'exam_attempt_limit'  => (int) $package->exam_attempt_limit,
                'status'            => (int) $package->status,
                'is_current'        => $meta['is_current'],
                'status_label'      => $meta['status_label'],
                'allowed_actions'   => $meta['allowed_actions'],
                'features'          => $package->features->map(function ($feature) {
                    return [
                        'id'                    => $feature->id,
                        'membership_package_id' => $feature->membership_package_id,
                        'description'           => $feature->description,
                        'badge'                 => $feature->badge,
                        'note'                  => $feature->note,
                    ];
                }),
            ];

            // Response Wrapper
            $response = [
                'membership_package' => $formattedPackage,
            ];

            // Success Response
            return $this->successResponse($response, 'Membership package details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch membership package.', 500);
        }
    }

    /**
     * Get metadata for a package relative to the authenticated user's active membership.
     */
    private function getPackageRelativeMetadata($package, $user)
    {
        if (!$user) {
            return [
                'is_current'      => false,
                'status_label'    => null,
                'allowed_actions' => ['upgrade'],
            ];
        }

        $activeMembership = $user->active_membership;
        $activePrice = $activeMembership ? (float) $activeMembership->price : 0.00;
        $activePackageId = $activeMembership ? $activeMembership->id : null;
        if (!$activePackageId) {
            // Default to Standard package (ID = 1) if no active membership is assigned
            $standardPackage = MembershipPackage::where('name', 'Standard')->first();
            $activePackageId = $standardPackage ? $standardPackage->id : 1;
        }

        $isCurrent = ($package->id === $activePackageId);
        $packagePrice = (float) $package->price;

        if ($isCurrent) {
            $statusLabel = 'current';
            $allowedActions = [];
            // If it's a paid plan, they can cancel it if auto-renewal is active
            if ($packagePrice > 0) {
                if ($user->stripe_subscription_id && !$user->stripe_subscription_cancel_at_period_end) {
                    $allowedActions[] = 'cancel';
                }
            }
        } else {
            if ($packagePrice > $activePrice) {
                $statusLabel = 'upgrade';
                $allowedActions = ['upgrade'];
            } elseif ($packagePrice < $activePrice) {
                $statusLabel = 'downgrade';
                $allowedActions = ['downgrade'];
            } else {
                $statusLabel = 'switch';
                $allowedActions = ['switch'];
            }
        }

        return [
            'is_current'      => $isCurrent,
            'status_label'    => $statusLabel,
            'allowed_actions' => $allowedActions,
        ];
    }
}
