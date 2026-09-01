<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogueHtmlResource;
use App\Services\HtmlDocumentRenderer;
use App\Services\HtmlResourceAccessService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Serves uploaded HTML documents.
 *
 * An <iframe src="..."> cannot send an Authorization header, so a document is
 * not fetched by id. The frontend exchanges its bearer token for a single-use
 * ticket and points the iframe at that. The direct /html/{resource} route is
 * kept only for documents marked public.
 */
class HtmlResourceController extends Controller
{
    use ApiResponse;

    public function __construct(
        private HtmlDocumentRenderer $renderer,
        private HtmlResourceAccessService $access,
    ) {
    }

    /**
     * POST /api/html/{resource}/redeem - exchange an access key for a licence.
     */
    public function redeem(Request $request, string $resource)
    {
        $validated = $request->validate(['key' => 'required|string|max:255']);

        $htmlResource = $this->findResource($resource);
        $user         = $this->currentUser();

        if (!$user) {
            return $this->errorResponse([], 'Please login first.', 401);
        }

        // A user who cannot reach the course cannot redeem against it, so a
        // stolen key alone is not enough.
        if (!$htmlResource->catalogue
            || !app(\App\Services\ExamEligibilityService::class)
                ->hasCatalogueAccess($user, $htmlResource->catalogue)) {
            return $this->errorResponse([], 'You do not have access to this course.', 403);
        }

        if (!$htmlResource->requiresLicense()) {
            return $this->successResponse([], 'This document does not require an access key.', 200);
        }

        $license = $this->access->redeem($htmlResource, $user, $validated['key']);

        if (!$license) {
            return $this->errorResponse(
                ['key' => ['That access key is not valid for this document.']],
                'Invalid access key.',
                422
            );
        }

        return $this->successResponse([
            'expires_at' => $license->expires_at?->toIso8601String(),
        ], 'Access key accepted.', 200);
    }

    /**
     * POST /api/html/{resource}/ticket - mint a one-time viewer URL.
     */
    public function ticket(Request $request, string $resource)
    {
        $htmlResource = $this->findResource($resource);
        $user         = $this->currentUser();

        [$allowed, $reason] = $this->access->check($htmlResource, $user);

        if (!$allowed) {
            return $this->errorResponse(
                ['reason' => $reason],
                $this->messageFor($reason),
                $user ? 403 : 401
            );
        }

        $ticket = $this->access->issueTicket($htmlResource, $user);

        return $this->successResponse([
            'url'        => url('/api/html/view/' . $ticket),
            'expires_in' => HtmlResourceAccessService::TICKET_TTL_SECONDS,
        ], 'Viewer ticket issued.', 200);
    }

    /**
     * GET /api/html/view/{ticket} - what the iframe actually loads.
     *
     * Deliberately unauthenticated: the ticket is the authority, it was checked
     * when issued, and it is destroyed on first use.
     */
    public function view(string $ticket): Response
    {
        $resourceId = $this->access->consumeTicket($ticket);

        if (!$resourceId) {
            abort(404);
        }

        $htmlResource = CatalogueHtmlResource::find($resourceId);

        if (!$htmlResource) {
            abort(404);
        }

        return $this->serve($htmlResource);
    }

    /**
     * GET /api/html/{resource} - retained for public documents only.
     */
    public function show(Request $request, string $resource): Response
    {
        $htmlResource = $this->findResource($resource);

        [$allowed] = $this->access->check($htmlResource, $this->currentUser());

        if (!$allowed) {
            abort(403);
        }

        return $this->serve($htmlResource);
    }

    private function serve(CatalogueHtmlResource $resource): Response
    {
        $path = $resource->absolutePath();

        if ($path === null) {
            abort(404);
        }

        $rendered = Cache::remember(
            'html-resource:' . $resource->id . ':' . filemtime($path),
            now()->addDay(),
            fn () => $this->renderer->render(file_get_contents($path))
        );

        return response($rendered, 200, [
            'Content-Type'           => 'text/html; charset=UTF-8',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, no-store, max-age=0',
            'Referrer-Policy'        => 'no-referrer',
        ]);
    }

    private function findResource(string $id): CatalogueHtmlResource
    {
        $resource = CatalogueHtmlResource::with('catalogue')->find($id);

        if (!$resource) {
            abort(404);
        }

        return $resource;
    }

    private function currentUser()
    {
        return Auth::guard('api')->user() ?? Auth::guard('web')->user();
    }

    private function messageFor(?string $reason): string
    {
        return match ($reason) {
            HtmlResourceAccessService::REASON_LICENSE_REQUIRED => 'This document needs an access key.',
            HtmlResourceAccessService::REASON_LICENSE_EXPIRED  => 'Your access to this document has expired.',
            HtmlResourceAccessService::REASON_LICENSE_REVOKED  => 'Your access to this document has been withdrawn.',
            default => 'You do not have access to this document.',
        };
    }
}
