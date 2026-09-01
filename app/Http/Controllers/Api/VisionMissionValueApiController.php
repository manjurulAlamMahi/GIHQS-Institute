<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisionMissionValue;
use App\Traits\ApiResponse;
use Throwable;

class VisionMissionValueApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Vision, Mission, and Values details.
     */
    public function getVisionMissionValues()
    {
        try {
            // Retrieve the first VisionMissionValue record
            $vmv = VisionMissionValue::first();

            // Existence Check
            if (!$vmv) {
                return $this->errorResponse([], 'Vision, Mission, and Values details not found.', 404);
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'                                   => $vmv->id,
                
                // General Section
                'tagline'                              => $vmv->tagline,
                'title1'                               => $vmv->title1,
                'title2'                               => $vmv->title2,
                'short_description'                    => $vmv->short_description,

                // Vision Section
                'vision_tagline'                       => $vmv->vision_tagline,
                'vision_title'                         => $vmv->vision_title,
                'vision_short_description'             => $vmv->vision_short_description,

                // Mission Section
                'mission_tagline'                      => $vmv->mission_tagline,
                'mission_title'                        => $vmv->mission_title,
                'mission_short_description'            => $vmv->mission_short_description,

                // Value Section
                'value_tagline'                        => $vmv->value_tagline,
                'value_title'                          => $vmv->value_title,
                'value_title2'                         => $vmv->value_title2,
                'value_short_description'              => $vmv->value_short_description,

                // Global Perspective Section
                'global_perspective_tagline'           => $vmv->global_perspective_tagline,
                'global_perspective_title'             => $vmv->global_perspective_title,
                'global_perspective_short_description' => $vmv->global_perspective_short_description,

                // Integrity Section
                'integrity_tagline'                    => $vmv->integrity_tagline,
                'integrity_title'                      => $vmv->integrity_title,
                'integrity_short_description'          => $vmv->integrity_short_description,

                // Human Centered Section
                'human_centered_tagline'               => $vmv->human_centered_tagline,
                'human_centered_title'                 => $vmv->human_centered_title,
                'human_centered_short_description'     => $vmv->human_centered_short_description,

                // Quality & Excellence Section
                'quality_excellence_tagline'           => $vmv->quality_excellence_tagline,
                'quality_excellence_title'             => $vmv->quality_excellence_title,
                'quality_excellence_short_description' => $vmv->quality_excellence_short_description,

                // Safety Leadership Section
                'safety_leadership_tagline'            => $vmv->safety_leadership_tagline,
                'safety_leadership_title'              => $vmv->safety_leadership_title,
                'safety_leadership_short_description'  => $vmv->safety_leadership_short_description,

                'content_file'                         => $vmv->content_file ? asset($vmv->content_file) : null,
                'injected_status'                      => (bool) $vmv->injected_status,
            ];

            // Response Wrapper
            $response = [
                'vision_mission_values' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'Vision, Mission, and Values details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch vision, mission, and values details.', 500);
        }
    }
}
