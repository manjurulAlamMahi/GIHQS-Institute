<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\AccreditationApplyHero;
use App\Models\AccreditationEligibilitySnapshot;
use App\Models\AccreditationEligibilitySnapshotFeature;
use Illuminate\Http\Request;

class AccreditationApplyHeroController extends Controller
{
    /**
     * Show the edit form.
     */
    public function edit()
    {
        $hero = AccreditationApplyHero::firstOrCreate(
            [],
            [
                'title1'      => 'Apply for Accreditation',
                'title2'      => 'Start Your Journey',
                'tagline'     => 'GIHQS ACCREDITATION',
                'description' => 'Begin your path to global quality recognition.',
                'note'        => 'Applications are reviewed within 5–7 business days.',
            ]
        );

        $snapshot = AccreditationEligibilitySnapshot::with('features')->firstOrCreate(
            [],
            [
                'title'       => 'Eligibility Snapshot',
                'description' => 'Check whether your facility meets the basic eligibility criteria before applying.',
            ]
        );

        return view('backend.layouts.accreditation_apply_hero.edit', compact('hero', 'snapshot'));
    }

    /**
     * Update the page configuration.
     */
    public function update(Request $request, $id)
    {
        $hero     = AccreditationApplyHero::findOrFail($id);
        $snapshot = AccreditationEligibilitySnapshot::firstOrCreate([]);

        $request->validate([
            // Hero section
            'title1'                              => 'required|string|max:255',
            'title2'                              => 'nullable|string|max:255',
            'tagline'                             => 'nullable|string|max:255',
            'description'                         => 'nullable|string',
            'note'                                => 'nullable|string',

            // Snapshot section
            'snapshot_title'                      => 'nullable|string|max:255',
            'snapshot_description'                => 'nullable|string',
            'features'                            => 'nullable|array',
            'features.*.keypoints'                => 'nullable|string|max:255',
            'features.*.details'                  => 'nullable|string',
        ]);

        // Update hero
        $hero->update([
            'title1'      => $request->title1,
            'title2'      => $request->title2,
            'tagline'     => $request->tagline,
            'description' => $request->description,
            'note'        => $request->note,
        ]);

        // Update snapshot header
        $snapshot->update([
            'title'       => $request->snapshot_title,
            'description' => $request->snapshot_description,
        ]);

        // Sync snapshot features repeater
        $submittedIds = [];

        if ($request->has('features')) {
            foreach ($request->features as $featureData) {
                $feature = null;
                if (!empty($featureData['id'])) {
                    $feature = AccreditationEligibilitySnapshotFeature::find($featureData['id']);
                }

                $fields = [
                    'keypoints' => $featureData['keypoints'] ?? null,
                    'details'   => $featureData['details']   ?? null,
                ];

                if ($feature) {
                    $feature->update($fields);
                    $submittedIds[] = $feature->id;
                } else {
                    $new = $snapshot->features()->create($fields);
                    $submittedIds[] = $new->id;
                }
            }
        }

        $snapshot->features()->whereNotIn('id', $submittedIds)->delete();

        return redirect()->back()->with('success', 'Apply Accreditation Hero updated successfully.');
    }
}
