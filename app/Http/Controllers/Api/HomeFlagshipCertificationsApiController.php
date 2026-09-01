<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeRecognizedPathway;
use App\Traits\ApiResponse;
use Throwable;

class HomeFlagshipCertificationsApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Home Flagship Certifications details.
     */
    public function getHomeFlagshipCertifications()
    {
        try {
            // Retrieve the first HomeRecognizedPathway record with its certificates
            $homeRecognizedPathway = HomeRecognizedPathway::with('certificates')->first();

            // Existence Check
            if (!$homeRecognizedPathway) {
                return $this->errorResponse([], 'Home Flagship Certifications details not found.', 404);
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'                => $homeRecognizedPathway->id,
                'title1'            => $homeRecognizedPathway->title1,
                'title2'            => $homeRecognizedPathway->title2,
                'tagline'           => $homeRecognizedPathway->tagline,
                'description'       => $homeRecognizedPathway->description,
                'content_file'      => $homeRecognizedPathway->content_file ? asset($homeRecognizedPathway->content_file) : null,
                'injected_status'   => (bool) $homeRecognizedPathway->injected_status,

                // Map certificates using table name: home_certificates
                'home_certificates' => $homeRecognizedPathway->certificates->map(function ($cert) {
                    return [
                        'id'                         => $cert->id,
                        'home_recognized_pathway_id' => $cert->home_recognized_pathway_id,
                        'short_title'                => $cert->short_title,
                        'title'                      => $cert->title,
                        'icon'                       => $cert->icon ? asset($cert->icon) : null,
                        'tagline'                    => $cert->tagline,
                        'headline'                   => $cert->headline,
                        'description'                => $cert->description,
                        'audience'                   => $cert->audience,
                        'tags'                       => $cert->tags,
                        'button_text'                => $cert->button_text,
                    ];
                }),
            ];

            // Response Wrapper (table name: home_recognized_pathways)
            $response = [
                'home_recognized_pathways' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'Home Flagship Certifications details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch home flagship certifications details.', 500);
        }
    }
}
