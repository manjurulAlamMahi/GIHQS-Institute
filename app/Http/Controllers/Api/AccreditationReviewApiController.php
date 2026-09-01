<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccreditationReview;
use App\Traits\ApiResponse;
use Throwable;

class AccreditationReviewApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Accreditation Review page details.
     */
    public function getAccreditationReview()
    {
        try {
            // Retrieve the first AccreditationReview record with its features
            $accreditationReview = AccreditationReview::with('features')->first();

            // Existence Check
            if (!$accreditationReview) {
                return $this->errorResponse([], 'Accreditation Review details not found.', 404);
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'                            => $accreditationReview->id,
                'title1'                        => $accreditationReview->title1,
                'title2'                        => $accreditationReview->title2,
                'tagline'                       => $accreditationReview->tagline,
                'short_description'             => $accreditationReview->short_description,

                'purpose_tagline'               => $accreditationReview->purpose_tagline,
                'purpose_title'                 => $accreditationReview->purpose_title,
                'purpose_short_description'     => $accreditationReview->purpose_short_description,

                'review_title'                  => $accreditationReview->review_title,

                'panel_title'                   => $accreditationReview->panel_title,
                'panel_short_description'       => $accreditationReview->panel_short_description,

                'appointment_title'             => $accreditationReview->appointment_title,
                'appointment_short_description' => $accreditationReview->appointment_short_description,

                'conflict_title'                => $accreditationReview->conflict_title,
                'conflict_short_description'    => $accreditationReview->conflict_short_description,

                'expression_title'              => $accreditationReview->expression_title,
                'expression_description'        => $accreditationReview->expression_description,

                'content_file'                  => $accreditationReview->content_file ? asset($accreditationReview->content_file) : null,
                'injected_status'               => (bool) $accreditationReview->injected_status,
                'accreditation_review_features' => $accreditationReview->features->map(function ($feature) {
                    return [
                        'id'          => $feature->id,
                        'description' => $feature->description,
                    ];
                }),
            ];

            // Response Wrapper
            $response = [
                'accreditation_reviews' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'Accreditation Review details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch accreditation review details.', 500);
        }
    }
}
