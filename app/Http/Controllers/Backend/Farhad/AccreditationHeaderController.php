<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\AccreditationHeader;
use App\Models\AccreditationTag;
use App\Models\AccreditationKeyfact;
use App\Helpers\MiaHelper;
use Illuminate\Http\Request;

class AccreditationHeaderController extends Controller
{
    /**
     * Show the form for editing the Accreditation Header page config.
     */
    public function edit()
    {
        $header = AccreditationHeader::with(['tags', 'keyfacts'])->firstOrCreate(
            [],
            [
                'title1' => 'Accreditation Header',
                'title2' => 'Information',
                'tagline' => 'GIHQS ACCREDITATION',
                'description' => 'Get accredited by the Global Institute for Healthcare Quality and Safety.',
                'note' => 'Important: All applications must be submitted before the deadline.',
                'apply_btn_text' => 'Apply Now',
                'download_btn_text' => 'Download Brochure',
                'download_file' => null,
                'content_file' => null,
                'injected_status' => 1,
            ]
        );

        return view('backend.layouts.accreditation_header.edit', compact('header'));
    }

    /**
     * Update the Accreditation Header configuration.
     */
    public function update(Request $request, $id)
    {
        $header = AccreditationHeader::findOrFail($id);

        $request->validate([
            'title1'            => 'required|string|max:255',
            'title2'            => 'nullable|string|max:255',
            'tagline'           => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'note'              => 'nullable|string',
            'apply_btn_text'    => 'nullable|string|max:255',
            'download_btn_text' => 'nullable|string|max:255',
            'download_file'     => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            
            // Content Injection
            'content_file'      => 'nullable|file|mimes:html,txt|max:10240',
            'injected_status'   => 'required|in:0,1',
            
            // Repeaters
            'tags'              => 'nullable|array',
            'tags.*.tagname'    => 'required|string|max:255',
            
            'keyfacts'          => 'nullable|array',
            'keyfacts.*.title'  => 'required|string|max:255',
            'keyfacts.*.subtitle' => 'nullable|string|max:255',
        ]);

        // File upload handling using MiaHelper
        $download_file_path = $header->download_file;
        if ($request->hasFile('download_file')) {
            if ($header->download_file) {
                MiaHelper::deleteFile($header->download_file);
            }
            $download_file_path = MiaHelper::uploadFile($request->file('download_file'), 'accreditation');
        } elseif ($request->boolean('remove_download_file')) {
            if ($header->download_file) {
                MiaHelper::deleteFile($header->download_file);
            }
            $download_file_path = null;
        }

        // Handle Content File injection
        $contentFilePath = $header->content_file;
        if ($request->remove_content_file == 1) {
            if ($header->content_file) {
                MiaHelper::deleteFile($header->content_file);
            }
            $contentFilePath = null;
        } elseif ($request->hasFile('content_file')) {
            $contentFilePath = MiaHelper::updateFile(
                $header->content_file,
                $request->file('content_file'),
                'accreditation'
            );
        }

        // Update main header record
        $header->update([
            'title1'            => $request->title1,
            'title2'            => $request->title2,
            'tagline'           => $request->tagline,
            'description'       => $request->description,
            'note'              => $request->note,
            'apply_btn_text'    => $request->apply_btn_text,
            'download_btn_text' => $request->download_btn_text,
            'download_file'     => $download_file_path,
            'content_file'      => $contentFilePath,
            'injected_status'   => $request->injected_status,
        ]);

        // Sync Tags Repeater
        $submittedTagIds = [];
        if ($request->has('tags')) {
            foreach ($request->tags as $item) {
                $tag = null;
                if (!empty($item['id'])) {
                    $tag = AccreditationTag::find($item['id']);
                }

                $fields = [
                    'tagname' => $item['tagname'],
                ];

                if ($tag) {
                    $tag->update($fields);
                    $submittedTagIds[] = $tag->id;
                } else {
                    $newTag = $header->tags()->create($fields);
                    $submittedTagIds[] = $newTag->id;
                }
            }
        }
        $header->tags()->whereNotIn('id', $submittedTagIds)->delete();

        // Sync Keyfacts Repeater
        $submittedKeyfactIds = [];
        if ($request->has('keyfacts')) {
            foreach ($request->keyfacts as $item) {
                $keyfact = null;
                if (!empty($item['id'])) {
                    $keyfact = AccreditationKeyfact::find($item['id']);
                }

                $fields = [
                    'title'    => $item['title'],
                    'subtitle' => $item['subtitle'],
                ];

                if ($keyfact) {
                    $keyfact->update($fields);
                    $submittedKeyfactIds[] = $keyfact->id;
                } else {
                    $newKeyfact = $header->keyfacts()->create($fields);
                    $submittedKeyfactIds[] = $newKeyfact->id;
                }
            }
        }
        $header->keyfacts()->whereNotIn('id', $submittedKeyfactIds)->delete();

        return redirect()->back()->with('success', 'Accreditation Header updated successfully.');
    }
}
