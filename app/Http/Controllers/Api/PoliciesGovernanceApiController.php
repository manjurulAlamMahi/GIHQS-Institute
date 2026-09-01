<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PoliciesGovernance;
use App\Traits\ApiResponse;
use Throwable;

class PoliciesGovernanceApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Policies & Governance page details.
     */
    public function getPoliciesGovernance()
    {
        try {
            // Retrieve the first PoliciesGovernance record with its documents
            $policiesGovernance = PoliciesGovernance::with('documents')->first();

            // Existence Check
            if (!$policiesGovernance) {
                return $this->errorResponse([], 'Policies & Governance details not found.', 404);
            }

                        // Grouping and formatting documents by type
            $institutionalDocuments = $policiesGovernance->documents->where('type', 'inst')->map(function ($doc) {
                return [
                    'id'    => $doc->id,
                    'title' => $doc->title,
                    'file'  => $doc->file ? asset($doc->file) : null,
                ];
            })->values();

            $certificationDocuments = $policiesGovernance->documents->where('type', 'cert')->map(function ($doc) {
                return [
                    'id'    => $doc->id,
                    'title' => $doc->title,
                    'file'  => $doc->file ? asset($doc->file) : null,
                ];
            })->values();

            $accreditationDocuments = $policiesGovernance->documents->where('type', 'acc')->map(function ($doc) {
                return [
                    'id'    => $doc->id,
                    'title' => $doc->title,
                    'file'  => $doc->file ? asset($doc->file) : null,
                ];
            })->values();

            // Data Formatting & Mapping
            $formattedData = [
                'id'                      => $policiesGovernance->id,
                'title1'                  => $policiesGovernance->title1,
                'title2'                  => $policiesGovernance->title2,
                'tagline'                 => $policiesGovernance->tagline,
                'description'             => $policiesGovernance->description,

                'inst_title'              => $policiesGovernance->inst_title,
                'inst_tag'                => $policiesGovernance->inst_tag,
                'inst_description'        => $policiesGovernance->inst_description,
                'institutional_documents' => $institutionalDocuments,

                'cert_title'              => $policiesGovernance->cert_title,
                'cert_tag'                => $policiesGovernance->cert_tag,
                'cert_description'        => $policiesGovernance->cert_description,
                'certification_documents' => $certificationDocuments,

                'acc_title'               => $policiesGovernance->acc_title,
                'acc_tag'                 => $policiesGovernance->acc_tag,
                'acc_description'         => $policiesGovernance->acc_description,
                'accreditation_documents' => $accreditationDocuments,

                'commitment_title1'       => $policiesGovernance->commitment_title1,
                'commitment_title2'       => $policiesGovernance->commitment_title2,
                'commitment_description'   => $policiesGovernance->commitment_description,

                'content_file'            => $policiesGovernance->content_file ? asset($policiesGovernance->content_file) : null,
                'injected_status'         => (bool) $policiesGovernance->injected_status,
            ];

            // Response Wrapper
            $response = [
                'policies_governances' => $formattedData,
            ];

            // Success Response
            return $this->successResponse($response, 'Policies & Governance details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch policies & governance details.', 500);
        }
    }
}
