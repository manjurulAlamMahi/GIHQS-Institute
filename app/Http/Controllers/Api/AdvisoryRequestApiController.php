<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\AdvisoryRequest;
use App\Models\AdminSetting;
use App\Models\User;
use App\Mail\ClientFormSubmissionMail;
use App\Mail\AdminFormSubmissionMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdvisoryRequestApiController extends Controller
{
    use ApiResponse;

    /**
     * Store a new Advisory Request.
     *
     * POST /advisory-requests
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'organization_name'    => 'required|string|max:255',
                'full_name'            => 'required|string|max:255',
                'work_email'           => 'required|email|max:255',
                'phone_number'         => 'required|string|max:50',
                'country'              => 'required|string|max:255',
                'organization_type'    => 'required|string|max:255',
                'service_of_interest'  => 'required|string|max:255',
                'desired_timeline'     => 'required|string|max:255',
                'description_of_needs' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
            }

            $advisoryRequest = AdvisoryRequest::create([
                'user_id'              => Auth::guard('api')->id(),
                'organization_name'    => $request->organization_name,
                'full_name'            => $request->full_name,
                'work_email'           => $request->work_email,
                'phone_number'         => $request->phone_number,
                'country'              => $request->country,
                'organization_type'    => $request->organization_type,
                'service_of_interest'  => $request->service_of_interest,
                'desired_timeline'     => $request->desired_timeline,
                'description_of_needs' => $request->description_of_needs,
                'status'               => 'pending',
            ]);

            // Form-submission notifications
            try {
                // 1. Resolve Admin Email
                $adminEmail = config('mail.receive_address') ?: AdminSetting::first()?->email;
                if (!$adminEmail) {
                    $adminUser = User::whereIn('role', ['admin', 'manager'])->first();
                    $adminEmail = $adminUser ? $adminUser->email : 'admin@gmail.com';
                }

                // 2. Resolve Support Contact
                $setting = AdminSetting::first();
                $supportContact = [
                    'email'    => $setting?->email ?? 'info@gihqs.org',
                    'phone'    => $setting?->phone_number ?? '',
                    'whatsapp' => $setting?->whatsapp_number ?? '',
                ];

                // 3. Prepare Submitter Email
                $summaryData = [
                    'Organization Name'    => $advisoryRequest->organization_name,
                    'Full Name'            => $advisoryRequest->full_name,
                    'Work Email'           => $advisoryRequest->work_email,
                    'Phone Number'         => $advisoryRequest->phone_number,
                    'Country'              => $advisoryRequest->country,
                    'Organization Type'    => $advisoryRequest->organization_type,
                    'Service of Interest'  => $advisoryRequest->service_of_interest,
                    'Desired Timeline'     => $advisoryRequest->desired_timeline,
                    'Description of Needs' => $advisoryRequest->description_of_needs,
                ];

                $clientMail = new ClientFormSubmissionMail(
                    $advisoryRequest->full_name,
                    'Advisory Request',
                    $advisoryRequest->reference_number,
                    $advisoryRequest->created_at->format('Y-m-d'),
                    $summaryData,
                    'We have received your request for advisory services. Our consultants will evaluate your needs and get in touch with you within 3-5 business days.',
                    $supportContact
                );

                // 4. Prepare Admin Email
                $clientInfo = [
                    'name'         => $advisoryRequest->full_name,
                    'email'        => $advisoryRequest->work_email,
                    'phone'        => $advisoryRequest->phone_number,
                    'organization' => $advisoryRequest->organization_name,
                ];

                $adminMail = new AdminFormSubmissionMail(
                    'Advisory Request',
                    $advisoryRequest->reference_number,
                    $advisoryRequest->created_at->format('Y-m-d H:i:s'),
                    $clientInfo,
                    $summaryData,
                    route('admin.advisory-requests.show', $advisoryRequest->id),
                    []
                );

                // 5. Dispatch Mails
                Mail::to($advisoryRequest->work_email)->send($clientMail);
                Mail::to($adminEmail)->send($adminMail);

            } catch (Throwable $e) {
                // Log failure gracefully without throwing exception
                Log::error('Failed to send Advisory Request notification emails', [
                    'submission_id' => $advisoryRequest->id,
                    'error_message' => $e->getMessage(),
                    'trace'         => $e->getTraceAsString(),
                ]);
            }

            $response = [
                'id'                   => $advisoryRequest->id,
                'organization_name'    => $advisoryRequest->organization_name,
                'full_name'            => $advisoryRequest->full_name,
                'work_email'           => $advisoryRequest->work_email,
                'phone_number'         => $advisoryRequest->phone_number,
                'country'              => $advisoryRequest->country,
                'organization_type'    => $advisoryRequest->organization_type,
                'service_of_interest'  => $advisoryRequest->service_of_interest,
                'desired_timeline'     => $advisoryRequest->desired_timeline,
                'description_of_needs' => $advisoryRequest->description_of_needs,
                'status'               => $advisoryRequest->status,
                'created_at'           => $advisoryRequest->created_at->toDateTimeString(),
            ];

            return $this->successResponse($response, 'Advisory request submitted successfully.', 201);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to submit advisory request.', 500);
        }
    }

    /**
     * Display a listing of the logged-in user's advisory requests.
     *
     * GET /get-advisory-request or GET /advisory-request
     */
    public function index()
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            $requests = AdvisoryRequest::where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('work_email', $user->email);
                })
                ->latest('id')
                ->get();

            if ($requests->isEmpty()) {
                return $this->errorResponse([], 'No advisory requests found.', 404);
            }

            $mappedData = $requests->map(function ($item) {
                return [
                    'id'                       => $item->id,
                    'reference_number'         => $item->reference_number,
                    'organization_name'        => $item->organization_name,
                    'full_name'                => $item->full_name,
                    'work_email'               => $item->work_email,
                    'phone_number'             => $item->phone_number,
                    'country'                  => $item->country,
                    'organization_type'        => $item->organization_type,
                    'service_of_interest'      => $item->service_of_interest,
                    'desired_timeline'         => $item->desired_timeline,
                    'status'                   => $item->status,
                    'admin_notes'              => $item->admin_notes ?? '',
                    'payment_amount'           => (float) ($item->payment_amount ?? 0.00),
                    'payment_currency'         => $item->payment_currency ?? 'usd',
                    'payment_description'      => $item->payment_description ?? '',
                    'payment_status'           => $item->payment_status ?? 'unpaid',
                    'stripe_payment_link'      => $item->stripe_payment_link ?? '',
                    'stripe_payment_intent_id' => $item->stripe_payment_intent_id ?? '',
                    'payment_date'             => $item->payment_date ? $item->payment_date->toDateTimeString() : null,
                    'validity_days'            => (int) ($item->validity_days ?? 30),
                    'expires_at'               => $item->expires_at ? $item->expires_at->toDateTimeString() : null,
                    'submission_date'          => $item->created_at->format('F j, Y'),
                    'created_at'               => $item->created_at->toDateTimeString(),
                ];
            });

            $response = [
                'advisory_requests' => $mappedData,
            ];

            return $this->successResponse($response, 'Advisory requests fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch advisory requests.', 500);
        }
    }

    /**
     * Display the specified advisory request details.
     *
     * GET /get-advisory-request/{id} or GET /advisory-request/{id}
     */
    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            $requestItem = AdvisoryRequest::find($id);

            if (!$requestItem) {
                return $this->errorResponse([], 'Advisory request not found.', 404);
            }

            if ($requestItem->user_id !== $user->id && $requestItem->work_email !== $user->email) {
                return $this->errorResponse([], 'Unauthorized access to this advisory request.', 403);
            }

            $response = [
                'advisory_request' => [
                    'id'                       => $requestItem->id,
                    'reference_number'         => $requestItem->reference_number,
                    'organization_name'        => $requestItem->organization_name,
                    'full_name'                => $requestItem->full_name,
                    'work_email'               => $requestItem->work_email,
                    'phone_number'             => $requestItem->phone_number,
                    'country'                  => $requestItem->country,
                    'organization_type'        => $requestItem->organization_type,
                    'service_of_interest'      => $requestItem->service_of_interest,
                    'desired_timeline'         => $requestItem->desired_timeline,
                    'description_of_needs'     => $requestItem->description_of_needs,
                    'status'                   => $requestItem->status,
                    'admin_notes'              => $requestItem->admin_notes ?? '',
                    'payment_amount'           => (float) ($requestItem->payment_amount ?? 0.00),
                    'payment_currency'         => $requestItem->payment_currency ?? 'usd',
                    'payment_description'      => $requestItem->payment_description ?? '',
                    'payment_status'           => $requestItem->payment_status ?? 'unpaid',
                    'stripe_payment_link'      => $requestItem->stripe_payment_link ?? '',
                    'stripe_payment_intent_id' => $requestItem->stripe_payment_intent_id ?? '',
                    'payment_date'             => $requestItem->payment_date ? $requestItem->payment_date->toDateTimeString() : null,
                    'validity_days'            => (int) ($requestItem->validity_days ?? 30),
                    'expires_at'               => $requestItem->expires_at ? $requestItem->expires_at->toDateTimeString() : null,
                    'submission_date'          => $requestItem->created_at->format('F j, Y'),
                    'created_at'               => $requestItem->created_at->toDateTimeString(),
                    'updated_at'               => $requestItem->updated_at->toDateTimeString(),
                ]
            ];

            return $this->successResponse($response, 'Advisory request details fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch advisory request details.', 500);
        }
    }
}

