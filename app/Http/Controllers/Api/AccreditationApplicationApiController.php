<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Helpers\MiaHelper;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccreditationApplication;
use App\Models\AdminSetting;
use App\Models\User;
use App\Mail\ClientFormSubmissionMail;
use App\Mail\AdminFormSubmissionMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AccreditationApplicationApiController extends Controller
{
    use ApiResponse;

    /**
     * Store a new Accreditation Application.
     *
     * POST /apply-accreditation
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // 1. Applicant Information
                'applicant_category'              => 'required|string|max:255',
                'applicant_name'                  => 'required|string|max:255',
                'department_division'             => 'nullable|string|max:255',
                'country'                         => 'required|string|max:255',
                'city'                            => 'required|string|max:255',
                'website_url'                     => 'nullable|url|max:255',
                'year_established'                => 'nullable|string|max:100',

                // 2. Program Information
                'program_name'                    => 'required|string|max:255',
                'program_type'                    => 'required|string|max:255',
                'program_delivery_format'         => 'required|string|max:255',
                'estimated_annual_participants'   => 'nullable|string|max:255',
                'primary_language_of_instruction' => 'nullable|string|max:255',
                'program_launch_date'             => 'nullable|string|max:100',

                // 3. Primary Contact Information
                'primary_contact_person'          => 'required|string|max:255',
                'contact_title_position'          => 'required|string|max:255',
                'email_address'                   => 'required|email|max:255',
                'phone_number'                    => 'nullable|string|max:50',

                // 4. Supporting Attachments (PDF preferred, max 10MB)
                'program_overview_doc'            => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
                'governance_policy_doc'           => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',

                // 5. Additional Information
                'additional_information'          => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
            }

            // Handle file uploads
            $programOverviewPath = null;
            if ($request->hasFile('program_overview_doc')) {
                $programOverviewPath = MiaHelper::uploadFile($request->file('program_overview_doc'), 'accreditation-docs');
            }

            $governancePolicyPath = null;
            if ($request->hasFile('governance_policy_doc')) {
                $governancePolicyPath = MiaHelper::uploadFile($request->file('governance_policy_doc'), 'accreditation-docs');
            }

            // Create record
            $application = AccreditationApplication::create([
                'user_id'                         => Auth::guard('api')->id(),
                // 1. Applicant Information
                'applicant_category'              => $request->applicant_category,
                'applicant_name'                  => $request->applicant_name,
                'department_division'             => $request->department_division,
                'country'                         => $request->country,
                'city'                            => $request->city,
                'website_url'                     => $request->website_url,
                'year_established'                => $request->year_established,

                // 2. Program Information
                'program_name'                    => $request->program_name,
                'program_type'                    => $request->program_type,
                'program_delivery_format'         => $request->program_delivery_format,
                'estimated_annual_participants'   => $request->estimated_annual_participants,
                'primary_language_of_instruction' => $request->primary_language_of_instruction,
                'program_launch_date'             => $request->program_launch_date,

                // 3. Primary Contact Information
                'primary_contact_person'          => $request->primary_contact_person,
                'contact_title_position'          => $request->contact_title_position,
                'email_address'                   => $request->email_address,
                'phone_number'                    => $request->phone_number,

                // 4. Supporting Attachments
                'program_overview_doc'            => $programOverviewPath,
                'governance_policy_doc'           => $governancePolicyPath,

                // 5. Additional Information
                'additional_information'          => $request->additional_information,

                'status'                          => 'pending',
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
                    'Applicant Category'              => $application->applicant_category,
                    'Applicant Name'                  => $application->applicant_name,
                    'Department / Division'           => $application->department_division ?? 'N/A',
                    'Country'                         => $application->country,
                    'City'                            => $application->city,
                    'Website URL'                     => $application->website_url ?? 'N/A',
                    'Year Established'                => $application->year_established ?? 'N/A',
                    'Program Name'                    => $application->program_name,
                    'Program Type'                    => $application->program_type,
                    'Delivery Format'                 => $application->program_delivery_format,
                    'Estimated Annual Participants'   => $application->estimated_annual_participants ?? 'N/A',
                    'Primary Language of Instruction' => $application->primary_language_of_instruction ?? 'N/A',
                    'Program Launch Date'             => $application->program_launch_date ?? 'N/A',
                    'Primary Contact Person'          => $application->primary_contact_person,
                    'Contact Title / Position'        => $application->contact_title_position,
                    'Additional Information'          => $application->additional_information ?? 'None',
                ];

                $clientMail = new ClientFormSubmissionMail(
                    $application->primary_contact_person,
                    'Accreditation Application',
                    $application->reference_number,
                    $application->created_at->format('Y-m-d'),
                    $summaryData,
                    'We have received your accreditation application. Our team will review the program details and supporting documentation and contact you within 5-7 business days.',
                    $supportContact
                );

                // 4. Prepare Admin Email
                $clientInfo = [
                    'name'         => $application->primary_contact_person,
                    'email'        => $application->email_address,
                    'phone'        => $application->phone_number ?? 'N/A',
                    'organization' => $application->applicant_name,
                ];

                $attachments = array_filter([
                    'Program Overview Document'  => $application->program_overview_doc ? asset($application->program_overview_doc) : null,
                    'Governance Policy Document' => $application->governance_policy_doc ? asset($application->governance_policy_doc) : null,
                ]);

                $adminMail = new AdminFormSubmissionMail(
                    'Accreditation Application',
                    $application->reference_number,
                    $application->created_at->format('Y-m-d H:i:s'),
                    $clientInfo,
                    $summaryData,
                    route('admin.accreditation-applications.show', $application->id),
                    $attachments
                );

                // 5. Dispatch Mails
                Mail::to($application->email_address)->send($clientMail);
                Mail::to($adminEmail)->send($adminMail);

            } catch (Throwable $e) {
                // Log failure gracefully without throwing exception
                Log::error('Failed to send Accreditation Application notification emails', [
                    'submission_id' => $application->id,
                    'error_message' => $e->getMessage(),
                    'trace'         => $e->getTraceAsString(),
                ]);
            }

            // Map and format output response data
            $response = [
                'id'                              => $application->id,
                'applicant_category'              => $application->applicant_category,
                'applicant_name'                  => $application->applicant_name,
                'department_division'             => $application->department_division             ?? '',
                'country'                         => $application->country,
                'city'                            => $application->city,
                'website_url'                     => $application->website_url                     ?? '',
                'year_established'                => $application->year_established                ?? '',
                'program_name'                    => $application->program_name,
                'program_type'                    => $application->program_type,
                'program_delivery_format'         => $application->program_delivery_format,
                'estimated_annual_participants'   => $application->estimated_annual_participants   ?? '',
                'primary_language_of_instruction' => $application->primary_language_of_instruction ?? '',
                'program_launch_date'             => $application->program_launch_date             ?? '',
                'primary_contact_person'          => $application->primary_contact_person,
                'contact_title_position'          => $application->contact_title_position,
                'email_address'                   => $application->email_address,
                'phone_number'                    => $application->phone_number                    ?? '',
                'program_overview_doc'            => $application->program_overview_doc ? asset($application->program_overview_doc) : null,
                'governance_policy_doc'           => $application->governance_policy_doc ? asset($application->governance_policy_doc) : null,
                'additional_information'          => $application->additional_information          ?? '',
                'status'                          => $application->status,
                'created_at'                      => $application->created_at->toDateTimeString(),
            ];

            return $this->successResponse($response, 'Accreditation application submitted successfully.', 201);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to submit accreditation application.', 500);
        }
    }

    /**
     * Display a listing of the logged-in user's accreditation applications.
     *
     * GET /apply-accreditation
     */
    public function index()
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            // Retrieve applications submitted by the user (matching either user_id or email_address)
            $applications = AccreditationApplication::where('user_id', $user->id)
                ->orWhere('email_address', $user->email)
                ->latest('id')
                ->get();

            if ($applications->isEmpty()) {
                return $this->errorResponse([], 'No accreditation applications found.', 404);
            }

            $mappedData = $applications->map(function ($item) {
                return [
                    'id'                    => $item->id,
                    'reference_number'      => $item->reference_number,
                    'verification_code'     => $item->verification_code,
                    'applicant_category'    => $item->applicant_category,
                    'applicant_name'        => $item->applicant_name,
                    'program_name'          => $item->program_name,
                    'submission_date'       => $item->created_at->format('F j, Y'),
                    'status'                => $item->status,
                    'computed_status'       => $item->computed_status,
                    'issued_at'             => $item->issued_at ? $item->issued_at->format('M d, Y') : null,
                    'expires_at'            => $item->expires_at ? $item->expires_at->format('M d, Y') : null,
                    'certificate_pdf_url'   => $item->certificate_pdf_url,
                    'admin_notes'           => $item->admin_notes ?? '',
                    'created_at'            => $item->created_at->toDateTimeString(),
                ];
            });

            $response = [
                'applications' => $mappedData,
            ];

            return $this->successResponse($response, 'Accreditation applications fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch accreditation applications.', 500);
        }
    }

    /**
     * Display the specified accreditation application details.
     *
     * GET /apply-accreditation/{id}
     */
    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            $application = AccreditationApplication::find($id);

            if (!$application) {
                return $this->errorResponse([], 'Accreditation application not found.', 404);
            }

            // Ensure the application belongs to the logged-in user (matching either user_id or email_address)
            if ($application->user_id !== $user->id && $application->email_address !== $user->email) {
                return $this->errorResponse([], 'Unauthorized access to this application.', 403);
            }

            $response = [
                'application' => [
                    'id'                              => $application->id,
                    'reference_number'                => $application->reference_number,
                    'verification_code'               => $application->verification_code,
                    'applicant_category'              => $application->applicant_category,
                    'applicant_name'                  => $application->applicant_name,
                    'department_division'             => $application->department_division             ?? '',
                    'country'                         => $application->country,
                    'city'                            => $application->city,
                    'website_url'                     => $application->website_url                     ?? '',
                    'year_established'                => $application->year_established                ?? '',
                    'program_name'                    => $application->program_name,
                    'program_type'                    => $application->program_type,
                    'program_delivery_format'         => $application->program_delivery_format,
                    'estimated_annual_participants'   => $application->estimated_annual_participants   ?? '',
                    'primary_language_of_instruction' => $application->primary_language_of_instruction ?? '',
                    'program_launch_date'             => $application->program_launch_date             ?? '',
                    'primary_contact_person'          => $application->primary_contact_person,
                    'contact_title_position'          => $application->contact_title_position,
                    'email_address'                   => $application->email_address,
                    'phone_number'                    => $application->phone_number                    ?? '',
                    'program_overview_doc'            => $application->program_overview_doc ? asset($application->program_overview_doc) : null,
                    'governance_policy_doc'           => $application->governance_policy_doc ? asset($application->governance_policy_doc) : null,
                    'additional_information'          => $application->additional_information          ?? '',
                    'status'                          => $application->status,
                    'computed_status'                 => $application->computed_status,
                    'issued_at'                       => $application->issued_at ? $application->issued_at->format('M d, Y') : null,
                    'expires_at'                      => $application->expires_at ? $application->expires_at->format('M d, Y') : null,
                    'certificate_pdf_url'             => $application->certificate_pdf_url,
                    'admin_notes'                     => $application->admin_notes                     ?? '',
                    'submission_date'                 => $application->created_at->format('F j, Y'),
                    'created_at'                      => $application->created_at->toDateTimeString(),
                    'updated_at'                      => $application->updated_at->toDateTimeString(),
                ]
            ];

            return $this->successResponse($response, 'Accreditation application details fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch accreditation application details.', 500);
        }
    }

    /**
     * Public verification endpoint for QR code & Verification ID.
     *
     * GET /accreditation/verify/{code}
     */
    public function publicVerify($code)
    {
        try {
            // Lookup by primary key used to be accepted here, which turned this
            // public endpoint into a directory: /verify/1, /verify/2 ... walked the
            // whole applications table, including applications that were never
            // accredited. Only the codes printed on a certificate are accepted.
            $application = AccreditationApplication::where(function ($query) use ($code) {
                    $query->where('verification_code', $code)
                        ->orWhere('reference_number', $code);
                })
                ->first();

            if (!$application) {
                return $this->errorResponse([], 'Accreditation record not found for the given Verification ID.', 404);
            }

            $computedStatus = $application->computed_status;
            $isValid = ($computedStatus === 'valid');

            $statusLabels = [
                'valid'        => 'Valid (Active Accreditation)',
                'expired'      => 'Expired (Validity Period Ended)',
                'revoked'      => 'Revoked (Accreditation Canceled)',
                'under_review' => 'Under Review',
                'pending'      => 'Pending Review',
                'canceled'     => 'Canceled',
            ];

            $setting = \App\Models\CertificateSetting::first();
            $sealUrl = ($setting && $setting->seal && file_exists(public_path($setting->seal)))
                ? asset($setting->seal)
                : null;

            $data = [
                'verification_code'   => $application->verification_code ?: $application->reference_number,
                'reference_number'    => $application->reference_number,
                'applicant_category'  => $application->applicant_category,
                'applicant_name'      => $application->applicant_name,
                'program_name'        => $application->program_name,
                'program_type'        => $application->program_type,
                'country'             => $application->country,
                'city'                => $application->city,
                'issued_at'           => $application->issued_at ? $application->issued_at->format('M d, Y') : null,
                'expires_at'          => $application->expires_at ? $application->expires_at->format('M d, Y') : null,
                'status'              => $computedStatus,
                'status_label'        => $statusLabels[$computedStatus] ?? ucfirst($computedStatus),
                'is_valid'            => $isValid,
                'certificate_pdf_url' => $isValid ? $application->certificate_pdf_url : null,
                'seal_url'            => $sealUrl,
            ];

            return $this->successResponse($data, 'Accreditation verification details fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Verification check failed: ' . $th->getMessage(), 500);
        }
    }
}
