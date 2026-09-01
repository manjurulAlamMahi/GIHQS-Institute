<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\VisionMissionValue;
use App\Helpers\MiaHelper;
use Illuminate\Http\Request;

class VisionMissionValueController extends Controller
{
    /**
     * Show the form for editing the Vision Mission Values page.
     * Always edits the first (only) record — creates one if it doesn't exist.
     */
    public function edit()
    {
        $vmv = VisionMissionValue::firstOrCreate([]);

        return view('backend.layouts.vision_mission_values.edit', compact('vmv'));
    }

    /**
     * Update the Vision Mission Values page.
     */
    public function update(Request $request, $id)
    {
        $vmv = VisionMissionValue::findOrFail($id);

        $request->validate([
            // General Section
            'tagline'                               => 'nullable|string|max:255',
            'title1'                                => 'nullable|string|max:255',
            'title2'                                => 'nullable|string|max:255',
            'short_description'                     => 'nullable|string',

            // Vision Section
            'vision_tagline'                        => 'nullable|string|max:255',
            'vision_title'                          => 'nullable|string|max:255',
            'vision_short_description'              => 'nullable|string',

            // Mission Section
            'mission_tagline'                       => 'nullable|string|max:255',
            'mission_title'                         => 'nullable|string|max:255',
            'mission_short_description'             => 'nullable|string',

            // Value Section
            'value_tagline'                         => 'nullable|string|max:255',
            'value_title'                           => 'nullable|string|max:255',
            'value_title2'                          => 'nullable|string|max:255',
            'value_short_description'               => 'nullable|string',

            // Global Perspective Section
            'global_perspective_tagline'            => 'nullable|string|max:255',
            'global_perspective_title'              => 'nullable|string|max:255',
            'global_perspective_short_description'  => 'nullable|string',

            // Integrity Section
            'integrity_tagline'                     => 'nullable|string|max:255',
            'integrity_title'                       => 'nullable|string|max:255',
            'integrity_short_description'           => 'nullable|string',

            // Human Centered Section
            'human_centered_tagline'                => 'nullable|string|max:255',
            'human_centered_title'                  => 'nullable|string|max:255',
            'human_centered_short_description'      => 'nullable|string',

            // Quality & Excellence Section
            'quality_excellence_tagline'            => 'nullable|string|max:255',
            'quality_excellence_title'              => 'nullable|string|max:255',
            'quality_excellence_short_description'  => 'nullable|string',

            // Safety Leadership Section
            'safety_leadership_tagline'             => 'nullable|string|max:255',
            'safety_leadership_title'               => 'nullable|string|max:255',
            'safety_leadership_short_description'   => 'nullable|string',

            'content_file'                          => 'nullable|file|mimes:html,txt|max:10240',
            'injected_status'                       => 'required|in:0,1',
        ]);

        $vmv->update([
            'tagline'                               => $request->tagline,
            'title1'                                => $request->title1,
            'title2'                                => $request->title2,
            'short_description'                     => $request->short_description,

            'vision_tagline'                        => $request->vision_tagline,
            'vision_title'                          => $request->vision_title,
            'vision_short_description'              => $request->vision_short_description,

            'mission_tagline'                       => $request->mission_tagline,
            'mission_title'                         => $request->mission_title,
            'mission_short_description'             => $request->mission_short_description,

            'value_tagline'                         => $request->value_tagline,
            'value_title'                           => $request->value_title,
            'value_title2'                          => $request->value_title2,
            'value_short_description'               => $request->value_short_description,

            'global_perspective_tagline'            => $request->global_perspective_tagline,
            'global_perspective_title'              => $request->global_perspective_title,
            'global_perspective_short_description'  => $request->global_perspective_short_description,

            'integrity_tagline'                     => $request->integrity_tagline,
            'integrity_title'                       => $request->integrity_title,
            'integrity_short_description'           => $request->integrity_short_description,

            'human_centered_tagline'                => $request->human_centered_tagline,
            'human_centered_title'                  => $request->human_centered_title,
            'human_centered_short_description'      => $request->human_centered_short_description,

            'quality_excellence_tagline'            => $request->quality_excellence_tagline,
            'quality_excellence_title'              => $request->quality_excellence_title,
            'quality_excellence_short_description'  => $request->quality_excellence_short_description,

            'safety_leadership_tagline'             => $request->safety_leadership_tagline,
            'safety_leadership_title'               => $request->safety_leadership_title,
            'safety_leadership_short_description'   => $request->safety_leadership_short_description,
        ]);

        // Handle content file upload
        $contentFilePath = $vmv->content_file;

        if ($request->remove_content_file == 1) {
            MiaHelper::deleteFile($vmv->content_file);
            $contentFilePath = null;
        } elseif ($request->hasFile('content_file')) {
            $contentFilePath = MiaHelper::updateFile(
                $vmv->content_file,
                $request->file('content_file'),
                'vision_mission_values'
            );
        }

        $vmv->update([
            'content_file' => $contentFilePath,
            'injected_status' => $request->injected_status,
        ]);

        return redirect()->route('admin.vision-mission-values.edit')
            ->with('success', 'Vision Mission Values updated successfully.');
    }
}
