<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\AccreditationEligibility;
use App\Models\AccreditationEligibilityFeature;
use App\Models\AccreditationProcess;
use App\Models\AccreditationProcessFeature;
use App\Models\AccreditationDomain;
use App\Models\AccreditationDomainFeature;
use App\Models\AccreditationInsight;
use App\Models\AccreditationInsightFeature;
use Illuminate\Http\Request;

class AccreditationDetailController extends Controller
{
    /**
     * Show the form for editing the Accreditation Details.
     */
    public function edit()
    {
        $eligibility = AccreditationEligibility::with('features')->firstOrCreate(
            [],
            [
                'title1' => 'Accreditation Eligibility',
                'title2' => 'Requirements',
                'description' => 'Healthcare organizations seeking accreditation must meet basic compliance and quality requirements.',
            ]
        );

        $process = AccreditationProcess::with('features')->firstOrCreate(
            [],
            [
                'title1' => 'Accreditation Process',
                'title2' => 'Steps to Get Accredited',
                'description' => 'Our accreditation process is structured into simple milestones.',
            ]
        );

        $domain = AccreditationDomain::with('features')->firstOrCreate(
            [],
            [
                'title1' => 'Accreditation Domains',
                'title2' => 'Assessment Fields',
                'description' => 'We measure quality standards across primary operational domains.',
            ]
        );

        $insight = AccreditationInsight::with('features')->firstOrCreate(
            [],
            [
                'title1' => 'Accreditation Insights',
                'title2' => 'Success & Impact Studies',
                'description' => 'Explore findings and statistics from recently accredited facilities.',
            ]
        );

        return view('backend.layouts.accreditation_detail.edit', compact(
            'eligibility',
            'process',
            'domain',
            'insight'
        ));
    }

    /**
     * Update the Accreditation Details.
     */
    public function update(Request $request, $id)
    {
        $eligibility = AccreditationEligibility::findOrFail($id);
        $process = AccreditationProcess::firstOrCreate([]);
        $domain = AccreditationDomain::firstOrCreate([]);
        $insight = AccreditationInsight::firstOrCreate([]);

        $request->validate([
            // Section 1: Eligibility
            'eligibility_title1'          => 'required|string|max:255',
            'eligibility_title2'          => 'nullable|string|max:255',
            'eligibility_description'     => 'nullable|string',
            'eligibility_features'         => 'nullable|array',
            'eligibility_features.*.title' => 'required|string|max:255',
            'eligibility_features.*.description' => 'nullable|string',

            // Section 2: Process
            'process_title1'              => 'required|string|max:255',
            'process_title2'              => 'nullable|string|max:255',
            'process_description'         => 'nullable|string',
            'process_features'            => 'nullable|array',
            'process_features.*.serial'   => 'nullable|string|max:255',
            'process_features.*.title'    => 'required|string|max:255',
            'process_features.*.subtitle' => 'nullable|string|max:255',
            'process_features.*.description' => 'nullable|string',

            // Section 3: Domain
            'domain_title1'               => 'required|string|max:255',
            'domain_title2'               => 'nullable|string|max:255',
            'domain_description'          => 'nullable|string',
            'domain_features'             => 'nullable|array',
            'domain_features.*.domain_serial' => 'nullable|string|max:255',
            'domain_features.*.title'     => 'required|string|max:255',
            'domain_features.*.description' => 'nullable|string',

            // Section 4: Insights
            'insight_title1'              => 'required|string|max:255',
            'insight_title2'              => 'nullable|string|max:255',
            'insight_description'         => 'nullable|string',
            'insight_features'            => 'nullable|array',
            'insight_features.*.title'    => 'required|string|max:255',
            'insight_features.*.tagline'  => 'nullable|string|max:255',
            'insight_features.*.description' => 'nullable|string',
        ]);

        // Update single-row records
        $eligibility->update([
            'title1'      => $request->eligibility_title1,
            'title2'      => $request->eligibility_title2,
            'description' => $request->eligibility_description,
        ]);

        $process->update([
            'title1'      => $request->process_title1,
            'title2'      => $request->process_title2,
            'description' => $request->process_description,
        ]);

        $domain->update([
            'title1'      => $request->domain_title1,
            'title2'      => $request->domain_title2,
            'description' => $request->domain_description,
        ]);

        $insight->update([
            'title1'      => $request->insight_title1,
            'title2'      => $request->insight_title2,
            'description' => $request->insight_description,
        ]);

        // 1. Sync Eligibility Features
        $submittedEligibilityFeatureIds = [];
        if ($request->has('eligibility_features')) {
            foreach ($request->eligibility_features as $item) {
                $feature = null;
                if (!empty($item['id'])) {
                    $feature = AccreditationEligibilityFeature::find($item['id']);
                }

                $fields = [
                    'title'       => $item['title'],
                    'description' => $item['description'],
                ];

                if ($feature) {
                    $feature->update($fields);
                    $submittedEligibilityFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $eligibility->features()->create($fields);
                    $submittedEligibilityFeatureIds[] = $newFeature->id;
                }
            }
        }
        $eligibility->features()->whereNotIn('id', $submittedEligibilityFeatureIds)->delete();

        // 2. Sync Process Features
        $submittedProcessFeatureIds = [];
        if ($request->has('process_features')) {
            foreach ($request->process_features as $item) {
                $feature = null;
                if (!empty($item['id'])) {
                    $feature = AccreditationProcessFeature::find($item['id']);
                }

                $fields = [
                    'serial'      => $item['serial'],
                    'title'       => $item['title'],
                    'subtitle'    => $item['subtitle'],
                    'description' => $item['description'],
                ];

                if ($feature) {
                    $feature->update($fields);
                    $submittedProcessFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $process->features()->create($fields);
                    $submittedProcessFeatureIds[] = $newFeature->id;
                }
            }
        }
        $process->features()->whereNotIn('id', $submittedProcessFeatureIds)->delete();

        // 3. Sync Domain Features
        $submittedDomainFeatureIds = [];
        if ($request->has('domain_features')) {
            foreach ($request->domain_features as $item) {
                $feature = null;
                if (!empty($item['id'])) {
                    $feature = AccreditationDomainFeature::find($item['id']);
                }

                $fields = [
                    'domain_serial' => $item['domain_serial'],
                    'title'         => $item['title'],
                    'description'   => $item['description'],
                ];

                if ($feature) {
                    $feature->update($fields);
                    $submittedDomainFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $domain->features()->create($fields);
                    $submittedDomainFeatureIds[] = $newFeature->id;
                }
            }
        }
        $domain->features()->whereNotIn('id', $submittedDomainFeatureIds)->delete();

        // 4. Sync Insights Features
        $submittedInsightFeatureIds = [];
        if ($request->has('insight_features')) {
            foreach ($request->insight_features as $item) {
                $feature = null;
                if (!empty($item['id'])) {
                    $feature = AccreditationInsightFeature::find($item['id']);
                }

                $fields = [
                    'title'       => $item['title'],
                    'tagline'     => $item['tagline'],
                    'description' => $item['description'],
                ];

                if ($feature) {
                    $feature->update($fields);
                    $submittedInsightFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $insight->features()->create($fields);
                    $submittedInsightFeatureIds[] = $newFeature->id;
                }
            }
        }
        $insight->features()->whereNotIn('id', $submittedInsightFeatureIds)->delete();

        return redirect()->back()->with('success', 'Accreditation Details updated successfully.');
    }
}
