<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CeActivity;
use App\Models\Catalogue;
use App\Helpers\MiaHelper;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CeActivityApiController extends Controller
{
    use ApiResponse;

    /**
     * Fetch all CE activities for the authenticated user.
     *
     * GET /api/profile/ce-activities
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user() ?? Auth::guard('web')->user() ?? auth()->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            $activities = CeActivity::where('user_id', $user->id)
                ->with('certification')
                ->latest()
                ->get();

            if ($activities->isEmpty()) {
                return $this->errorResponse([], 'No CE activities found.', 404);
            }

            $formattedActivities = $activities->map(function ($item) {
                return [
                    'id'              => $item->id,
                    'catalogue_id'    => $item->catalogue_id,
                    'certification'   => $item->certification ? $item->certification->title : null,
                    'certification_short' => $item->certification ? $item->certification->short_title : null,
                    'domain'          => $item->domain,
                    'activity_type'   => $item->activity_type,
                    'activity_title'  => $item->activity_title,
                    'provider'        => $item->provider,
                    'completion_date' => $item->completion_date ? $item->completion_date->toDateString() : null,
                    'credits_earned'  => (float) $item->credits_earned,
                    'evidence_file'   => $item->evidence_file ? asset($item->evidence_file) : null,
                    'description'     => $item->description,
                    'status'          => $item->status,
                    'created_at'      => $item->created_at->toIso8601String(),
                ];
            });

            $response = [
                'activities' => $formattedActivities,
            ];

            return $this->successResponse($response, 'CE activities fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch CE activities: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Store a new CE activity.
     *
     * POST /api/profile/ce-activities
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::guard('api')->user() ?? Auth::guard('web')->user() ?? auth()->user();

            if (!$user) {
                return $this->errorResponse([], 'User not authenticated.', 401);
            }

            // Validation rules
            $validator = Validator::make($request->all(), [
                'catalogue_id'    => 'required|exists:catalogues,id',
                'domain'          => 'required|string|max:255',
                'activity_type'   => 'required|string|max:255',
                'activity_title'  => 'required|string|max:255',
                'provider'        => 'required|string|max:255',
                'completion_date' => 'required|date',
                'credits_earned'  => 'required|numeric|min:0',
                'evidence_file'   => 'nullable|file|max:10240|mimes:pdf,jpeg,png,jpg,doc,docx',
                'description'     => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
            }

            // Double check that the catalogue_id is a Certification
            $catalogue = Catalogue::where('id', $request->catalogue_id)
                ->where('service_type', 'Certification')
                ->first();

            if (!$catalogue) {
                return $this->errorResponse([], 'Selected catalogue item is not a valid certification.', 422);
            }

            // Handle file upload if present
            $filePath = null;
            if ($request->hasFile('evidence_file')) {
                $filePath = MiaHelper::uploadFile($request->file('evidence_file'), 'ce-activities');
            }

            // Create CE Activity record
            $activity = CeActivity::create([
                'user_id'         => $user->id,
                'catalogue_id'    => $request->catalogue_id,
                'domain'          => $request->domain,
                'activity_type'   => $request->activity_type,
                'activity_title'  => $request->activity_title,
                'provider'        => $request->provider,
                'completion_date' => $request->completion_date,
                'credits_earned'  => $request->credits_earned,
                'evidence_file'   => $filePath,
                'description'     => $request->description,
                'status'          => 'pending',
            ]);

            $formattedActivity = [
                'id'              => $activity->id,
                'catalogue_id'    => $activity->catalogue_id,
                'certification'   => $catalogue->title,
                'certification_short' => $catalogue->short_title,
                'domain'          => $activity->domain,
                'activity_type'   => $activity->activity_type,
                'activity_title'  => $activity->activity_title,
                'provider'        => $activity->provider,
                'completion_date' => $activity->completion_date ? $activity->completion_date->toDateString() : null,
                'credits_earned'  => (float) $activity->credits_earned,
                'evidence_file'   => $activity->evidence_file ? asset($activity->evidence_file) : null,
                'description'     => $activity->description,
                'status'          => $activity->status,
                'created_at'      => $activity->created_at->toIso8601String(),
            ];

            $response = [
                'ce_activity' => $formattedActivity,
            ];

            return $this->successResponse($response, 'CE Activity added successfully.', 201);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to add CE activity: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Track CE credits progress for all purchased certifications.
     *
     * GET /api/profile/ce-activities/tracking
     */
    public function trackCredits(Request $request)
    {
        try {
            $user = Auth::guard('api')->user() ?? Auth::guard('web')->user() ?? auth()->user();

            if (!$user) {
                return $this->errorResponse([], 'User not authenticated.', 401);
            }

            // Retrieve user's paid certification purchases
            $purchases = \App\Models\Purchase::where('user_id', $user->id)
                ->where('purchase_type', 'catalogue')
                ->where('payment_status', 'paid')
                ->whereHas('catalogue', function ($query) {
                    $query->where('service_type', 'Certification');
                })
                ->with('catalogue')
                ->get();

            if ($purchases->isEmpty()) {
                return $this->errorResponse([], 'No active certification trackings found.', 404);
            }

            $trackings = $purchases->map(function ($purchase) use ($user) {
                $catalogue = $purchase->catalogue;
                
                // Required credits
                $requiredCredits = (float) $catalogue->ce_credit_total_required;

                // Dates calculation - base date is passed exam date, fallback to purchase date
                $examIds = $catalogue->exams()->pluck('id');

                $passResult = \App\Models\UserExamResult::where('user_id', $user->id)
                    ->whereIn('catalogue_exam_id', $examIds)
                    ->where('status', 'passed')
                    ->orderBy('end_time', 'desc')
                    ->first();

                // Base date is the exam completion date (if passed), otherwise fallback to purchase creation date
                $passDate = $passResult && $passResult->end_time
                    ? \Carbon\Carbon::parse($passResult->end_time)
                    : ($purchase->created_at ? \Carbon\Carbon::parse($purchase->created_at) : now());

                $validityYears = (int) ($catalogue->validity_years ?? 1);
                $validityYears = max(1, $validityYears); // Ensure validity is at least 1 year

                // Dynamic, recurring CE cycle calculation
                $cycleIndex = 0;
                $completedCredits = 0.0;
                $cycleStartDate = $passDate->copy();
                $cycleEndDate = $passDate->copy()->addYears($validityYears);

                $purchaseDate = $purchase->created_at ? \Carbon\Carbon::parse($purchase->created_at) : $passDate;

                while (true) {
                    $start = $passDate->copy()->addYears($cycleIndex * $validityYears);
                    $end = $passDate->copy()->addYears(($cycleIndex + 1) * $validityYears);
                    
                    // For the first cycle, allow activities completed since purchase date
                    $startLimit = ($cycleIndex === 0) ? $purchaseDate : $start;

                    // Sum credits for activities completed in this cycle
                    $creditsInCycle = (float) CeActivity::where('user_id', $user->id)
                        ->where('catalogue_id', $catalogue->id)
                        ->where('status', 'approved')
                        ->whereBetween('completion_date', [$startLimit->toDateString(), $end->toDateString()])
                        ->sum('credits_earned');
                        
                    // If required credits is met, advance to the next cycle (recurring reopening)
                    if ($requiredCredits > 0 && $creditsInCycle >= $requiredCredits) {
                        $cycleIndex++;
                    } else {
                        // This is the active/current cycle
                        $completedCredits = $creditsInCycle;
                        $cycleStartDate = $start;
                        $cycleEndDate = $end;
                        break;
                    }
                }

                $renewalDate = $cycleStartDate->toDateString();
                $expirationDate = $cycleEndDate->toDateString();
                
                $ceWindowStart = $cycleStartDate->format('M j, Y');
                $ceWindowEnd = $cycleEndDate->format('M j, Y');
                $ceWindow = "{$ceWindowStart} -> {$ceWindowEnd}";

                $submissionDue = $cycleEndDate->copy()->subDays(30)->toDateString();

                return [
                    'catalogue_id'        => $catalogue->id,
                    'certification_title' => $catalogue->title,
                    'certification_short' => $catalogue->short_title,
                    'required_credits'    => $requiredCredits,
                    'completed_credits'   => $completedCredits,
                    'renewal_date'        => $renewalDate,
                    'expiration_date'     => $expirationDate,
                    'ce_window'           => $ceWindow,
                    'submission_due'      => $submissionDue,
                ];
            });

            $response = [
                'trackings' => $trackings,
            ];

            return $this->successResponse($response, 'Certification credit trackings fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch credit trackings: ' . $th->getMessage(), 500);
        }
    }
}
