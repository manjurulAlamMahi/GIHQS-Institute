<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MiaHelper;
use App\Http\Controllers\Controller;
use App\Models\AboutInstitute;
use App\Traits\ApiResponse;
use Throwable;

class AboutInstituteApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch About Institute page details with its FAQs.
     */
    public function getAboutInstitute()
    {
        try {
            // Retrieve the first AboutInstitute record with its FAQs
            $aboutInstitute = AboutInstitute::with('faqs')->first();

            // Existence Check
            if (!$aboutInstitute) {
                return $this->errorResponse([], 'About Institute details not found.', 404);
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'              => $aboutInstitute->id,
                'title1'          => $aboutInstitute->title1,
                'title2'          => $aboutInstitute->title2,
                'tag_line'        => $aboutInstitute->tag_line,
                'description'     => MiaHelper::htmlToMarkdown($aboutInstitute->description),
                'image'           => $aboutInstitute->image ? asset($aboutInstitute->image) : null,
                'content_file'    => $aboutInstitute->content_file ? asset($aboutInstitute->content_file) : null,
                'injected_status' => (bool) $aboutInstitute->injected_status,
                'faqs'            => $aboutInstitute->faqs->map(function ($faq) {
                    return [
                        'id'                    => $faq->id,
                        'faq_title'             => $faq->faq_title,
                        'faq_short_description' => $faq->faq_short_description,
                    ];
                }),
            ];

            // Response Wrapper
            $response = [
                'about_institutes' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'About Institute details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch about institute details.', 500);
        }
    }
}
