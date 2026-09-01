<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StrategicAdvisory;
use App\Traits\ApiResponse;
use Throwable;

class StrategicAdvisoryApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Strategic Advisory page details.
     */
    public function getStrategicAdvisory()
    {
        try {
            // Retrieve the first StrategicAdvisory record with its features
            $strategicAdvisory = StrategicAdvisory::with('features')->first();

            // Existence Check
            if (!$strategicAdvisory) {
                return $this->errorResponse([], 'Strategic Advisory details not found.', 404);
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'                            => $strategicAdvisory->id,
                'title1'                        => $strategicAdvisory->title1,
                'title2'                        => $strategicAdvisory->title2,
                'tagline'                       => $strategicAdvisory->tagline,
                'short_description'             => $strategicAdvisory->short_description,

                'purpose_tagline'               => $strategicAdvisory->purpose_tagline,
                'purpose_title'                 => $strategicAdvisory->purpose_title,
                'purpose_short_description'     => $strategicAdvisory->purpose_short_description,

                'advisory_title'                => $strategicAdvisory->advisory_title,

                'panel_title'                   => $strategicAdvisory->panel_title,
                'panel_short_description'       => $strategicAdvisory->panel_short_description,

                'appointment_title'             => $strategicAdvisory->appointment_title,
                'appointment_short_description' => $strategicAdvisory->appointment_short_description,

                'conflict_title'                => $strategicAdvisory->conflict_title,
                'conflict_short_description'    => $strategicAdvisory->conflict_short_description,

                'expression_title'              => $strategicAdvisory->expression_title,
                'expression_description'        => $strategicAdvisory->expression_description,

                'content_file'                  => $strategicAdvisory->content_file ? asset($strategicAdvisory->content_file) : null,
                'injected_status'               => (bool) $strategicAdvisory->injected_status,
                'strategic_advisory_features' => $strategicAdvisory->features->map(function ($feature) {
                    return [
                        'id'          => $feature->id,
                        'description' => $feature->description,
                    ];
                }),
            ];

            // Response Wrapper
            $response = [
                'strategic_advisories' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'Strategic Advisory details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch strategic advisory details.', 500);
        }
    }
}
