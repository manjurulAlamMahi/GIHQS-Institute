<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccreditationEligibility;
use App\Models\AccreditationProcess;
use App\Models\AccreditationDomain;
use App\Models\AccreditationInsight;
use App\Traits\ApiResponse;
use Throwable;

class AccreditationDetailApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Accreditation Details (Eligibility, Process, Domains, and Insights).
     */
    public function getAccreditationDetails()
    {
        try {
            // Retrieve all 4 accreditation details records with their features
            $eligibility = AccreditationEligibility::with('features')->first();
            $process     = AccreditationProcess::with('features')->first();
            $domain      = AccreditationDomain::with('features')->first();
            $insight     = AccreditationInsight::with('features')->first();

            // Existence Check: check if all are empty
            if (!$eligibility && !$process && !$domain && !$insight) {
                return $this->errorResponse([], 'Accreditation details not found.', 404);
            }

            // 1. Format Eligibility (table: accreditation_eligibility)
            $eligibilityData = null;
            if ($eligibility) {
                $eligibilityData = [
                    'id'                                  => $eligibility->id,
                    'title1'                              => $eligibility->title1,
                    'title2'                              => $eligibility->title2,
                    'description'                         => $eligibility->description,
                    
                    // Features mapped to table name: accreditation_eligibility_features
                    'accreditation_eligibility_features' => $eligibility->features->map(function ($feature) {
                        return [
                            'id'                           => $feature->id,
                            'accreditation_eligibility_id' => $feature->accreditation_eligibility_id,
                            'title'                        => $feature->title,
                            'description'                  => $feature->description,
                        ];
                    }),
                ];
            }

            // 2. Format Process (table: accreditation_processes)
            $processData = null;
            if ($process) {
                $processData = [
                    'id'                             => $process->id,
                    'title1'                         => $process->title1,
                    'title2'                         => $process->title2,
                    'description'                    => $process->description,
                    
                    // Features mapped to table name: accreditation_process_features
                    'accreditation_process_features' => $process->features->map(function ($feature) {
                        return [
                            'id'                       => $feature->id,
                            'accreditation_process_id' => $feature->accreditation_process_id,
                            'serial'                   => $feature->serial,
                            'title'                    => $feature->title,
                            'subtitle'                 => $feature->subtitle,
                            'description'              => $feature->description,
                        ];
                    }),
                ];
            }

            // 3. Format Domain (table: accreditation_domains)
            $domainData = null;
            if ($domain) {
                $domainData = [
                    'id'                            => $domain->id,
                    'title1'                        => $domain->title1,
                    'title2'                        => $domain->title2,
                    'description'                   => $domain->description,
                    
                    // Features mapped to table name: accreditation_domain_features
                    'accreditation_domain_features' => $domain->features->map(function ($feature) {
                        return [
                            'id'                      => $feature->id,
                            'accreditation_domain_id' => $feature->accreditation_domain_id,
                            'domain_serial'           => $feature->domain_serial,
                            'title'                   => $feature->title,
                            'description'             => $feature->description,
                        ];
                    }),
                ];
            }

            // 4. Format Insight (table: accreditation_insights)
            $insightData = null;
            if ($insight) {
                $insightData = [
                    'id'                             => $insight->id,
                    'title1'                         => $insight->title1,
                    'title2'                         => $insight->title2,
                    'description'                    => $insight->description,
                    
                    // Features mapped to table name: accreditation_insights_features
                    'accreditation_insights_features' => $insight->features->map(function ($feature) {
                        return [
                            'id'                        => $feature->id,
                            'accreditation_insights_id' => $feature->accreditation_insights_id,
                            'title'                     => $feature->title,
                            'tagline'                   => $feature->tagline,
                            'description'               => $feature->description,
                        ];
                    }),
                ];
            }

            // Response Wrapper: map datasets to their database table names
            $response = [
                'accreditation_eligibility' => $eligibilityData,
                'accreditation_processes'   => $processData,
                'accreditation_domains'     => $domainData,
                'accreditation_insights'    => $insightData,
            ];

            // Success Response
            return $this->successResponse($response, 'Accreditation Details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch accreditation details.', 500);
        }
    }
}
