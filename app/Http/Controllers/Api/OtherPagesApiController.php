<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtherPage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Throwable;

class OtherPagesApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch list of all other pages or filter by slug.
     */
    public function index(Request $request)
    {
        try {
            $slug = $request->query('slug');

            if ($slug) {
                $page = OtherPage::where('slug', $slug)->first();

                if (!$page) {
                    return $this->errorResponse([], 'Page not found.', 404);
                }

                $formattedData = [
                    'id'              => $page->id,
                    'slug'            => $page->slug,
                    'title'           => $page->title,
                    'content_file'    => $page->content_file ? asset($page->content_file) : null,
                    'injected_status' => (bool) $page->injected_status,
                ];

                $response = [
                    'other_page' => $formattedData,
                ];

                return $this->successResponse($response, $page->title . ' details fetched successfully.', 200);
            }

            $pages = OtherPage::all();

            if ($pages->isEmpty()) {
                return $this->errorResponse([], 'Other pages not found.', 404);
            }

            $formattedData = $pages->map(function ($page) {
                return [
                    'id'              => $page->id,
                    'slug'            => $page->slug,
                    'title'           => $page->title,
                    'content_file'    => $page->content_file ? asset($page->content_file) : null,
                    'injected_status' => (bool) $page->injected_status,
                ];
            });

            $response = [
                'other_pages' => $formattedData,
            ];

            return $this->successResponse($response, 'Other pages fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch other pages.', 500);
        }
    }

    /**
     * Fetch detail for a specific other page (e.g. privacy-policy, terms-of-use).
     */
    public function getOtherPage($slug)
    {
        try {
            $page = OtherPage::where('slug', $slug)->first();

            if (!$page) {
                return $this->errorResponse([], 'Page not found.', 404);
            }

            $formattedData = [
                'id'              => $page->id,
                'slug'            => $page->slug,
                'title'           => $page->title,
                'content_file'    => $page->content_file ? asset($page->content_file) : null,
                'injected_status' => (bool) $page->injected_status,
            ];

            $response = [
                'other_page' => $formattedData,
            ];

            return $this->successResponse($response, $page->title . ' details fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch page details.', 500);
        }
    }
}
