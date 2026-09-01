<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\AdvisoryHeader;
use App\Models\AdvisoryFocus;
use App\Models\AdvisoryFocusFeature;
use App\Models\AdvisoryScope;
use App\Models\AdvisoryScopeFeature;
use App\Models\AdvisoryDeliverableCard;
use App\Models\AdvisoryDeliverableCardFeature;
use App\Models\AdvisoryService;
use App\Models\AdvisoryServiceFeature;
use App\Models\AdvisoryDiscussCard;
use App\Helpers\MiaHelper;
use Illuminate\Http\Request;

class AdvisoryServicesController extends Controller
{
    /**
     * Show the form for editing the Advisory Services page config.
     * Loads/creates all single records.
     */
    public function edit()
    {
        $header = AdvisoryHeader::firstOrCreate(
            [],
            [
                'title1' => 'Advisory Panel',
                'title2' => 'Header',
                'tagline' => 'GIHQS ADVISORY',
                'description' => 'The Advisory Panel provides independent guidance to support the long-term vision, strategic direction, and institutional development of the Global Institute for Healthcare Quality and Safety (GIHQS).',
            ]
        );

        $focus = AdvisoryFocus::with('features')->firstOrCreate(
            [],
            [
                'title' => 'Focus & Features',
                'description' => 'GIHQS advisory focus is built on years of excellence in healthcare governance and patient safety implementation.',
            ]
        );

        $scope = AdvisoryScope::with('features')->firstOrCreate(
            [],
            [
                'title1' => 'Advisory Scope',
                'title2' => 'Strategic Fields',
                'description' => 'Our scope encompasses primary pillars of global health standards and governance frameworks.',
            ]
        );

        $deliverable = AdvisoryDeliverableCard::with('features')->firstOrCreate(
            [],
            [
                'title1' => 'Deliverable Card',
                'title2' => 'Key Outcomes',
                'description' => 'Key milestones and reports to ensure structured progress and execution.',
            ]
        );

        $service = AdvisoryService::with('features')->firstOrCreate(
            [],
            [
                'title1' => 'Service Packages',
                'title2' => 'Consulting Plans',
                'description' => 'Packages designed to fit different organizational scales and target priorities.',
            ]
        );

        $discuss = AdvisoryDiscussCard::firstOrCreate(
            [],
            [
                'title1' => 'Discuss Card',
                'title2' => 'Get in Touch',
                'description' => 'Connect with a GIHQS Advisory Panel coordinator to detail custom plans.',
                'button_text' => 'Start Discussion',
            ]
        );

        return view('backend.layouts.advisory_services.edit', compact(
            'header',
            'focus',
            'scope',
            'deliverable',
            'service',
            'discuss'
        ));
    }

    /**
     * Update the Advisory Services configuration.
     */
    public function update(Request $request, $id)
    {
        $header = AdvisoryHeader::findOrFail($id);
        $focus = AdvisoryFocus::firstOrCreate([]);
        $scope = AdvisoryScope::firstOrCreate([]);
        $deliverable = AdvisoryDeliverableCard::firstOrCreate([]);
        $service = AdvisoryService::firstOrCreate([]);
        $discuss = AdvisoryDiscussCard::firstOrCreate([]);

        $request->validate([
            // Section 1
            'header_title1'          => 'required|string|max:255',
            'header_title2'          => 'nullable|string|max:255',
            'header_tagline'         => 'nullable|string|max:255',
            'header_description'     => 'nullable|string',

            // Section 2
            'focus_title'            => 'nullable|string|max:255',
            'focus_description'      => 'nullable|string',
            'focus_features'         => 'nullable|array',
            'focus_features.*.description' => 'required|string',

            // Section 3
            'scope_title1'           => 'required|string|max:255',
            'scope_title2'           => 'nullable|string|max:255',
            'scope_description'      => 'nullable|string',
            'scope_features'         => 'nullable|array',
            'scope_features.*.title' => 'required|string|max:255',
            'scope_features.*.icon'  => 'nullable|file|mimes:jpeg,png,jpg,svg|max:2048',
            'scope_features.*.description' => 'nullable|string',

            // Section 4
            'deliverable_title1'     => 'required|string|max:255',
            'deliverable_title2'     => 'nullable|string|max:255',
            'deliverable_description'=> 'nullable|string',
            'deliverable_features'   => 'nullable|array',
            'deliverable_features.*.name' => 'required|string|max:255',

            // Section 5
            'service_title1'         => 'required|string|max:255',
            'service_title2'         => 'nullable|string|max:255',
            'service_description'    => 'nullable|string',
            'service_features'       => 'nullable|array',
            'service_features.*.serial_number' => 'nullable|string|max:255',
            'service_features.*.tagline'       => 'nullable|string|max:255',
            'service_features.*.title'         => 'required|string|max:255',
            'service_features.*.description'   => 'nullable|string',

            // Section 6
            'discuss_title1'         => 'required|string|max:255',
            'discuss_title2'         => 'nullable|string|max:255',
            'discuss_description'    => 'nullable|string',
            'discuss_btn_text'       => 'nullable|string|max:255',

            // Content Injection
            'content_file'           => 'nullable|file|mimes:html,txt|max:10240',
            'injected_status'        => 'required|in:0,1',
        ]);

        // Update single-row records
        $header->update([
            'title1'      => $request->header_title1,
            'title2'      => $request->header_title2,
            'tagline'     => $request->header_tagline,
            'description' => $request->header_description,
        ]);

        $focus->update([
            'title'       => $request->focus_title,
            'description' => $request->focus_description,
        ]);

        $scope->update([
            'title1'      => $request->scope_title1,
            'title2'      => $request->scope_title2,
            'description' => $request->scope_description,
        ]);

        $deliverable->update([
            'title1'      => $request->deliverable_title1,
            'title2'      => $request->deliverable_title2,
            'description' => $request->deliverable_description,
        ]);

        $service->update([
            'title1'      => $request->service_title1,
            'title2'      => $request->service_title2,
            'description' => $request->service_description,
        ]);

        $discuss->update([
            'title1'      => $request->discuss_title1,
            'title2'      => $request->discuss_title2,
            'description' => $request->discuss_description,
            'button_text' => $request->discuss_btn_text,
        ]);

        // Handle Content File injection
        $contentFilePath = $header->content_file;

        if ($request->remove_content_file == 1) {
            MiaHelper::deleteFile($header->content_file);
            $contentFilePath = null;
        } elseif ($request->hasFile('content_file')) {
            $contentFilePath = MiaHelper::updateFile(
                $header->content_file,
                $request->file('content_file'),
                'advisory_overview'
            );
        }

        $header->update([
            'content_file'    => $contentFilePath,
            'injected_status' => $request->injected_status,
        ]);

        // 1. Sync Focus Features Repeater
        $submittedFocusFeatureIds = [];
        if ($request->has('focus_features')) {
            foreach ($request->focus_features as $featureData) {
                if (empty($featureData['description'])) {
                    continue;
                }
                $featureId = $featureData['id'] ?? null;
                $feature   = null;
                if ($featureId) {
                    $feature = AdvisoryFocusFeature::find($featureId);
                }

                if ($feature) {
                    $feature->update([
                        'description' => $featureData['description'],
                    ]);
                    $submittedFocusFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $focus->features()->create([
                        'description' => $featureData['description'],
                    ]);
                    $submittedFocusFeatureIds[] = $newFeature->id;
                }
            }
        }
        $focus->features()->whereNotIn('id', $submittedFocusFeatureIds)->delete();

        // 2. Sync Scope Features Repeater
        $submittedScopeFeatureIds = [];
        if ($request->has('scope_features')) {
            foreach ($request->scope_features as $index => $featureData) {
                if (empty($featureData['title'])) {
                    continue;
                }
                $featureId = $featureData['id'] ?? null;
                $feature   = null;
                if ($featureId) {
                    $feature = AdvisoryScopeFeature::find($featureId);
                }

                // Handle icon file upload
                $iconPath = $feature ? $feature->icon : null;
                $fileKey = "scope_features.{$index}.icon";

                if ($request->hasFile($fileKey)) {
                    $uploadedFile = $request->file($fileKey);
                    if ($feature && $feature->icon) {
                        $iconPath = MiaHelper::updateFile($feature->icon, $uploadedFile, 'advisory_scope');
                    } else {
                        $iconPath = MiaHelper::uploadFile($uploadedFile, 'advisory_scope');
                    }
                }

                $fields = [
                    'icon'        => $iconPath,
                    'title'       => $featureData['title'],
                    'description' => $featureData['description'] ?? null,
                ];

                if ($feature) {
                    $feature->update($fields);
                    $submittedScopeFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $scope->features()->create($fields);
                    $submittedScopeFeatureIds[] = $newFeature->id;
                }
            }
        }
        // Delete features removed from the repeater and their associated icons from storage
        $featuresToDelete = $scope->features()->whereNotIn('id', $submittedScopeFeatureIds)->get();
        foreach ($featuresToDelete as $delFeature) {
            if ($delFeature->icon) {
                MiaHelper::deleteFile($delFeature->icon);
            }
            $delFeature->delete();
        }

        // 3. Sync Deliverable Features Repeater
        $submittedDeliverableFeatureIds = [];
        if ($request->has('deliverable_features')) {
            foreach ($request->deliverable_features as $featureData) {
                if (empty($featureData['name'])) {
                    continue;
                }
                $featureId = $featureData['id'] ?? null;
                $feature   = null;
                if ($featureId) {
                    $feature = AdvisoryDeliverableCardFeature::find($featureId);
                }

                $fields = [
                    'name' => $featureData['name'],
                ];

                if ($feature) {
                    $feature->update($fields);
                    $submittedDeliverableFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $deliverable->features()->create($fields);
                    $submittedDeliverableFeatureIds[] = $newFeature->id;
                }
            }
        }
        $deliverable->features()->whereNotIn('id', $submittedDeliverableFeatureIds)->delete();

        // 4. Sync Service Features Repeater
        $submittedServiceFeatureIds = [];
        if ($request->has('service_features')) {
            foreach ($request->service_features as $featureData) {
                if (empty($featureData['title'])) {
                    continue;
                }
                $featureId = $featureData['id'] ?? null;
                $feature   = null;
                if ($featureId) {
                    $feature = AdvisoryServiceFeature::find($featureId);
                }

                $fields = [
                    'serial_number' => $featureData['serial_number'] ?? null,
                    'tagline'       => $featureData['tagline'] ?? null,
                    'title'         => $featureData['title'],
                    'description'   => $featureData['description'] ?? null,
                ];

                if ($feature) {
                    $feature->update($fields);
                    $submittedServiceFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $service->features()->create($fields);
                    $submittedServiceFeatureIds[] = $newFeature->id;
                }
            }
        }
        $service->features()->whereNotIn('id', $submittedServiceFeatureIds)->delete();

        return redirect()->route('admin.advisory-services.edit')
            ->with('success', 'Advisory Services updated successfully.');
    }
}
