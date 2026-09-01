<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutContact;
use App\Traits\ApiResponse;
use Throwable;

class AboutContactApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch About Contact page details.
     */
    public function getAboutContact()
    {
        try {
            // Retrieve the first AboutContact record
            $aboutContact = AboutContact::first();

            // Existence Check
            if (!$aboutContact) {
                return $this->errorResponse([], 'About Contact details not found.', 404);
            }

            // Data Formatting & Mapping
            $formattedData = [
                'id'              => $aboutContact->id,
                'title'           => $aboutContact->title,
                'phone'           => $aboutContact->phone,
                'email'           => $aboutContact->email,
                'address'         => $aboutContact->address,
                'working_hours'   => $aboutContact->working_hours,
                'mission'         => $aboutContact->mission,
                'content_file'    => $aboutContact->content_file ? asset($aboutContact->content_file) : null,
                'injected_status' => (bool) $aboutContact->injected_status,
            ];

            // Response Wrapper
            $response = [
                'about_contact' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'About Contact details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch about contact details.', 500);
        }
    }
}
