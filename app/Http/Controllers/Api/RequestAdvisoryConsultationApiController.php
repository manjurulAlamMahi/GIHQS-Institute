<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RequestAdvisory;
use App\Traits\ApiResponse;
use Throwable;

class RequestAdvisoryConsultationApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Request Advisory Consultation page details.
     */
    public function getRequestAdvisoryConsultation()
    {
        try {
            // Retrieve the first RequestAdvisory record
            $consultation = RequestAdvisory::first();

            // Existence Check
            if (!$consultation) {
                return $this->errorResponse([], 'Request Advisory Consultation details not found.', 404);
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'          => $consultation->id,
                'title1'      => $consultation->title1,
                'title2'      => $consultation->title2,
                'tagline'     => $consultation->tagline,
                'description' => $consultation->description,
            ];

            // Response Wrapper (table name: request_advisories)
            $response = [
                'request_advisories' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'Request Advisory Consultation details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch request advisory consultation details.', 500);
        }
    }
}
