<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccreditationFee;
use App\Traits\ApiResponse;
use Throwable;

class AccreditationFeeApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Accreditation Fees details.
     */
    public function getAccreditationFees()
    {
        try {
            // Retrieve the first AccreditationFee record with its plans and their features
            $fee = AccreditationFee::with(['plans.features'])->first();

            // Existence Check
            if (!$fee) {
                return $this->errorResponse([], 'Accreditation Fees details not found.', 404);
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'                       => $fee->id,
                'title1'                   => $fee->title1,
                'title2'                   => $fee->title2,
                'description'              => $fee->description,

                // Map plans using table name: accreditation_fees_plans
                'accreditation_fees_plans' => $fee->plans->map(function ($plan) {
                    return [
                        'id'                   => $plan->id,
                        'accreditation_fee_id' => $plan->accreditation_fee_id,
                        'title'                => $plan->title,
                        'price'                => $plan->price,
                        'description'          => $plan->description,

                        // Map features using table name: accreditation_fees_plan_features
                        'accreditation_fees_plan_features' => $plan->features->map(function ($feat) {
                            return [
                                'id'                         => $feat->id,
                                'accreditation_fees_plan_id' => $feat->accreditation_fees_plan_id,
                                'feature'                    => $feat->feature,
                            ];
                        }),
                    ];
                }),
            ];

            // Response Wrapper (table name: accreditation_fees)
            $response = [
                'accreditation_fees' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'Accreditation Fees details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch accreditation fees details.', 500);
        }
    }
}
