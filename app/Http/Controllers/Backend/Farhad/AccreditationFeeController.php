<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\AccreditationFee;
use App\Models\AccreditationFeesPlan;
use App\Models\AccreditationFeesPlanFeature;
use Illuminate\Http\Request;

class AccreditationFeeController extends Controller
{
    /**
     * Show the form for editing the Accreditation Fees page config.
     */
    public function edit()
    {
        $fee = AccreditationFee::with(['plans.features'])->firstOrCreate(
            [],
            [
                'title1' => 'Accreditation Fees',
                'title2' => 'Transparent Pricing Plans',
                'description' => 'Select a package that best fits the size and scope of your clinical facility.',
            ]
        );

        return view('backend.layouts.accreditation_fee.edit', compact('fee'));
    }

    /**
     * Update the Accreditation Fees configuration.
     */
    public function update(Request $request, $id)
    {
        $fee = AccreditationFee::findOrFail($id);

        $request->validate([
            'title1'                              => 'required|string|max:255',
            'title2'                              => 'nullable|string|max:255',
            'description'                         => 'nullable|string',
            'plans'                               => 'nullable|array',
            'plans.*.title'                       => 'required|string|max:255',
            'plans.*.price'                       => 'nullable|string|max:255',
            'plans.*.description'                 => 'nullable|string',
            'plans.*.features'                    => 'nullable|array',
            'plans.*.features.*.feature'          => 'required|string|max:1000',
        ]);

        // Update main config
        $fee->update([
            'title1'      => $request->title1,
            'title2'      => $request->title2,
            'description' => $request->description,
        ]);

        // Sync Plans repeater
        $submittedPlanIds = [];

        if ($request->has('plans')) {
            foreach ($request->plans as $planData) {
                // Find existing or create new
                $plan = null;
                if (!empty($planData['id'])) {
                    $plan = AccreditationFeesPlan::find($planData['id']);
                }

                $planFields = [
                    'title'       => $planData['title'],
                    'price'       => $planData['price'] ?? null,
                    'description' => $planData['description'] ?? null,
                ];

                if ($plan) {
                    $plan->update($planFields);
                } else {
                    $plan = $fee->plans()->create($planFields);
                }

                $submittedPlanIds[] = $plan->id;

                // Sync nested Plan Features
                $submittedFeatureIds = [];

                if (!empty($planData['features']) && is_array($planData['features'])) {
                    foreach ($planData['features'] as $featureData) {
                        $feature = null;
                        if (!empty($featureData['id'])) {
                            $feature = AccreditationFeesPlanFeature::find($featureData['id']);
                        }

                        $featureFields = [
                            'feature' => $featureData['feature'],
                        ];

                        if ($feature) {
                            $feature->update($featureFields);
                        } else {
                            $feature = $plan->features()->create($featureFields);
                        }

                        $submittedFeatureIds[] = $feature->id;
                    }
                }

                // Delete removed features
                $plan->features()->whereNotIn('id', $submittedFeatureIds)->delete();
            }
        }

        // Delete removed plans (cascade deletes features)
        $fee->plans()->whereNotIn('id', $submittedPlanIds)->delete();

        return redirect()->back()->with('success', 'Accreditation Fees updated successfully.');
    }
}
