<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccreditationApplyHero;
use App\Models\AccreditationEligibilitySnapshot;
use App\Traits\ApiResponse;
use Throwable;

class AccreditationApplyHeroApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Accreditation Apply Hero and Eligibility Snapshot details.
     */
    public function getAccreditationApplyHero()
    {
        try {
            // Retrieve both records
            $hero     = AccreditationApplyHero::first();
            $snapshot = AccreditationEligibilitySnapshot::with('features')->first();

            // Existence Check: check if both are empty
            if (!$hero && !$snapshot) {
                return $this->errorResponse([], 'Accreditation Apply Hero details not found.', 404);
            }

            // 1. Format Hero (table: accreditation_apply_hero)
            $heroData = null;
            if ($hero) {
                $heroData = [
                    'id'          => $hero->id,
                    'title1'      => $hero->title1,
                    'title2'      => $hero->title2,
                    'tagline'     => $hero->tagline,
                    'description' => $hero->description,
                    'note'        => $hero->note,
                ];
            }

            // 2. Format Eligibility Snapshot (table: accreditation_eligibility_snapshot)
            $snapshotData = null;
            if ($snapshot) {
                $snapshotData = [
                    'id'          => $snapshot->id,
                    'title'       => $snapshot->title,
                    'description' => $snapshot->description,
                    
                    // Features mapped to table name: accreditation_eligibility_snapshot_features
                    'accreditation_eligibility_snapshot_features' => $snapshot->features->map(function ($feat) {
                        return [
                            'id'                                    => $feat->id,
                            'accreditation_eligibility_snapshot_id' => $feat->accreditation_eligibility_snapshot_id,
                            'keypoints'                             => $feat->keypoints,
                            'details'                               => $feat->details,
                        ];
                    }),
                ];
            }

            // Response Wrapper: map datasets to their database table names
            $response = [
                'accreditation_apply_hero'          => $heroData,
                'accreditation_eligibility_snapshot' => $snapshotData,
            ];

            // Success Response
            return $this->successResponse($response, 'Accreditation Apply Hero details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch accreditation apply hero details.', 500);
        }
    }
}
