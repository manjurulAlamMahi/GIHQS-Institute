<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccreditationHeader;
use App\Traits\ApiResponse;
use Throwable;

class AccreditationHeaderApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Accreditation Header details.
     */
    public function getAccreditationHeader()
    {
        try {
            // Retrieve the first AccreditationHeader record with its tags and keyfacts
            $header = AccreditationHeader::with(['tags', 'keyfacts'])->first();

            // Existence Check
            if (!$header) {
                return $this->errorResponse([], 'Accreditation Header details not found.', 404);
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'                     => $header->id,
                'title1'                 => $header->title1,
                'title2'                 => $header->title2,
                'tagline'                => $header->tagline,
                'description'            => $header->description,
                'note'                   => $header->note,
                'apply_btn_text'         => $header->apply_btn_text,
                'download_btn_text'      => $header->download_btn_text,
                'download_file'          => $header->download_file ? asset($header->download_file) : null,
                'content_file'           => $header->content_file ? asset($header->content_file) : null,
                'injected_status'        => (bool) $header->injected_status,

                // Map tags using table name: accreditation_tags
                'accreditation_tags'     => $header->tags->map(function ($tag) {
                    return [
                        'id'                      => $tag->id,
                        'accreditation_header_id' => $tag->accreditation_header_id,
                        'tagname'                 => $tag->tagname,
                    ];
                }),

                // Map keyfacts using table name: accreditation_keyfacts
                'accreditation_keyfacts' => $header->keyfacts->map(function ($keyfact) {
                    return [
                        'id'                      => $keyfact->id,
                        'accreditation_header_id' => $keyfact->accreditation_header_id,
                        'title'                   => $keyfact->title,
                        'subtitle'                => $keyfact->subtitle,
                    ];
                }),
            ];

            // Response Wrapper (table name: accreditation_headers)
            $response = [
                'accreditation_headers' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'Accreditation Header details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch accreditation header details.', 500);
        }
    }
}
