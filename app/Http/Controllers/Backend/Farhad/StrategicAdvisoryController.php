<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\StrategicAdvisory;
use App\Models\StrategicAdvisoryFeature;
use App\Helpers\MiaHelper;
use Illuminate\Http\Request;

class StrategicAdvisoryController extends Controller
{
    /**
     * Show the form for editing the Strategic Advisory page.
     * Always edits the first (only) record — creates one if it doesn't exist.
     */
    public function edit()
    {
        $strategicAdvisory = StrategicAdvisory::with('features')->firstOrCreate(
            [],
            ['title1' => 'Strategic Advisory Panel']
        );

        return view('backend.layouts.strategic_advisory.edit', compact('strategicAdvisory'));
    }

    /**
     * Update the Strategic Advisory page.
     */
    public function update(Request $request, $id)
    {
        $strategicAdvisory = StrategicAdvisory::findOrFail($id);

        $request->validate([
            'title1'                         => 'required|string|max:255',
            'title2'                         => 'nullable|string|max:255',
            'tagline'                        => 'nullable|string|max:255',
            'short_description'              => 'nullable|string',

            'purpose_tagline'                => 'nullable|string|max:255',
            'purpose_title'                  => 'nullable|string|max:255',
            'purpose_short_description'      => 'nullable|string',

            'advisory_title'                 => 'nullable|string|max:255',

            'panel_title'                    => 'nullable|string|max:255',
            'panel_short_description'        => 'nullable|string',

            'appointment_title'              => 'nullable|string|max:255',
            'appointment_short_description'  => 'nullable|string',

            'conflict_title'                 => 'nullable|string|max:255',
            'conflict_short_description'     => 'nullable|string',

            'expression_title'               => 'nullable|string|max:255',
            'expression_description'         => 'nullable|string',

            'features'                       => 'nullable|array',
            'features.*.description'          => 'required|string',

            'content_file' => 'nullable|file|mimes:html,txt|max:10240',
            'injected_status' => 'required|in:0,1',
        ]);

        $strategicAdvisory->update([
            'title1'                         => $request->title1,
            'title2'                         => $request->title2,
            'tagline'                        => $request->tagline,
            'short_description'              => $request->short_description,

            'purpose_tagline'                => $request->purpose_tagline,
            'purpose_title'                  => $request->purpose_title,
            'purpose_short_description'      => $request->purpose_short_description,

            'advisory_title'                 => $request->advisory_title,

            'panel_title'                    => $request->panel_title,
            'panel_short_description'        => $request->panel_short_description,

            'appointment_title'              => $request->appointment_title,
            'appointment_short_description'  => $request->appointment_short_description,

            'conflict_title'                 => $request->conflict_title,
            'conflict_short_description'     => $request->conflict_short_description,

            'expression_title'               => $request->expression_title,
            'expression_description'         => $request->expression_description,
        ]);

        // Handle content file upload
        $contentFilePath = $strategicAdvisory->content_file;

        if ($request->remove_content_file == 1) {
            MiaHelper::deleteFile($strategicAdvisory->content_file);
            $contentFilePath = null;
        } elseif ($request->hasFile('content_file')) {
            $contentFilePath = MiaHelper::updateFile(
                $strategicAdvisory->content_file,
                $request->file('content_file'),
                'strategic_advisory'
            );
        }

        $strategicAdvisory->update([
            'content_file' => $contentFilePath,
            'injected_status' => $request->injected_status,
        ]);

        // Handle Advisory Features Repeater
        $submittedFeatureIds = [];

        if ($request->has('features')) {
            foreach ($request->features as $featureData) {
                if (empty($featureData['description'])) {
                    continue;
                }

                $featureId = $featureData['id'] ?? null;
                $feature   = null;

                if ($featureId) {
                    $feature = StrategicAdvisoryFeature::find($featureId);
                }

                if ($feature) {
                    $feature->update([
                        'description' => $featureData['description'],
                    ]);
                    $submittedFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $strategicAdvisory->features()->create([
                        'description' => $featureData['description'],
                    ]);
                    $submittedFeatureIds[] = $newFeature->id;
                }
            }
        }

        // Delete features removed from the repeater
        $strategicAdvisory->features()->whereNotIn('id', $submittedFeatureIds)->delete();

        return redirect()->route('admin.strategic-advisory.edit')
            ->with('success', 'Strategic Advisory updated successfully.');
    }
}
