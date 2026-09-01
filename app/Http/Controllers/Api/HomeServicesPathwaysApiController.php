<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeGihq;
use App\Traits\ApiResponse;
use Throwable;

class HomeServicesPathwaysApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Home Services & Pathways page details.
     */
    public function getHomeServicesPathways()
    {
        try {
            // Retrieve the first HomeGihq record with its relationships
            $homeGihq = HomeGihq::with(['servicesPathways', 'professionalPathways', 'nextStep'])->first();

            // Existence Check
            if (!$homeGihq) {
                return $this->errorResponse([], 'Home Services & Pathways details not found.', 404);
            }

            // Formatting next step relation (table: home_next_steps)
            $nextStepData = null;
            if ($homeGihq->nextStep) {
                $nextStepData = [
                    'id'                   => $homeGihq->nextStep->id,
                    'title1'               => $homeGihq->nextStep->title1,
                    'title2'               => $homeGihq->nextStep->title2,
                    'tagline'              => $homeGihq->nextStep->tagline,
                    'certificate_btn_text' => $homeGihq->nextStep->certificate_btn_text,
                    'learning_btn_text'    => $homeGihq->nextStep->learning_btn_text,
                    'advisory_btn_text'    => $homeGihq->nextStep->advisory_btn_text,
                    'member_btn_text'      => $homeGihq->nextStep->member_btn_text,
                ];
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'                            => $homeGihq->id,
                'title1'                        => $homeGihq->title1,
                'title2'                        => $homeGihq->title2,
                'tagline'                       => $homeGihq->tagline,
                'description'                   => $homeGihq->description,
                'certificate_btn_text'          => $homeGihq->certificate_btn_text,
                'learning_btn_text'             => $homeGihq->learning_btn_text,
                'advisory_btn_text'             => $homeGihq->advisory_btn_text,
                'member_btn_text'               => $homeGihq->member_btn_text,

                'professional_ecosystem_title'  => $homeGihq->professional_ecosystem_title,
                'learning_tagline'              => $homeGihq->learning_tagline,
                'learning_title'                => $homeGihq->learning_title,
                'learning_details'              => $homeGihq->learning_details,
                'certificate_tagline'           => $homeGihq->certificate_tagline,
                'certificate_title'             => $homeGihq->certificate_title,
                'certificate_details'           => $homeGihq->certificate_details,
                'lead_tagline'                  => $homeGihq->lead_tagline,
                'lead_title'                    => $homeGihq->lead_title,
                'lead_details'                  => $homeGihq->lead_details,

                'content_file'                  => $homeGihq->content_file ? asset($homeGihq->content_file) : null,
                'injected_status'               => (bool) $homeGihq->injected_status,

                // Map relationships to table names
                'home_services_pathways'        => $homeGihq->servicesPathways->map(function ($item) {
                    return [
                        'id'              => $item->id,
                        'serial'          => $item->serial,
                        'target_audience' => $item->target_audience,
                        'title'           => $item->title,
                        'description'     => $item->description,
                        'link_text'       => $item->link_text,
                    ];
                }),

                'home_professional_pathways'    => $homeGihq->professionalPathways->map(function ($item) {
                    return [
                        'id'          => $item->id,
                        'serial'      => $item->serial,
                        'title'       => $item->title,
                        'description' => $item->description,
                        'link_text'   => $item->link_text,
                    ];
                }),

                'home_next_steps'               => $nextStepData,
            ];

            // Response Wrapper (table name: home_gihqs)
            $response = [
                'home_gihqs' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'Home Services & Pathways details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch home services & pathways details.', 500);
        }
    }
}
