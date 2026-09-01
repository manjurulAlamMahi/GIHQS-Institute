<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\PoliciesGovernance;
use App\Models\PoliciesGovernanceDocument;
use App\Helpers\MiaHelper;
use Illuminate\Http\Request;

class PoliciesGovernanceController extends Controller
{
    /**
     * Show the form for editing the Policies & Governance page.
     * Always edits the first (only) record — creates one if it doesn't exist.
     */
    public function edit()
    {
        $policiesGovernance = PoliciesGovernance::with('documents')->firstOrCreate(
            [],
            [
                'title1' => 'Policies &',
                'title2' => 'Governance',
                'tagline' => 'GIHQS GOVERNANCE FRAMEWORK',
            ]
        );

        return view('backend.layouts.policies_governance.edit', compact('policiesGovernance'));
    }

    /**
     * Update the Policies & Governance page.
     */
    public function update(Request $request, $id)
    {
        $policiesGovernance = PoliciesGovernance::findOrFail($id);

        $request->validate([
            'title1'                   => 'required|string|max:255',
            'title2'                   => 'nullable|string|max:255',
            'tagline'                  => 'nullable|string|max:255',
            'description'              => 'nullable|string',

            'inst_title'               => 'nullable|string|max:255',
            'inst_tag'                 => 'nullable|string|max:255',
            'inst_description'         => 'nullable|string',

            'cert_title'               => 'nullable|string|max:255',
            'cert_tag'                 => 'nullable|string|max:255',
            'cert_description'         => 'nullable|string',

            'acc_title'                => 'nullable|string|max:255',
            'acc_tag'                  => 'nullable|string|max:255',
            'acc_description'          => 'nullable|string',

            'commitment_title1'        => 'nullable|string|max:255',
            'commitment_title2'        => 'nullable|string|max:255',
            'commitment_description'    => 'nullable|string',

            // Institutional Policies Validation
            'inst_policies'            => 'nullable|array',
            'inst_policies.*.title'    => 'required|string|max:255',
            'inst_policies.*.file'     => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt|max:10240',

            // Certification Policies Validation
            'cert_policies'            => 'nullable|array',
            'cert_policies.*.title'    => 'required|string|max:255',
            'cert_policies.*.file'     => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt|max:10240',

            'acc_policies'             => 'nullable|array',
            'acc_policies.*.title'     => 'required|string|max:255',
            'acc_policies.*.file'      => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt|max:10240',

            'content_file' => 'nullable|file|mimes:html,txt|max:10240',
            'injected_status' => 'required|in:0,1',
        ]);

        $policiesGovernance->update([
            'title1'                   => $request->title1,
            'title2'                   => $request->title2,
            'tagline'                  => $request->tagline,
            'description'              => $request->description,

            'inst_title'               => $request->inst_title,
            'inst_tag'                 => $request->inst_tag,
            'inst_description'         => $request->inst_description,

            'cert_title'               => $request->cert_title,
            'cert_tag'                 => $request->cert_tag,
            'cert_description'         => $request->cert_description,

            'acc_title'                => $request->acc_title,
            'acc_tag'                  => $request->acc_tag,
            'acc_description'          => $request->acc_description,

            'commitment_title1'        => $request->commitment_title1,
            'commitment_title2'        => $request->commitment_title2,
            'commitment_description'    => $request->commitment_description,
        ]);

        // Handle content file upload
        $contentFilePath = $policiesGovernance->content_file;

        if ($request->remove_content_file == 1) {
            MiaHelper::deleteFile($policiesGovernance->content_file);
            $contentFilePath = null;
        } elseif ($request->hasFile('content_file')) {
            $contentFilePath = MiaHelper::updateFile(
                $policiesGovernance->content_file,
                $request->file('content_file'),
                'policies_governance'
            );
        }

        $policiesGovernance->update([
            'content_file' => $contentFilePath,
            'injected_status' => $request->injected_status,
        ]);

        $submittedDocumentIds = [];

        // Helper function to sync documents of a specific type
        $syncDocuments = function ($policiesInput, $type) use ($request, $policiesGovernance, &$submittedDocumentIds) {
            if ($policiesInput) {
                foreach ($policiesInput as $index => $docData) {
                    if (empty($docData['title'])) {
                        continue;
                    }

                    $docId = $docData['id'] ?? null;
                    $doc = null;

                    if ($docId) {
                        $doc = PoliciesGovernanceDocument::find($docId);
                    }

                    // Handle document file upload if provided
                    $filePath = $doc ? $doc->file : null;
                    $fileKey = "{$type}_policies.{$index}.file";

                    if ($request->hasFile($fileKey)) {
                        $uploadedFile = $request->file($fileKey);
                        if ($doc && $doc->file) {
                            $filePath = MiaHelper::updateFile($doc->file, $uploadedFile, 'policies-governance');
                        } else {
                            $filePath = MiaHelper::uploadFile($uploadedFile, 'policies-governance');
                        }
                    }

                    if ($doc) {
                        $doc->update([
                            'title' => $docData['title'],
                            'file'  => $filePath,
                        ]);
                        $submittedDocumentIds[] = $doc->id;
                    } else {
                        $newDoc = $policiesGovernance->documents()->create([
                            'type'  => $type,
                            'title' => $docData['title'],
                            'file'  => $filePath,
                        ]);
                        $submittedDocumentIds[] = $newDoc->id;
                    }
                }
            }
        };

        // Sync each section
        $syncDocuments($request->inst_policies, 'inst');
        $syncDocuments($request->cert_policies, 'cert');
        $syncDocuments($request->acc_policies, 'acc');

        // Prune deleted documents
        $deletedDocs = $policiesGovernance->documents()->whereNotIn('id', $submittedDocumentIds)->get();
        foreach ($deletedDocs as $deletedDoc) {
            if ($deletedDoc->file) {
                MiaHelper::deleteFile($deletedDoc->file);
            }
            $deletedDoc->delete();
        }

        return redirect()->route('admin.policies-governance.edit')
            ->with('success', 'Policies & Governance updated successfully.');
    }
}
