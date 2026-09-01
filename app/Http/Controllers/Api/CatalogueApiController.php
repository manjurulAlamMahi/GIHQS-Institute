<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catalogue;
use App\Models\Purchase;
use App\Models\CertificationApplication;
use App\Models\UserExamOverride;
use App\Services\ExamEligibilityService;
use App\Support\ExamLinkSigner;
use App\Traits\ApiResponse;
use App\Traits\GeneratesCertificates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CatalogueApiController extends Controller
{
    use ApiResponse, GeneratesCertificates;

    public function __construct(private ExamEligibilityService $eligibility)
    {
    }

    /**
     * Paid course material (module, story guide, overview video) is only handed
     * out to a user who owns the catalogue. These files used to be returned by
     * the public catalogue endpoints, so anyone who knew a catalogue id could
     * read the full paid module straight from /full-module/{id}.
     */
    private function paidMaterial(Catalogue $item, $user, ?bool $hasAccess = null): array
    {
        $hasAccess ??= $user ? $this->eligibility->hasCatalogueAccess($user, $item) : false;

        return [
            'story_guide_file' => $hasAccess && $item->story_guide_file ? asset($item->story_guide_file) : null,
            'module_file'      => $hasAccess && $item->module_file ? asset($item->module_file) : null,
            'overview_video'   => $hasAccess && $item->overview_video ? asset($item->overview_video) : null,
            'has_access'       => $hasAccess,
        ];
    }

    /**
     * Fetch all catalogues with optional filtering.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            $purchasedCatalogueIds = [];
            $approvedCertificationCatalogueIds = [];
            if ($user) {
                $purchasedCatalogueIds = Purchase::where('user_id', $user->id)
                    ->where('purchase_type', 'catalogue')
                    ->where('payment_status', 'paid')
                    ->pluck('catalogue_id')
                    ->toArray();

                $approvedCertificationCatalogueIds = CertificationApplication::where('user_id', $user->id)
                    ->whereIn('status', ['accepted', 'completed'])
                    ->pluck('catalogue_id')
                    ->toArray();
            }

            // Retrieve catalogues query builder with relationships
            $query = Catalogue::with(['features']);

            // Exclude "members only" catalogues if the user is a guest or does not have active paid membership
            if (!$user || !$user->active_paid_membership) {
                $query->where('catalogue_type', '!=', 'members only');
            }

            // Sorting by service type (e.g. ?sorting=course, ?sorting=certification, ?sorting=all)
            if ($request->has('sorting') && !empty($request->sorting)) {
                $sorting = trim($request->sorting);
                if (strtolower($sorting) !== 'all') {
                    $query->where('service_type', ucfirst(strtolower($sorting)));
                }
            }

            // Filtering parameter (e.g. ?filtering=featured, ?filtering=trending, ?filtering=popular)
            if ($request->has('filtering') && !empty($request->filtering)) {
                $filtering = $request->filtering;
                if ($filtering === 'featured') {
                    $query->where('is_feature', true);
                } elseif ($filtering === 'trending') {
                    $query->where('is_trending', true);
                } elseif ($filtering === 'popular') {
                    $query->where('is_popular', true);
                }
            }

            // Fallback to legacy individual query parameters if filtering is not specified
            if (!$request->has('filtering') || empty($request->filtering)) {
                if ($request->has('featured') && $request->featured !== null) {
                    $query->where('is_feature', filter_var($request->featured, FILTER_VALIDATE_BOOLEAN));
                }
                if ($request->has('trending') && $request->trending !== null) {
                    $query->where('is_trending', filter_var($request->trending, FILTER_VALIDATE_BOOLEAN));
                }
                if ($request->has('popular') && $request->popular !== null) {
                    $query->where('is_popular', filter_var($request->popular, FILTER_VALIDATE_BOOLEAN));
                }
            }

            // Keywords filtering (e.g. ?keyword=AIHQSP)
            if ($request->has('keyword') && !empty($request->keyword)) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                       ->orWhere('short_title', 'like', "%{$keyword}%")
                       ->orWhere('short_description', 'like', "%{$keyword}%");
                });
            }

            // Get filtered results
            $catalogues = $query->latest()->get();

            // Existence Check
            if ($catalogues->isEmpty()) {
                return $this->errorResponse([], 'No catalogues found matching the criteria.', 404);
            }

            // Data Formatting & Mapping
            $formattedCatalogues = $catalogues->map(function ($item) use ($user, $purchasedCatalogueIds, $approvedCertificationCatalogueIds) {
                $hasAccess = $user && (
                    in_array($item->id, $purchasedCatalogueIds)
                    || ($user->active_paid_membership && $item->catalogue_type === 'members only')
                );
                $paid = $this->paidMaterial($item, $user, $hasAccess);

                return [
                    'id'                             => $item->id,
                    'title'                          => $item->title,
                    'short_title'                    => $item->short_title,
                    'short_description'              => $item->short_description,
                    'price_regular'                  => (float) $item->price_regular,
                    'price_final'                    => (float) $item->calculateFinalPriceForUser($user),
                    'catalogue_type'                 => $item->catalogue_type,
                    'discount_type'                  => $item->discount_type,
                    'discount_value'                 => (float) $item->discount_value,
                    'is_discount_active'             => (bool) $item->is_discount_active,
                    'service_type'                   => $item->service_type,
                    'details_file'                   => $item->details_file ? asset($item->details_file) : null,
                    'story_guide_file'               => $paid['story_guide_file'],
                    'module_file'                    => $paid['module_file'],
                    'overview_video'                 => $paid['overview_video'],
                    'has_access'                     => $paid['has_access'],
                    'is_feature'                     => (bool) $item->is_feature,
                    'is_trending'                    => (bool) $item->is_trending,
                    'is_popular'                     => (bool) $item->is_popular,
                    'healthcare_quality_improvement' => (bool) $item->healthcare_quality_improvement,
                    'patient_safety_risk_management' => (bool) $item->patient_safety_risk_management,
                    'status'                         => (int) $item->status,
                    'credit_earn'                    => (float) $item->credit_earn,
                    'ce_credit_total_required'       => (float) $item->ce_credit_total_required,
                    'certification_approved'         => $user ? in_array($item->id, $approvedCertificationCatalogueIds) : false,
                    'features'                       => $item->features->map(function ($feature) {
                        return [
                            'id'           => $feature->id,
                            'catalogue_id' => $feature->catalogue_id,
                            'description'  => $feature->description,
                        ];
                    }),
                ];
            });

            // Response Wrapper
            $response = [
                'catalogues' => $formattedCatalogues,
            ];

            // Success Response
            return $this->successResponse($response, 'Catalogues fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch catalogues.', 500);
        }
    }

    /**
     * Fetch a single catalogue.
     */
    public function show($id)
    {
        try {
            // Retrieve catalogue by id
            $item = Catalogue::find($id);

            // Existence Check
            if (!$item) {
                return $this->errorResponse([], 'Catalogue not found.', 404);
            }

            $user = Auth::guard('api')->user() ?? Auth::guard('web')->user() ?? auth()->user();
            if ($item->catalogue_type === 'members only' && (!$user || !$user->active_paid_membership)) {
                return $this->errorResponse([], 'Catalogue not found.', 404);
            }

            $paid = $this->paidMaterial($item, $user);

            // Data Formatting & Mapping
            $formattedCatalogue = [
                'id'           => $item->id,
                'title'        => $item->title,
                'details_file' => $item->details_file ? asset($item->details_file) : null,
                'story_guide_file' => $paid['story_guide_file'],
                'module_file'  => $paid['module_file'],
                'overview_video' => $paid['overview_video'],
                'has_access'   => $paid['has_access'],
            ];

            // Response Wrapper
            $response = [
                'catalogue' => $formattedCatalogue,
            ];

            // Success Response
            return $this->successResponse($response, 'Catalogue details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch catalogue.', 500);
        }
    }

    /**
     * Fetch a single catalogue with full details (matching the list mapping exactly).
     */
    public function details($id)
    {
        try {
            // Retrieve catalogue by id with features relationship
            $item = Catalogue::with(['features'])->find($id);

            // Existence Check
            if (!$item) {
                return $this->errorResponse([], 'Catalogue not found.', 404);
            }

            $user = Auth::guard('api')->user() ?? Auth::guard('web')->user() ?? auth()->user();
            if ($item->catalogue_type === 'members only' && (!$user || !$user->active_paid_membership)) {
                return $this->errorResponse([], 'Catalogue not found.', 404);
            }

            $isApproved = false;
            if ($user) {
                $isApproved = CertificationApplication::where('user_id', $user->id)
                    ->where('catalogue_id', $item->id)
                    ->whereIn('status', ['accepted', 'completed'])
                    ->exists();
            }

            $paid = $this->paidMaterial($item, $user);

            // Data Formatting & Mapping (matching the formatted catalog structure exactly)
            $formattedCatalogue = [
                'id'                             => $item->id,
                'title'                          => $item->title,
                'short_title'                    => $item->short_title,
                'short_description'              => $item->short_description,
                'price_regular'                  => (float) $item->price_regular,
                'price_final'                    => (float) $item->calculateFinalPriceForUser($user),
                'catalogue_type'                 => $item->catalogue_type,
                'discount_type'                  => $item->discount_type,
                'discount_value'                 => (float) $item->discount_value,
                'is_discount_active'             => (bool) $item->is_discount_active,
                'service_type'                   => $item->service_type,
                'details_file'                   => $item->details_file ? asset($item->details_file) : null,
                'story_guide_file'               => $paid['story_guide_file'],
                'module_file'                    => $paid['module_file'],
                'overview_video'                 => $paid['overview_video'],
                'has_access'                     => $paid['has_access'],
                'is_feature'                     => (bool) $item->is_feature,
                'is_trending'                    => (bool) $item->is_trending,
                'is_popular'                     => (bool) $item->is_popular,
                'healthcare_quality_improvement' => (bool) $item->healthcare_quality_improvement,
                'patient_safety_risk_management' => (bool) $item->patient_safety_risk_management,
                'status'                         => (int) $item->status,
                'credit_earn'                    => (float) $item->credit_earn,
                'ce_credit_total_required'       => (float) $item->ce_credit_total_required,
                'certification_approved'         => $isApproved,
                'features'                       => $item->features->map(function ($feature) {
                    return [
                        'id'           => $feature->id,
                        'catalogue_id' => $feature->catalogue_id,
                        'description'  => $feature->description,
                    ];
                }),
            ];

            // Response Wrapper
            $response = [
                'catalogue' => $formattedCatalogue,
            ];

            // Success Response
            return $this->successResponse($response, 'Catalogue details fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch catalogue details.', 500);
        }
    }

    /**
     * Fetch all catalogues purchased by the authenticated customer.
     */
    public function purchasedCatalogues(Request $request)
    {
        try {
            $user = Auth::guard('api')->user() ?? Auth::guard('web')->user() ?? auth()->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            $approvedCertificationCatalogueIds = CertificationApplication::where('user_id', $user->id)
                ->whereIn('status', ['accepted', 'completed'])
                ->pluck('catalogue_id')
                ->toArray();

            // Retrieve catalogues query builder
            if ($user->active_paid_membership) {
                $query = Catalogue::where(function ($q) use ($user) {
                    $q->whereHas('purchases', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id)
                              ->where('purchase_type', 'catalogue')
                              ->where('payment_status', 'paid');
                    })->orWhere('catalogue_type', 'members only');
                });
            } else {
                $query = Catalogue::whereHas('purchases', function ($subQuery) use ($user) {
                    $subQuery->where('user_id', $user->id)
                          ->where('purchase_type', 'catalogue')
                          ->where('payment_status', 'paid');
                });
            }

            // Filter by service_type (accepts array or comma-separated string)
            if ($request->has('service_type') && !empty($request->service_type)) {
                $serviceTypes = $request->service_type;
                if (is_string($serviceTypes)) {
                    $serviceTypes = array_filter(array_map('trim', explode(',', $serviceTypes)));
                }
                if (is_array($serviceTypes) && !empty($serviceTypes)) {
                    // Normalize casing to match database TitleCase (e.g., 'course' -> 'Course')
                    $serviceTypes = array_map(function ($type) {
                        return ucfirst(strtolower(trim($type)));
                    }, $serviceTypes);
                    
                    $query->whereIn('service_type', $serviceTypes);
                }
            }

            // Keywords filtering (e.g. ?keyword=AIHQSP)
            if ($request->has('keyword') && !empty($request->keyword)) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                       ->orWhere('short_title', 'like', "%{$keyword}%")
                       ->orWhere('short_description', 'like', "%{$keyword}%");
                });
            }

            $catalogues = $query->with(['features'])->latest()->get();

            // Existence Check
            if ($catalogues->isEmpty()) {
                return $this->errorResponse([], 'No purchased catalogues found.', 404);
            }

            // Data Formatting & Mapping
            $formattedCatalogues = $catalogues->map(function ($item) use ($user, $approvedCertificationCatalogueIds) {
                return [
                    'id'                             => $item->id,
                    'title'                          => $item->title,
                    'short_title'                    => $item->short_title,
                    'short_description'              => $item->short_description,
                    'price_regular'                  => (float) $item->price_regular,
                    'price_final'                    => (float) $item->calculateFinalPriceForUser($user),
                    'catalogue_type'                 => $item->catalogue_type,
                    'discount_type'                  => $item->discount_type,
                    'discount_value'                 => (float) $item->discount_value,
                    'is_discount_active'             => (bool) $item->is_discount_active,
                    'service_type'                   => $item->service_type,
                    'details_file'                   => $item->details_file ? asset($item->details_file) : null,
                    'story_guide_file'               => $item->story_guide_file ? asset($item->story_guide_file) : null,
                    'module_file'                    => $item->module_file ? asset($item->module_file) : null,
                    'overview_video'                 => $item->overview_video ? asset($item->overview_video) : null,
                    'is_feature'                     => (bool) $item->is_feature,
                    'is_trending'                    => (bool) $item->is_trending,
                    'is_popular'                     => (bool) $item->is_popular,
                    'healthcare_quality_improvement' => (bool) $item->healthcare_quality_improvement,
                    'patient_safety_risk_management' => (bool) $item->patient_safety_risk_management,
                    'status'                         => (int) $item->status,
                    'credit_earn'                    => (float) $item->credit_earn,
                    'ce_credit_total_required'       => (float) $item->ce_credit_total_required,
                    'certification_approved'         => in_array($item->id, $approvedCertificationCatalogueIds),
                    'features'                       => $item->features->map(function ($feature) {
                        return [
                            'id'           => $feature->id,
                            'catalogue_id' => $feature->catalogue_id,
                            'description'  => $feature->description,
                        ];
                    }),
                ];
            });

            // Response Wrapper
            $response = [
                'catalogues' => $formattedCatalogues,
            ];

            // Success Response
            return $this->successResponse($response, 'Purchased catalogues fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch purchased catalogues.', 500);
        }
    }

    /**
     * Fetch a single purchased catalogue by ID.
     */
    public function purchasedCatalogueShow($id)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            // Retrieve the specific catalogue if the user has purchased and paid for it, or if it is "members only" and user is an active member
            $query = Catalogue::where('id', $id);
            if ($user->active_paid_membership) {
                $query->where(function ($q) use ($user) {
                    $q->whereHas('purchases', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id)
                              ->where('purchase_type', 'catalogue')
                              ->where('payment_status', 'paid');
                    })->orWhere('catalogue_type', 'members only');
                });
            } else {
                $query->whereHas('purchases', function ($subQuery) use ($user) {
                    $subQuery->where('user_id', $user->id)
                          ->where('purchase_type', 'catalogue')
                          ->where('payment_status', 'paid');
                });
            }
            $item = $query->with(['features', 'resources', 'exams', 'liveLinks', 'videos', 'videoLinks'])->first();

            // Existence Check
            if (!$item) {
                return $this->errorResponse([], 'Purchased catalogue not found.', 404);
            }

            // Fetch the specific purchase record
            $purchase = Purchase::where('user_id', $user->id)
                ->where('purchase_type', 'catalogue')
                ->where('catalogue_id', $item->id)
                ->where('payment_status', 'paid')
                ->latest()
                ->first();

            // Data Formatting & Mapping
            $formattedCatalogue = [
                'id'                             => $item->id,
                'title'                          => $item->title,
                'short_title'                    => $item->short_title,
                'short_description'              => $item->short_description,
                'purchase_details' => $purchase ? [
                    'price_regular'       => (float) ($purchase->price_regular ?? $item->price_regular),
                    'price_purchased'     => (float) ($purchase->price_purchased ?? $purchase->amount),
                    'discount_amount'     => (float) ($purchase->discount_amount ?? 0.00),
                    'discount_percentage' => (float) ($purchase->discount_percentage ?? 0.00),
                    'purchased_at'        => $purchase->created_at->toIso8601String(),
                ] : null,
                'price_regular'                  => (float) $item->price_regular,
                'price_final'                    => (float) $item->calculateFinalPriceForUser($user),
                'catalogue_type'                 => $item->catalogue_type,
                'discount_type'                  => $item->discount_type,
                'discount_value'                 => (float) $item->discount_value,
                'is_discount_active'             => (bool) $item->is_discount_active,
                'service_type'                   => $item->service_type,
                'details_file'                   => $item->details_file ? asset($item->details_file) : null,
                'story_guide_file'               => $item->story_guide_file ? asset($item->story_guide_file) : null,
                'module_file'                    => $item->module_file ? asset($item->module_file) : null,
                'overview_video'                 => $item->overview_video ? asset($item->overview_video) : null,
                'is_feature'                     => (bool) $item->is_feature,
                'is_trending'                    => (bool) $item->is_trending,
                'is_popular'                     => (bool) $item->is_popular,
                'healthcare_quality_improvement' => (bool) $item->healthcare_quality_improvement,
                'patient_safety_risk_management' => (bool) $item->patient_safety_risk_management,
                'status'                         => (int) $item->status,
                'credit_earn'                    => (float) $item->credit_earn,
                'ce_credit_total_required'       => (float) $item->ce_credit_total_required,
                'features'                       => $item->features->map(function ($feature) {
                    return [
                        'id'           => $feature->id,
                        'catalogue_id' => $feature->catalogue_id,
                        'description'  => $feature->description,
                    ];
                }),
                'resources'                      => $item->resources->map(function ($resource) {
                    return [
                        'id'             => $resource->id,
                        'catalogue_id'   => $resource->catalogue_id,
                        'resource_title' => $resource->resource_title,
                        'resource_file'  => $resource->resource_file ? asset($resource->resource_file) : null,
                        'is_premium'     => (bool) $resource->is_premium,
                    ];
                }),
                'live_links'                     => $item->liveLinks->map(function ($liveLink) {
                    return [
                        'id'           => $liveLink->id,
                        'catalogue_id' => $liveLink->catalogue_id,
                        'link_title'   => $liveLink->link_title,
                        'link_url'     => $liveLink->link_url,
                    ];
                }),
                'video_files'                    => $item->videos->map(function ($video) use ($user) {
                    $isCompleted = false;
                    if ($user) {
                        $isCompleted = \App\Models\UserVideoProgress::where('user_id', $user->id)
                            ->where('video_id', $video->id)
                            ->where('is_completed', true)
                            ->exists();
                    }
                    return [
                        'id'           => $video->id,
                        'catalogue_id' => $video->catalogue_id,
                        'video_title'  => $video->video_title,
                        'video_file'   => $video->video_file ? asset($video->video_file) : null,
                        'thumbnail'    => $video->thumbnail ? asset($video->thumbnail) : null,
                        'is_completed' => $isCompleted,
                    ];
                }),
                'video_links'                    => $item->videoLinks->map(function ($videoLink) use ($user) {
                    $isCompleted = false;
                    if ($user) {
                        $isCompleted = \App\Models\UserVideoProgress::where('user_id', $user->id)
                            ->where('video_link_id', $videoLink->id)
                            ->where('is_completed', true)
                            ->exists();
                    }
                    return [
                        'id'               => $videoLink->id,
                        'catalogue_id'     => $videoLink->catalogue_id,
                        'video_link_title' => $videoLink->video_link_title,
                        'video_link_url'   => $videoLink->video_link_url,
                        'is_completed'     => $isCompleted,
                    ];
                }),
                'coursework_completed'           => $this->eligibility->hasCompletedCoursework($user, $item),
                'exams'                          => $item->exams->map(function ($exam) use ($user, $item) {
                    $status = $this->eligibility->attemptStatus($user, $exam, $item);

                    $maxAttempts        = $status['max_attempts'];
                    $attemptsCount      = $status['attempts_count'];
                    $attemptsExceeded   = $status['attempts_exceeded'];
                    $retakeLocked       = $status['retake_locked'];
                    $retakeEligibleDate = $status['retake_eligible_date'];
                    $result             = $status['latest_result'];

                    // The exam link is only issued when the user is actually allowed
                    // to sit the exam - entitlement, coursework, attempts and cooldown
                    // all included. The link carries a signed identifier so the result
                    // that comes back cannot be re-pointed at another user or exam.
                    [$allowed] = $this->eligibility->check($user, $exam, $item);

                    $examLink = null;
                    if ($allowed && $exam->exam_link) {
                        $examLink = $this->generateSecureExamLink($exam->exam_link, $exam->id, $user);
                    }

                    return [
                        'id'                => $exam->id,
                        'catalogue_id'      => $exam->catalogue_id,
                        'exam_title'        => $exam->exam_title,
                        'exam_link'         => $examLink,
                        'can_take_exam'     => $allowed,
                        'exam_type'         => $exam->exam_link ? 'classmarker' : ($exam->exam_id ? 'local' : null),
                        'local_exam_id'     => $exam->exam_id,
                        'is_premium'        => (bool) $exam->is_premium,
                        'max_attempts'      => $maxAttempts,
                        'attempts_count'    => $attemptsCount,
                        'attempts_exceeded' => $attemptsExceeded,
                        'retake_locked'     => $retakeLocked,
                        'retake_eligible_date' => $retakeEligibleDate,
                        'user_status'  => $result ? [
                            'status'                    => $result->status, // passed, failed
                            'score'                     => $result->score,
                            'points_available'           => $result->points_available,
                            'percentage'                => $result->percentage,
                            'percentage_passmark'        => $result->percentage_passmark,
                            'taken_at'                  => $result->created_at->toIso8601String(),
                            'duration'                  => $result->duration,
                            'ip_address'                 => $result->ip_address,
                            'start_time'                 => $result->start_time ? $result->start_time->toIso8601String() : null,
                            'end_time'                   => $result->end_time ? $result->end_time->toIso8601String() : null,
                            'certificate_serial_number' => $result->certificate_serial_number,
                            'certificate_url'           => $result->certificate_url,
                            'download_certificate'      => $result->download_certificate,
                            'view_results_url'          => $result->view_results_url,
                            'category_results'           => $result->category_results,
                        ] : null,
                    ];
                }),
            ];

            // Response Wrapper
            $response = [
                'catalogue' => $formattedCatalogue,
            ];

            // Success Response
            return $this->successResponse($response, 'Purchased catalogue fetched successfully.', 200);

        } catch (Throwable $th) {
            // Error Response
            return $this->errorResponse([], 'Failed to fetch purchased catalogue.', 500);
        }
    }

    /**
     * Fetch all attempts for a specific exam by the authenticated user.
     */
    public function examAttempts(Request $request, $examId)
    {
        try {
            $user = Auth::guard('api')->user() ?? Auth::guard('web')->user() ?? auth()->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            // Find the exam
            $exam = \App\Models\CatalogueExam::find($examId);
            if (!$exam) {
                return $this->errorResponse([], 'Exam not found.', 404);
            }

            $catalogue = \App\Models\Catalogue::find($exam->catalogue_id);

            if (!$catalogue) {
                return $this->errorResponse([], 'Catalogue not found.', 404);
            }

            // Check if user has purchased the catalogue containing this exam, or if it is members only and they have active membership
            if (!$this->eligibility->hasCatalogueAccess($user, $catalogue)) {
                return $this->errorResponse([], 'You do not have access to this exam.', 403);
            }

            // Fetch user's attempts for this exam
            $latestPurchase = Purchase::where('user_id', $user->id)
                ->where('catalogue_id', $exam->catalogue_id)
                ->where('purchase_type', 'catalogue')
                ->where('payment_status', 'paid')
                ->latest('id')
                ->first();

            $resultsQuery = \App\Models\UserExamResult::where('user_id', $user->id)
                ->where('catalogue_exam_id', $exam->id);

            if ($latestPurchase) {
                $resultsQuery->where('created_at', '>=', $latestPurchase->created_at);
            }

            $results = $resultsQuery->orderBy('created_at', 'desc')->get();

            $status = $this->eligibility->attemptStatus($user, $exam, $catalogue);

            $maxAttempts        = $status['max_attempts'];
            $attemptsCount      = $status['attempts_count'];
            $attemptsExceeded   = $status['attempts_exceeded'];
            $retakeLocked       = $status['retake_locked'];
            $retakeEligibleDate = $status['retake_eligible_date'];

            [$allowed] = $this->eligibility->check($user, $exam, $catalogue);

            $examLink = null;
            if ($allowed && $exam->exam_link) {
                $examLink = $this->generateSecureExamLink($exam->exam_link, $exam->id, $user);
            }

            // Format the exam details
            $formattedExam = [
                'id'                => $exam->id,
                'catalogue_id'      => $exam->catalogue_id,
                'exam_title'        => $exam->exam_title,
                'exam_link'         => $examLink,
                'can_take_exam'     => $allowed,
                'exam_type'         => $exam->exam_link ? 'classmarker' : ($exam->exam_id ? 'local' : null),
                'local_exam_id'     => $exam->exam_id,
                'is_premium'        => (bool) $exam->is_premium,
                'max_attempts'      => $maxAttempts,
                'attempts_count'    => $attemptsCount,
                'attempts_exceeded' => $attemptsExceeded,
                'retake_locked'     => $retakeLocked,
                'retake_eligible_date' => $retakeEligibleDate,
            ];

            // Format the attempts
            $formattedAttempts = $results->map(function ($result) {
                return [
                    'id'                        => $result->id,
                    'score'                     => $result->score,
                    'points_available'           => $result->points_available,
                    'percentage'                => $result->percentage,
                    'percentage_passmark'        => $result->percentage_passmark,
                    'status'                    => $result->status, // passed, failed
                    'duration'                  => $result->duration,
                    'ip_address'                 => $result->ip_address,
                    'start_time'                 => $result->start_time ? $result->start_time->toIso8601String() : null,
                    'end_time'                   => $result->end_time ? $result->end_time->toIso8601String() : null,
                    'taken_at'                  => $result->created_at->toIso8601String(),
                    'certificate_serial_number' => $result->certificate_serial_number,
                    'certificate_url'           => $result->certificate_url,
                    'download_certificate'      => $result->download_certificate,
                    'view_results_url'          => $result->view_results_url,
                    'category_results'           => $result->category_results,
                ];
            });

            $response = [
                'exam'     => $formattedExam,
                'attempts' => $formattedAttempts,
            ];

            return $this->successResponse($response, 'Exam attempts fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch exam attempts.', 500);
        }
    }

    /**
     * Generate a secure ClassMarker exam link.
     */
    private function generateSecureExamLink($examLink, $examId, $user)
    {
        if (!$examLink || !$user) {
            return $examLink;
        }

        // cm_user_id is visible and editable in the browser's address bar once the
        // candidate is on ClassMarker, and it is what the result webhook uses to
        // decide who gets credited for which exam. It is signed so a tampered value
        // is rejected on the way back in.
        $query = http_build_query([
            'cm_user_id' => ExamLinkSigner::sign((int) $user->id, (int) $examId),
            'cm_e'       => $user->email,
            'cm_fn'      => $user->first_name ?? $user->full_name,
            'cm_ln'      => $user->last_name ?? '',
        ]);

        $separator = parse_url($examLink, PHP_URL_QUERY) ? '&' : '?';
        return $examLink . $separator . $query;
    }

    /**
     * Fetch questions and options of a local/custom exam.
     */
    public function getLocalExamDetails($catalogueExamId)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            // Find the catalogue exam
            $catalogueExam = \App\Models\CatalogueExam::find($catalogueExamId);
            if (!$catalogueExam) {
                return $this->errorResponse([], 'Catalogue exam not found.', 404);
            }

            $catalogue = \App\Models\Catalogue::find($catalogueExam->catalogue_id);
            if (!$catalogue) {
                return $this->errorResponse([], 'Catalogue not found.', 404);
            }

            // Entitlement, coursework, attempt limit and cooldown - all enforced
            // server side. The dashboard shows the same rules, but the exam page is
            // reachable by direct URL, so the browser's copy of them is only a hint.
            //
            // This runs BEFORE any check on the exam's shape: answering "that is not
            // a local exam" to someone with no access tells them about a resource
            // they are not entitled to see.
            [$allowed, $reason, $statusCode] = $this->eligibility->check($user, $catalogueExam, $catalogue);

            if (!$allowed) {
                return $this->errorResponse([], $reason, $statusCode);
            }

            // Verify it is a local exam (has exam_id)
            if (!$catalogueExam->exam_id) {
                return $this->errorResponse([], 'This is not a local custom exam.', 400);
            }

            // Fetch local exam details
            $localExam = \App\Models\Exam::with(['questions.options'])->find($catalogueExam->exam_id);
            if (!$localExam) {
                return $this->errorResponse([], 'Local exam details not found.', 404);
            }

            // Format data - DO NOT include is_correct attribute for safety
            $formattedExam = [
                'id' => $catalogueExam->id,
                'catalogue_id' => $catalogueExam->catalogue_id,
                'exam_title' => $catalogueExam->exam_title,
                'questions' => $localExam->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question_text' => $question->question_text,
                        'sort_order' => $question->sort_order,
                        'options' => $question->options->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'option_text' => $option->option_text,
                                'sort_order' => $option->sort_order,
                            ];
                        }),
                    ];
                }),
            ];

            $response = [
                'exam' => $formattedExam,
            ];

            return $this->successResponse($response, 'Local exam questions fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch local exam details.', 500);
        }
    }

    /**
     * Submit answers for a local/custom exam.
     */
    public function submitLocalExam(Request $request, $catalogueExamId)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            // Validate request
            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|exists:exam_questions,id',
                'answers.*.option_id' => 'required|exists:exam_options,id',
                'duration' => 'nullable|integer', // in seconds
                'start_time' => 'nullable|date',
            ]);

            // Find the catalogue exam
            $catalogueExam = \App\Models\CatalogueExam::find($catalogueExamId);
            if (!$catalogueExam) {
                return $this->errorResponse([], 'Catalogue exam not found.', 404);
            }

            $catalogue = \App\Models\Catalogue::find($catalogueExam->catalogue_id);
            if (!$catalogue) {
                return $this->errorResponse([], 'Catalogue not found.', 404);
            }

            // Entitlement, coursework, attempt limit and cooldown - all enforced
            // server side. The dashboard shows the same rules, but the exam page is
            // reachable by direct URL, so the browser's copy of them is only a hint.
            //
            // This runs BEFORE any check on the exam's shape: answering "that is not
            // a local exam" to someone with no access tells them about a resource
            // they are not entitled to see.
            [$allowed, $reason, $statusCode] = $this->eligibility->check($user, $catalogueExam, $catalogue);

            if (!$allowed) {
                return $this->errorResponse([], $reason, $statusCode);
            }

            // Verify it is a local exam (has exam_id)
            if (!$catalogueExam->exam_id) {
                return $this->errorResponse([], 'This is not a local custom exam.', 400);
            }

            // Fetch the local exam questions and options
            $localExam = \App\Models\Exam::with(['questions.options'])->find($catalogueExam->exam_id);
            if (!$localExam) {
                return $this->errorResponse([], 'Local exam details not found.', 404);
            }

            $questions = $localExam->questions;
            $pointsAvailable = (float) $questions->count();
            if ($pointsAvailable <= 0) {
                return $this->errorResponse([], 'This exam has no questions.', 400);
            }

            // Evaluate answers
            $score = 0.0;
            $submittedAnswersMap = [];
            foreach ($validated['answers'] as $ans) {
                $submittedAnswersMap[$ans['question_id']] = $ans['option_id'];
            }

            foreach ($questions as $question) {
                $correctOption = $question->options->where('is_correct', true)->first();
                if ($correctOption) {
                    $userOptionId = $submittedAnswersMap[$question->id] ?? null;
                    if ($userOptionId && (int)$userOptionId === (int)$correctOption->id) {
                        $score += 1.0;
                    }
                }
            }

            $percentage = ($score / $pointsAvailable) * 100;
            $passMark = $catalogueExam->pass_mark !== null ? (float)$catalogueExam->pass_mark : 50.0;
            $status = ($percentage >= $passMark) ? 'passed' : 'failed';

            // Create Exam Result
            $examResult = \App\Models\UserExamResult::create([
                'user_id' => $user->id,
                'catalogue_exam_id' => $catalogueExam->id,
                'score' => $score,
                'points_available' => $pointsAvailable,
                'percentage' => $percentage,
                'percentage_passmark' => $passMark,
                'status' => $status,
                'duration' => $validated['duration'] ?? null,
                'ip_address' => $request->ip(),
                'start_time' => isset($validated['start_time']) ? \Carbon\Carbon::parse($validated['start_time']) : null,
                'end_time' => now(),
            ]);

            // Generate certificate if passed
            if ($examResult->status === 'passed') {
                if ($catalogue) {
                    $this->generateLocalCertificate($examResult, $catalogue, $user);
                }
            }

            // Fresh load result to get updated certificate fields
            $examResult->refresh();

            // Prepare response data
            $response = [
                'result' => [
                    'id' => $examResult->id,
                    'score' => $examResult->score,
                    'points_available' => $examResult->points_available,
                    'percentage' => $examResult->percentage,
                    'percentage_passmark' => $examResult->percentage_passmark,
                    'status' => $examResult->status,
                    'taken_at' => $examResult->created_at->toIso8601String(),
                    'duration' => $examResult->duration,
                    'certificate_serial_number' => $examResult->certificate_serial_number,
                    'certificate_url' => $examResult->certificate_url,
                    'download_certificate' => $examResult->download_certificate,
                ]
            ];

            return $this->successResponse($response, 'Exam submitted and evaluated successfully.', 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Let a validation failure surface as 422 rather than being swallowed
            // by the catch-all below and reported as a server error.
            throw $e;
        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to submit exam. Error: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Verify a certificate by serial number (Public API).
     */
    public function verifyCertificate($certificate_id)
    {
        try {
            // Only a passed attempt is a credential. A serial left behind by a
            // failed attempt must not verify.
            $examResult = \App\Models\UserExamResult::with(['user', 'catalogueExam.catalogue'])
                ->where('certificate_serial_number', $certificate_id)
                ->where('status', 'passed')
                ->first();

            if (!$examResult) {
                return $this->errorResponse([], 'Certificate not found or invalid.', 404);
            }

            $user = $examResult->user;
            $catalogue = $examResult->catalogueExam->catalogue ?? null;

            $issueDate = $examResult->created_at ? $examResult->created_at->format('M d, Y') : null;
            $validityYears = $catalogue ? $catalogue->validity_years : null;
            $expiry = $validityYears && $examResult->created_at
                ? $examResult->created_at->copy()->addYears($validityYears)
                : null;
            $expiryDate = $expiry ? $expiry->format('M d, Y') : 'Lifetime';
            $isValid = $expiry === null || now()->lt($expiry);

            $data = [
                'certificate_id'     => $examResult->certificate_serial_number,
                'is_valid'           => $isValid,
                'status'             => $isValid ? 'Verified' : 'Expired',
                'recipient_name'     => $user ? $user->full_name : 'N/A',
                'certification'      => $catalogue ? $catalogue->title : 'N/A',
                'certification_code' => $catalogue ? $catalogue->short_title : 'N/A',
                'issue_date'         => $issueDate,
                'expiry_date'        => $expiryDate,
                'certificate_pdf'    => $isValid ? ($examResult->certificate_url ?? $examResult->download_certificate) : null,
            ];

            return $this->successResponse($data, 'Certificate verified successfully.', 200);

        } catch (\Throwable $th) {
            return $this->errorResponse([], 'Verification failed: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update video or video link completion progress for the authenticated user.
     */
    public function updateVideoProgress(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            $request->validate([
                'video_id' => 'nullable|required_without:video_link_id|exists:catalogue_videos,id',
                'video_link_id' => 'nullable|required_without:video_id|exists:catalogue_video_links,id',
                'is_completed' => 'required|boolean',
            ]);

            $videoId = $request->video_id;
            $videoLinkId = $request->video_link_id;
            $isCompleted = (bool) $request->is_completed;

            // Video completion is a prerequisite for sitting the exam, so progress
            // may only be recorded against a catalogue the user actually owns.
            $catalogueId = $videoId
                ? optional(\App\Models\CatalogueVideo::find($videoId))->catalogue_id
                : optional(\App\Models\CatalogueVideoLink::find($videoLinkId))->catalogue_id;

            $catalogue = $catalogueId ? Catalogue::find($catalogueId) : null;

            if (!$catalogue || !$this->eligibility->hasCatalogueAccess($user, $catalogue)) {
                return $this->errorResponse([], 'You do not have access to this course.', 403);
            }

            $progress = \App\Models\UserVideoProgress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'video_id' => $videoId,
                    'video_link_id' => $videoLinkId,
                ],
                [
                    'is_completed' => $isCompleted,
                ]
            );

            return $this->successResponse(
                [
                    'id' => $progress->id,
                    'user_id' => $progress->user_id,
                    'video_id' => $progress->video_id,
                    'video_link_id' => $progress->video_link_id,
                    'is_completed' => $progress->is_completed,
                ],
                'Progress updated successfully.',
                200
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Let a validation failure surface as 422 rather than being swallowed
            // by the catch-all below and reported as a server error.
            throw $e;
        } catch (\Throwable $th) {
            return $this->errorResponse([], 'Failed to update progress: ' . $th->getMessage(), 500);
        }
    }



    /**
     * Get catalogues formatted specifically for the menu/navigation bar.
     */
    public function menu(Request $request)
    {
        try {
            $mapper = function ($item) {
                return [
                    'id'               => $item->id,
                    'name'             => $item->title,
                    'title'            => $item->title,
                    'short_title'      => $item->short_title,
                    'details_file'     => $item->details_file ? asset($item->details_file) : null,
                    // Paid material: withheld from the public navigation payload.
                    'story_guide_file' => $this->paidMaterial($item, Auth::guard('api')->user())['story_guide_file'],
                ];
            };

            // Support query parameter filtering (e.g. ?service_type=certification)
            if ($request->has('service_type') && !empty($request->service_type)) {
                $serviceType = strtolower(trim($request->service_type));

                if ($serviceType === 'certification') {
                    $certifications = Catalogue::where('service_type', 'Certification')->get();
                    return $this->successResponse([
                        'certifications' => $certifications->map($mapper)
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                if ($serviceType === 'module') {
                    $modulesHqi = Catalogue::where('service_type', 'Module')->where('healthcare_quality_improvement', true)->get();
                    $modulesPsrm = Catalogue::where('service_type', 'Module')->where('patient_safety_risk_management', true)->get();
                    $modulesOthers = Catalogue::where('service_type', 'Module')->where('healthcare_quality_improvement', false)->where('patient_safety_risk_management', false)->get();
                    return $this->successResponse([
                        'modules' => [
                            'healthcare_quality_improvement' => $modulesHqi->map($mapper),
                            'patient_safety_risk_management' => $modulesPsrm->map($mapper),
                            'others'                         => $modulesOthers->map($mapper),
                        ]
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                if ($serviceType === 'course') {
                    $courses = Catalogue::where('service_type', 'Course')->get();
                    return $this->successResponse([
                        'courses' => $courses->map($mapper)
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                if ($serviceType === 'toolkit') {
                    $toolkits = Catalogue::where('service_type', 'Toolkit')->get();
                    return $this->successResponse([
                        'toolkits' => $toolkits->map($mapper)
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                if ($serviceType === 'webinar') {
                    $webinars = Catalogue::where('service_type', 'Webinar')->get();
                    return $this->successResponse([
                        'webinars' => $webinars->map($mapper)
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                if ($serviceType === 'workshop') {
                    $workshops = Catalogue::where('service_type', 'Workshop')->get();
                    return $this->successResponse([
                        'workshops' => $workshops->map($mapper)
                    ], 'Menu catalogues fetched successfully.', 200);
                }
            }

            // Fallback to retrieving and returning all categories
            $modulesHqi = Catalogue::where('service_type', 'Module')
                ->where('healthcare_quality_improvement', true)
                ->get();

            $modulesPsrm = Catalogue::where('service_type', 'Module')
                ->where('patient_safety_risk_management', true)
                ->get();

            $modulesOthers = Catalogue::where('service_type', 'Module')
                ->where('healthcare_quality_improvement', false)
                ->where('patient_safety_risk_management', false)
                ->get();

            $courses = Catalogue::where('service_type', 'Course')
                ->get();

            $toolkits = Catalogue::where('service_type', 'Toolkit')
                ->get();

            $certifications = Catalogue::where('service_type', 'Certification')
                ->get();

            $webinars = Catalogue::where('service_type', 'Webinar')
                ->get();

            $workshops = Catalogue::where('service_type', 'Workshop')
                ->get();

            if ($modulesHqi->isEmpty() && $modulesPsrm->isEmpty() && $modulesOthers->isEmpty() && $courses->isEmpty() && $toolkits->isEmpty() && $certifications->isEmpty() && $webinars->isEmpty() && $workshops->isEmpty()) {
                return $this->errorResponse([], 'No catalogues found.', 404);
            }

            $response = [
                'modules' => [
                    'healthcare_quality_improvement' => $modulesHqi->map($mapper),
                    'patient_safety_risk_management' => $modulesPsrm->map($mapper),
                    'others'                         => $modulesOthers->map($mapper),
                ],
                'courses'        => $courses->map($mapper),
                'toolkits'       => $toolkits->map($mapper),
                'certifications' => $certifications->map($mapper),
                'webinars'       => $webinars->map($mapper),
                'workshops'      => $workshops->map($mapper),
            ];

            return $this->successResponse($response, 'Menu catalogues fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch menu catalogues.', 500);
        }
    }

    /**
     * Get catalogues formatted specifically for the menu/navigation bar, excluding certifications.
     */
    public function menuWithoutCertification(Request $request)
    {
        try {
            $mapper = function ($item) {
                return [
                    'id'               => $item->id,
                    'name'             => $item->title,
                    'title'            => $item->title,
                    'short_title'      => $item->short_title,
                    'details_file'     => $item->details_file ? asset($item->details_file) : null,
                    // Paid material: withheld from the public navigation payload.
                    'story_guide_file' => $this->paidMaterial($item, Auth::guard('api')->user())['story_guide_file'],
                ];
            };

            // Support query parameter filtering (e.g. ?service_type=course)
            if ($request->has('service_type') && !empty($request->service_type)) {
                $serviceType = strtolower(trim($request->service_type));

                if ($serviceType === 'module') {
                    $modulesHqi = Catalogue::where('service_type', 'Module')->where('healthcare_quality_improvement', true)->get();
                    $modulesPsrm = Catalogue::where('service_type', 'Module')->where('patient_safety_risk_management', true)->get();
                    $modulesOthers = Catalogue::where('service_type', 'Module')->where('healthcare_quality_improvement', false)->where('patient_safety_risk_management', false)->get();
                    return $this->successResponse([
                        'modules' => [
                            'healthcare_quality_improvement' => $modulesHqi->map($mapper),
                            'patient_safety_risk_management' => $modulesPsrm->map($mapper),
                            'others'                         => $modulesOthers->map($mapper),
                        ]
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                if ($serviceType === 'course') {
                    $courses = Catalogue::where('service_type', 'Course')->get();
                    return $this->successResponse([
                        'courses' => $courses->map($mapper)
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                if ($serviceType === 'toolkit') {
                    $toolkits = Catalogue::where('service_type', 'Toolkit')->get();
                    return $this->successResponse([
                        'toolkits' => $toolkits->map($mapper)
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                if ($serviceType === 'webinar') {
                    $webinars = Catalogue::where('service_type', 'Webinar')->get();
                    return $this->successResponse([
                        'webinars' => $webinars->map($mapper)
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                if ($serviceType === 'workshop') {
                    $workshops = Catalogue::where('service_type', 'Workshop')->get();
                    return $this->successResponse([
                        'workshops' => $workshops->map($mapper)
                    ], 'Menu catalogues fetched successfully.', 200);
                }

                // If asking specifically for certification, return empty/not found for this endpoint
                if ($serviceType === 'certification') {
                    return $this->errorResponse([], 'Certifications are not available on this endpoint.', 404);
                }
            }

            // Fallback to retrieving and returning all categories EXCEPT certifications
            $modulesHqi = Catalogue::where('service_type', 'Module')
                ->where('healthcare_quality_improvement', true)
                ->get();

            $modulesPsrm = Catalogue::where('service_type', 'Module')
                ->where('patient_safety_risk_management', true)
                ->get();

            $modulesOthers = Catalogue::where('service_type', 'Module')
                ->where('healthcare_quality_improvement', false)
                ->where('patient_safety_risk_management', false)
                ->get();

            $courses = Catalogue::where('service_type', 'Course')
                ->get();

            $toolkits = Catalogue::where('service_type', 'Toolkit')
                ->get();

            $webinars = Catalogue::where('service_type', 'Webinar')
                ->get();

            $workshops = Catalogue::where('service_type', 'Workshop')
                ->get();

            if ($modulesHqi->isEmpty() && $modulesPsrm->isEmpty() && $modulesOthers->isEmpty() && $courses->isEmpty() && $toolkits->isEmpty() && $webinars->isEmpty() && $workshops->isEmpty()) {
                return $this->errorResponse([], 'No catalogues found.', 404);
            }

            $response = [
                'modules' => [
                    'healthcare_quality_improvement' => $modulesHqi->map($mapper),
                    'patient_safety_risk_management' => $modulesPsrm->map($mapper),
                    'others'                         => $modulesOthers->map($mapper),
                ],
                'courses'   => $courses->map($mapper),
                'toolkits'  => $toolkits->map($mapper),
                'webinars'  => $webinars->map($mapper),
                'workshops' => $workshops->map($mapper),
            ];

            return $this->successResponse($response, 'Menu catalogues fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch menu catalogues.', 500);
        }
    }

    /**
     * Get a single catalogue formatted for the menu bar context.
     */
    public function menuShow($id)
    {
        try {
            $item = Catalogue::find($id);

            if (!$item) {
                return $this->errorResponse([], 'Catalogue not found.', 404);
            }

            $response = [
                'catalogue' => [
                    'id'               => $item->id,
                    'name'             => $item->title,
                    'title'            => $item->title,
                    'short_title'      => $item->short_title,
                    'details_file'     => $item->details_file ? asset($item->details_file) : null,
                    // Paid material: withheld from the public navigation payload.
                    'story_guide_file' => $this->paidMaterial($item, Auth::guard('api')->user())['story_guide_file'],
                ]
            ];

            return $this->successResponse($response, 'Menu catalogue details fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch menu catalogue details.', 500);
        }
    }
}
