<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\HomeRecognizedPathway;
use App\Models\HomeCertificate;
use App\Helpers\MiaHelper;
use Illuminate\Http\Request;

class HomeFlagshipCertificationsController extends Controller
{
    /**
     * Show the form for editing the Flagship Certifications configurations.
     * Always edits the first record — creates one if it doesn't exist.
     */
    public function edit()
    {
        $homeRecognizedPathway = HomeRecognizedPathway::with('certificates')->firstOrCreate(
            [],
            [
                'title1' => 'Flagship Certifications',
            ]
        );

        return view('backend.layouts.home_flagship_certifications.edit', compact('homeRecognizedPathway'));
    }

    /**
     * Update the Flagship Certifications configurations.
     */
    public function update(Request $request, $id)
    {
        $homeRecognizedPathway = HomeRecognizedPathway::findOrFail($id);

        $request->validate([
            'title1'                         => 'required|string|max:255',
            'title2'                         => 'nullable|string|max:255',
            'tagline'                        => 'nullable|string|max:255',
            'description'                    => 'nullable|string',

            'certificates'                   => 'nullable|array',
            'certificates.*.title'           => 'required|string|max:255',
            'certificates.*.icon'            => 'nullable|file|mimes:svg,png,jpeg,jpg|max:2048',

            'content_file'                   => 'nullable|file|mimes:html,txt|max:10240',
            'injected_status'                => 'required|in:0,1',
        ]);

        $homeRecognizedPathway->update([
            'title1'                         => $request->title1,
            'title2'                         => $request->title2,
            'tagline'                        => $request->tagline,
            'description'                    => $request->description,
        ]);

        // Handle content file upload
        $contentFilePath = $homeRecognizedPathway->content_file;

        if ($request->remove_content_file == 1) {
            MiaHelper::deleteFile($homeRecognizedPathway->content_file);
            $contentFilePath = null;
        } elseif ($request->hasFile('content_file')) {
            $contentFilePath = MiaHelper::updateFile(
                $homeRecognizedPathway->content_file,
                $request->file('content_file'),
                'home_recognized_pathway'
            );
        }

        $homeRecognizedPathway->update([
            'content_file' => $contentFilePath,
            'injected_status' => $request->injected_status,
        ]);

        // Handle Certificates Repeater
        $submittedCertIds = [];
        if ($request->has('certificates')) {
            foreach ($request->certificates as $index => $itemData) {
                if (empty($itemData['title'])) {
                    continue;
                }

                $itemId = $itemData['id'] ?? null;
                $item   = null;

                if ($itemId) {
                    $item = HomeCertificate::find($itemId);
                }

                // Handle icon file upload
                $iconPath = $item ? $item->icon : null;
                $fileKey  = "certificates.{$index}.icon";

                if ($request->hasFile($fileKey)) {
                    $uploadedFile = $request->file($fileKey);
                    if ($item && $item->icon) {
                        $iconPath = MiaHelper::updateFile($item->icon, $uploadedFile, 'home_certificates');
                    } else {
                        $iconPath = MiaHelper::uploadFile($uploadedFile, 'home_certificates');
                    }
                }

                $fields = [
                    'short_title' => $itemData['short_title'] ?? null,
                    'title'       => $itemData['title'],
                    'icon'        => $iconPath,
                    'tagline'     => $itemData['tagline'] ?? null,
                    'headline'    => $itemData['headline'] ?? null,
                    'description' => $itemData['description'] ?? null,
                    'audience'    => $itemData['audience'] ?? null,
                    'tags'        => $itemData['tags'] ?? null,
                    'button_text' => $itemData['button_text'] ?? null,
                ];

                if ($item) {
                    $item->update($fields);
                    $submittedCertIds[] = $item->id;
                } else {
                    $newItem = $homeRecognizedPathway->certificates()->create($fields);
                    $submittedCertIds[] = $newItem->id;
                }
            }
        }

        // Delete removed items and their files
        $deletedItems = $homeRecognizedPathway->certificates()->whereNotIn('id', $submittedCertIds)->get();
        foreach ($deletedItems as $delItem) {
            if ($delItem->icon) {
                MiaHelper::deleteFile($delItem->icon);
            }
            $delItem->delete();
        }

        return redirect()->route('admin.home-flagship-certifications.edit')
            ->with('success', 'Home Page Flagship Certifications updated successfully.');
    }
}
