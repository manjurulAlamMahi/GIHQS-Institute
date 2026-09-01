<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\HomeGihq;
use App\Models\HomeServicesPathway;
use App\Models\HomeProfessionalPathway;
use App\Helpers\MiaHelper;
use Illuminate\Http\Request;

class HomeServicesPathwaysController extends Controller
{
    /**
     * Show the form for editing the Home Page configurations.
     * Always edits the first record — creates one if it doesn't exist.
     */
    public function edit()
    {
        $homeGihq = HomeGihq::with(['servicesPathways', 'professionalPathways', 'nextStep'])->firstOrCreate(
            [],
            [
                'title1' => 'Home GIHQ',
                'title2' => 'Professional Ecosystem',
            ]
        );

        if (!$homeGihq->nextStep) {
            $homeGihq->nextStep()->create([
                'title1' => 'Choose your next step',
            ]);
            $homeGihq->load('nextStep');
        }

        return view('backend.layouts.home_services_pathways.edit', compact('homeGihq'));
    }

    /**
     * Update the Home Page configurations.
     */
    public function update(Request $request, $id)
    {
        $homeGihq = HomeGihq::findOrFail($id);

        $request->validate([
            'title1'                         => 'required|string|max:255',
            'title2'                         => 'nullable|string|max:255',
            'tagline'                        => 'nullable|string|max:255',
            'description'                    => 'nullable|string',
            'certificate_btn_text'           => 'nullable|string|max:255',
            'learning_btn_text'              => 'nullable|string|max:255',
            'advisory_btn_text'              => 'nullable|string|max:255',
            'member_btn_text'                => 'nullable|string|max:255',

            'professional_ecosystem_title'   => 'nullable|string|max:255',
            'learning_tagline'               => 'nullable|string|max:255',
            'learning_title'                 => 'nullable|string|max:255',
            'learning_details'               => 'nullable|string',
            'certificate_tagline'            => 'nullable|string|max:255',
            'certificate_title'              => 'nullable|string|max:255',
            'certificate_details'            => 'nullable|string',
            'lead_tagline'                   => 'nullable|string|max:255',
            'lead_title'                     => 'nullable|string|max:255',
            'lead_details'                   => 'nullable|string',

            'services_pathways'              => 'nullable|array',
            'services_pathways.*.title'      => 'required|string|max:255',

            'professional_pathways'          => 'nullable|array',
            'professional_pathways.*.title'  => 'required|string|max:255',

            'next_step'                      => 'required|array',
            'next_step.title1'               => 'required|string|max:255',
            'next_step.title2'               => 'nullable|string|max:255',
            'next_step.tagline'              => 'nullable|string|max:255',
            'next_step.certificate_btn_text' => 'nullable|string|max:255',
            'next_step.learning_btn_text'    => 'nullable|string|max:255',
            'next_step.advisory_btn_text'    => 'nullable|string|max:255',
            'next_step.member_btn_text'      => 'nullable|string|max:255',

            'content_file'                   => 'nullable|file|mimes:html,txt|max:10240',
            'injected_status'                => 'required|in:0,1',
        ]);

        $homeGihq->update([
            'title1'                         => $request->title1,
            'title2'                         => $request->title2,
            'tagline'                        => $request->tagline,
            'description'                    => $request->description,
            'certificate_btn_text'           => $request->certificate_btn_text,
            'learning_btn_text'              => $request->learning_btn_text,
            'advisory_btn_text'              => $request->advisory_btn_text,
            'member_btn_text'                => $request->member_btn_text,

            'professional_ecosystem_title'   => $request->professional_ecosystem_title,
            'learning_tagline'               => $request->learning_tagline,
            'learning_title'                 => $request->learning_title,
            'learning_details'               => $request->learning_details,
            'certificate_tagline'            => $request->certificate_tagline,
            'certificate_title'              => $request->certificate_title,
            'certificate_details'            => $request->certificate_details,
            'lead_tagline'                   => $request->lead_tagline,
            'lead_title'                     => $request->lead_title,
            'lead_details'                   => $request->lead_details,
        ]);

        // Handle content file upload
        $contentFilePath = $homeGihq->content_file;

        if ($request->remove_content_file == 1) {
            MiaHelper::deleteFile($homeGihq->content_file);
            $contentFilePath = null;
        } elseif ($request->hasFile('content_file')) {
            $contentFilePath = MiaHelper::updateFile(
                $homeGihq->content_file,
                $request->file('content_file'),
                'home_gihq'
            );
        }

        $homeGihq->update([
            'content_file' => $contentFilePath,
            'injected_status' => $request->injected_status,
        ]);

        // Handle Choose Your Next Step section
        if ($request->has('next_step')) {
            $homeGihq->nextStep()->updateOrCreate(
                ['home_gihq_id' => $homeGihq->id],
                [
                    'title1'               => $request->next_step['title1'],
                    'title2'               => $request->next_step['title2'] ?? null,
                    'tagline'              => $request->next_step['tagline'] ?? null,
                    'certificate_btn_text' => $request->next_step['certificate_btn_text'] ?? null,
                    'learning_btn_text'    => $request->next_step['learning_btn_text'] ?? null,
                    'advisory_btn_text'    => $request->next_step['advisory_btn_text'] ?? null,
                    'member_btn_text'      => $request->next_step['member_btn_text'] ?? null,
                ]
            );
        }

        // Handle Services & Pathways Repeater
        $submittedServicesIds = [];
        if ($request->has('services_pathways')) {
            foreach ($request->services_pathways as $itemData) {
                if (empty($itemData['title'])) {
                    continue;
                }

                $itemId = $itemData['id'] ?? null;
                $item   = null;

                if ($itemId) {
                    $item = HomeServicesPathway::find($itemId);
                }

                if ($item) {
                    $item->update([
                        'serial'          => $itemData['serial'] ?? null,
                        'target_audience' => $itemData['target_audience'] ?? null,
                        'title'           => $itemData['title'],
                        'description'     => $itemData['description'] ?? null,
                        'link_text'       => $itemData['link_text'] ?? null,
                    ]);
                    $submittedServicesIds[] = $item->id;
                } else {
                    $newItem = $homeGihq->servicesPathways()->create([
                        'serial'          => $itemData['serial'] ?? null,
                        'target_audience' => $itemData['target_audience'] ?? null,
                        'title'           => $itemData['title'],
                        'description'     => $itemData['description'] ?? null,
                        'link_text'       => $itemData['link_text'] ?? null,
                    ]);
                    $submittedServicesIds[] = $newItem->id;
                }
            }
        }
        $homeGihq->servicesPathways()->whereNotIn('id', $submittedServicesIds)->delete();

        // Handle The GIHQS Professional Pathways Repeater
        $submittedProfIds = [];
        if ($request->has('professional_pathways')) {
            foreach ($request->professional_pathways as $itemData) {
                if (empty($itemData['title'])) {
                    continue;
                }

                $itemId = $itemData['id'] ?? null;
                $item   = null;

                if ($itemId) {
                    $item = HomeProfessionalPathway::find($itemId);
                }

                if ($item) {
                    $item->update([
                        'serial'      => $itemData['serial'] ?? null,
                        'title'       => $itemData['title'],
                        'description' => $itemData['description'] ?? null,
                        'link_text'   => $itemData['link_text'] ?? null,
                    ]);
                    $submittedProfIds[] = $item->id;
                } else {
                    $newItem = $homeGihq->professionalPathways()->create([
                        'serial'      => $itemData['serial'] ?? null,
                        'title'       => $itemData['title'],
                        'description' => $itemData['description'] ?? null,
                        'link_text'   => $itemData['link_text'] ?? null,
                    ]);
                    $submittedProfIds[] = $newItem->id;
                }
            }
        }
        $homeGihq->professionalPathways()->whereNotIn('id', $submittedProfIds)->delete();

        return redirect()->route('admin.home-services-pathways.edit')
            ->with('success', 'Home Page Services & Pathways updated successfully.');
    }
}
