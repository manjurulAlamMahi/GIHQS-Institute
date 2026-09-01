<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdvisoryHeader;
use App\Models\AdvisoryFocus;
use App\Models\AdvisoryScope;
use App\Models\AdvisoryDeliverableCard;
use App\Models\AdvisoryService;
use App\Models\AdvisoryDiscussCard;
use App\Traits\ApiResponse;
use Throwable;

class AdvisoryServicesApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Advisory Services details.
     */
    public function getAdvisoryServices()
    {
        try {
            // Retrieve all 6 advisory records with their features/relationships
            $header      = AdvisoryHeader::first();
            $focus       = AdvisoryFocus::with('features')->first();
            $scope       = AdvisoryScope::with('features')->first();
            $deliverable = AdvisoryDeliverableCard::with('features')->first();
            $service     = AdvisoryService::with('features')->first();
            $discuss     = AdvisoryDiscussCard::first();

            // Existence Check: check if all are empty
            if (!$header && !$focus && !$scope && !$deliverable && !$service && !$discuss) {
                return $this->errorResponse([], 'Advisory Services details not found.', 404);
            }

            // 1. Format Header (table: advisory_headers)
            $headerData = null;
            if ($header) {
                $headerData = [
                    'id'              => $header->id,
                    'title1'          => $header->title1,
                    'title2'          => $header->title2,
                    'tagline'         => $header->tagline,
                    'description'     => $header->description,
                    'content_file'    => $header->content_file ? asset($header->content_file) : null,
                    'injected_status' => (bool) $header->injected_status,
                ];
            }

            // 2. Format Focus (table: advisory_focuses)
            $focusData = null;
            if ($focus) {
                $focusData = [
                    'id'                      => $focus->id,
                    'title'                   => $focus->title,
                    'description'             => $focus->description,
                    
                    // Features mapped to table name: advisory_focus_features
                    'advisory_focus_features' => $focus->features->map(function ($feat) {
                        return [
                            'id'                => $feat->id,
                            'advisory_focus_id' => $feat->advisory_focus_id,
                            'description'       => $feat->description,
                        ];
                    }),
                ];
            }

            // 3. Format Scope (table: advisory_scopes)
            $scopeData = null;
            if ($scope) {
                $scopeData = [
                    'id'                      => $scope->id,
                    'title1'                  => $scope->title1,
                    'title2'                  => $scope->title2,
                    'description'             => $scope->description,
                    
                    // Features mapped to table name: advisory_scope_features
                    'advisory_scope_features' => $scope->features->map(function ($feat) {
                        return [
                            'id'                => $feat->id,
                            'advisory_scope_id' => $feat->advisory_scope_id,
                            'icon'              => $feat->icon ? asset($feat->icon) : null,
                            'title'             => $feat->title,
                            'description'       => $feat->description,
                        ];
                    }),
                ];
            }

            // 4. Format Deliverable Card (table: advisory_deliverable_cards)
            $deliverableData = null;
            if ($deliverable) {
                $deliverableData = [
                    'id'                                 => $deliverable->id,
                    'title1'                             => $deliverable->title1,
                    'title2'                             => $deliverable->title2,
                    'description'                        => $deliverable->description,
                    
                    // Features mapped to table name: advisory_deliverable_card_features
                    'advisory_deliverable_card_features' => $deliverable->features->map(function ($feat) {
                        return [
                            'id'                           => $feat->id,
                            'advisory_deliverable_card_id' => $feat->advisory_deliverable_card_id,
                            'name'                         => $feat->name,
                        ];
                    }),
                ];
            }

            // 5. Format Service Packages (table: advisory_services)
            $serviceData = null;
            if ($service) {
                $serviceData = [
                    'id'                        => $service->id,
                    'title1'                    => $service->title1,
                    'title2'                    => $service->title2,
                    'description'               => $service->description,
                    
                    // Features mapped to table name: advisory_service_features
                    'advisory_service_features' => $service->features->map(function ($feat) {
                        return [
                            'id'                  => $feat->id,
                            'advisory_service_id' => $feat->advisory_service_id,
                            'serial_number'       => $feat->serial_number,
                            'tagline'             => $feat->tagline,
                            'title'               => $feat->title,
                            'description'         => $feat->description,
                        ];
                    }),
                ];
            }

            // 6. Format Discuss Card (table: advisory_discuss_cards)
            $discussData = null;
            if ($discuss) {
                $discussData = [
                    'id'          => $discuss->id,
                    'title1'      => $discuss->title1,
                    'title2'      => $discuss->title2,
                    'description' => $discuss->description,
                    'button_text' => $discuss->button_text,
                ];
            }

            // Response Wrapper: map datasets to their database table names
            $response = [
                'advisory_headers'           => $headerData,
                'advisory_focuses'           => $focusData,
                'advisory_scopes'            => $scopeData,
                'advisory_deliverable_cards' => $deliverableData,
                'advisory_services'          => $serviceData,
                'advisory_discuss_cards'     => $discussData,
            ];

            // Success Response
            return $this->successResponse($response, 'Advisory Services details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch advisory services details.', 500);
        }
    }
}
