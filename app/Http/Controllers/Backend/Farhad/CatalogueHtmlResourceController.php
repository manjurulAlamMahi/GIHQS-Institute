<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Helpers\MiaHelper;
use App\Http\Controllers\Controller;
use App\Models\Catalogue;
use App\Models\CatalogueHtmlResource;
use App\Models\HtmlResourceLicense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin CRUD for the standalone HTML documents attached to a catalogue.
 *
 * Uploaded files are stored verbatim and never rewritten - the navigation
 * bootstrap is added at request time by HtmlResourceController, so the original
 * document is always recoverable and re-processing never needs a re-upload.
 */
class CatalogueHtmlResourceController extends Controller
{
    private const STORAGE_DIRECTORY = 'html-resources';

    public function index(Catalogue $catalogue): View
    {
        return view('backend.farhad.catalogue-html-resources.index', [
            'catalogue' => $catalogue,
            'resources' => $catalogue->htmlResources()->with('licenses.user')->get(),
            'kinds'     => CatalogueHtmlResource::KINDS,
        ]);
    }

    /**
     * Withdraw one user's licence. Takes effect on their next ticket request,
     * which is at most one minute away.
     */
    public function revokeLicense(HtmlResourceLicense $htmlResourceLicense): RedirectResponse
    {
        $htmlResourceLicense->update(['revoked_at' => now()]);

        return redirect()
            ->route('admin.catalogue-html-resources.index', $htmlResourceLicense->resource->catalogue_id)
            ->with('success', 'Access revoked.');
    }

    /**
     * Restore a licence that was previously withdrawn.
     */
    public function restoreLicense(HtmlResourceLicense $htmlResourceLicense): RedirectResponse
    {
        $htmlResourceLicense->update(['revoked_at' => null]);

        return redirect()
            ->route('admin.catalogue-html-resources.index', $htmlResourceLicense->resource->catalogue_id)
            ->with('success', 'Access restored.');
    }

    public function store(Request $request, Catalogue $catalogue): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'kind'       => 'required|string|in:' . implode(',', CatalogueHtmlResource::KINDS),
            'file'       => 'required|file|mimes:html,txt|max:10240',
            'is_public'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            // Leaving the key blank means the document needs no licence.
            'access_key'            => 'nullable|string|max:255',
            'license_validity_days' => 'nullable|integer|min:1|max:3650',
        ]);

        CatalogueHtmlResource::create([
            'catalogue_id' => $catalogue->id,
            'title'        => $validated['title'],
            'kind'         => $validated['kind'],
            'file_path'    => MiaHelper::uploadFile($request->file('file'), self::STORAGE_DIRECTORY),
            'is_public'    => (bool) ($validated['is_public'] ?? false),
            'sort_order'   => (int) ($validated['sort_order'] ?? 0),
            // A blank key leaves the document ungated.
            'access_key'            => filled($validated['access_key'] ?? null) ? $validated['access_key'] : null,
            'license_validity_days' => $validated['license_validity_days'] ?? null,
        ]);

        return redirect()
            ->route('admin.catalogue-html-resources.index', $catalogue->id)
            ->with('success', 'HTML document uploaded successfully.');
    }

    public function update(Request $request, CatalogueHtmlResource $htmlResource): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'kind'       => 'required|string|in:' . implode(',', CatalogueHtmlResource::KINDS),
            'file'       => 'nullable|file|mimes:html,txt|max:10240',
            'is_public'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'access_key'            => 'nullable|string|max:255',
            'license_validity_days' => 'nullable|integer|min:1|max:3650',
        ]);

        $attributes = [
            'title'      => $validated['title'],
            'kind'       => $validated['kind'],
            'is_public'  => (bool) ($validated['is_public'] ?? false),
            'sort_order' => (int) ($validated['sort_order'] ?? $htmlResource->sort_order),
            'access_key'            => filled($validated['access_key'] ?? null) ? $validated['access_key'] : null,
            'license_validity_days' => $validated['license_validity_days'] ?? null,
        ];

        // Replacing the file is optional; when omitted the stored document and
        // its cached rendering are left untouched.
        if ($request->hasFile('file')) {
            MiaHelper::deleteFile($htmlResource->file_path);
            $attributes['file_path'] = MiaHelper::uploadFile($request->file('file'), self::STORAGE_DIRECTORY);
        }

        $htmlResource->update($attributes);

        return redirect()
            ->route('admin.catalogue-html-resources.index', $htmlResource->catalogue_id)
            ->with('success', 'HTML document updated successfully.');
    }

    public function destroy(CatalogueHtmlResource $htmlResource): RedirectResponse
    {
        $catalogueId = $htmlResource->catalogue_id;

        MiaHelper::deleteFile($htmlResource->file_path);
        $htmlResource->delete();

        return redirect()
            ->route('admin.catalogue-html-resources.index', $catalogueId)
            ->with('success', 'HTML document deleted successfully.');
    }
}
