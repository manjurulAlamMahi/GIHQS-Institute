<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Helpers\MiaHelper;
use App\Models\Catalogue;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CertificationApplication;
use App\Models\AdminSetting;
use App\Models\User;
use App\Mail\ClientFormSubmissionMail;
use App\Mail\AdminFormSubmissionMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CertificationApplicationController extends Controller
{
    use ApiResponse;

    /**
     * Get certification catalogues for dropdown.
     *
     * GET /certification-catalogues
     */
    public function certificationCatalogues()
    {
        try {
            $catalogues = Catalogue::where('service_type', 'Certification')
                ->where('status', 1)
                ->orderBy('title')
                ->get();

            if ($catalogues->isEmpty()) {
                return $this->errorResponse([], 'No certification programmes found.', 404);
            }

            $data = $catalogues->map(function ($item) {
                return [
                    'id'    => $item->id,
                    'title' => $item->title,
                ];
            });

            $response = [
                'certifications' => $data,
            ];

            return $this->successResponse($response, 'Certification catalogues fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch certification catalogues.', 500);
        }
    }

    /**
     * Store a new certification application.
     *
     * POST /apply-for-certification
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // Applicant Information
                'first_name'               => 'required|string|max:255',
                'last_name'                => 'required|string|max:255',
                'email'                    => 'required|email|max:255',
                'phone'                    => 'required|string|max:50',
                'country'                  => 'required|string|max:255',
                'city'                     => 'required|string|max:255',
                'current_job_title'        => 'required|string|max:255',
                'organization'             => 'required|string|max:255',
                'linkedin_profile'         => 'nullable|url|max:500',

                // Professional Background
                'years_of_experience'        => 'required|string|max:50',
                'primary_area_of_experience' => 'required|string|max:500',
                'professional_role'          => 'required|string|max:255',
                'resume_cv'                  => 'nullable|file|mimes:pdf,doc,docx|max:5120',

                // Certification Selection
                'catalogue_id'             => 'required|integer|exists:catalogues,id',

                // Confirmations
                'confirm_accuracy'         => 'required|boolean',
                'agree_policies'           => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
            }

            // Verify the selected catalogue is of type Certification
            $catalogue = Catalogue::where('id', $request->catalogue_id)
                ->where('service_type', 'Certification')
                ->where('status', 1)
                ->first();

            if (!$catalogue) {
                return $this->errorResponse([], 'Selected certification programme is invalid or unavailable.', 422);
            }

            $userId = Auth::guard('api')->id();
            $email = $request->email;

            // Check running applications (pending or accepted) for this user or email
            $hasRunning = CertificationApplication::where(function ($query) use ($userId, $email) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->where('email', $email);
                    }
                })
                ->where('catalogue_id', $request->catalogue_id)
                ->whereIn('status', ['pending', 'accepted'])
                ->exists();

            if ($hasRunning) {
                return $this->errorResponse([], 'You already have a pending or approved application for this certification.', 400);
            }

            // Check active valid certification
            $targetUser = $userId ? Auth::guard('api')->user() : \App\Models\User::where('email', $email)->first();
            if ($targetUser && $targetUser->hasValidCertification($request->catalogue_id)) {
                return $this->errorResponse([], 'You already have a valid active certificate for this certification program.', 400);
            }

            // Handle resume/CV file upload
            $resumePath = null;
            if ($request->hasFile('resume_cv')) {
                $resumePath = MiaHelper::uploadFile($request->file('resume_cv'), 'certification-resumes');
            }

            $application = CertificationApplication::create([
                'user_id'                    => Auth::guard('api')->id(),
                // Applicant Information
                'first_name'                 => $request->first_name,
                'last_name'                  => $request->last_name,
                'email'                      => $request->email,
                'phone'                      => $request->phone,
                'country'                    => $request->country,
                'city'                       => $request->city,
                'current_job_title'          => $request->current_job_title,
                'organization'               => $request->organization,
                'linkedin_profile'           => $request->linkedin_profile,

                // Professional Background
                'years_of_experience'        => $request->years_of_experience,
                'primary_area_of_experience' => $request->primary_area_of_experience,
                'professional_role'          => $request->professional_role,
                'resume_cv'                  => $resumePath,

                // Certification Selection
                'catalogue_id'               => $request->catalogue_id,

                // Confirmations
                'confirm_accuracy'           => $request->boolean('confirm_accuracy'),
                'agree_policies'             => $request->boolean('agree_policies'),

                'status'                     => 'pending',
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
                    'First Name'                 => $application->first_name,
                    'Last Name'                  => $application->last_name,
                    'Email Address'              => $application->email,
                    'Phone Number'               => $application->phone,
                    'Country'                    => $application->country,
                    'City'                       => $application->city,
                    'Current Job Title'          => $application->current_job_title,
                    'Organization'               => $application->organization,
                    'LinkedIn Profile'           => $application->linkedin_profile ?? 'N/A',
                    'Years of Experience'        => $application->years_of_experience,
                    'Primary Area of Experience' => $application->primary_area_of_experience,
                    'Professional Role'          => $application->professional_role,
                    'Selected Programme'         => $catalogue->title,
                ];

                $clientMail = new ClientFormSubmissionMail(
                    $application->name,
                    'Certification Application',
                    $application->reference_number,
                    $application->created_at->format('Y-m-d'),
                    $summaryData,
                    'We have received your application for the certification programme. Our review board will assess your professional background and CV and contact you with our decision or next steps within 5-7 business days.',
                    $supportContact
                );

                // 4. Prepare Admin Email
                $clientInfo = [
                    'name'         => $application->name,
                    'email'        => $application->email,
                    'phone'        => $application->phone,
                    'organization' => $application->organization,
                ];

                $attachments = array_filter([
                    'Resume/CV' => $application->resume_cv ? asset($application->resume_cv) : null,
                ]);

                $adminMail = new AdminFormSubmissionMail(
                    'Certification Application',
                    $application->reference_number,
                    $application->created_at->format('Y-m-d H:i:s'),
                    $clientInfo,
                    $summaryData,
                    route('admin.certification-applications.show', $application->id),
                    $attachments
                );

                // 5. Dispatch Mails
                Mail::to($application->email)->send($clientMail);
                Mail::to($adminEmail)->send($adminMail);

            } catch (Throwable $e) {
                // Log failure gracefully without throwing exception
                Log::error('Failed to send Certification Application notification emails', [
                    'submission_id' => $application->id,
                    'error_message' => $e->getMessage(),
                    'trace'         => $e->getTraceAsString(),
                ]);
            }

            $response = [
                'id'                         => $application->id,
                'first_name'                 => $application->first_name,
                'last_name'                  => $application->last_name,
                'email'                      => $application->email,
                'phone'                      => $application->phone,
                'country'                    => $application->country,
                'city'                       => $application->city,
                'current_job_title'          => $application->current_job_title,
                'organization'               => $application->organization,
                'linkedin_profile'           => $application->linkedin_profile     ?? '',
                'years_of_experience'        => $application->years_of_experience,
                'primary_area_of_experience' => $application->primary_area_of_experience,
                'professional_role'          => $application->professional_role,
                'resume_cv'                  => $application->resume_cv ? asset($application->resume_cv) : null,
                'catalogue_id'               => $application->catalogue_id,
                'certification_title'        => $catalogue->title,
                'confirm_accuracy'           => $application->confirm_accuracy,
                'agree_policies'             => $application->agree_policies,
                'status'                     => $application->status,
                'submitted_at'               => $application->created_at->toDateTimeString(),
            ];

            return $this->successResponse($response, 'Your certification application has been submitted successfully.', 201);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to submit certification application.', 500);
        }
    }

    /**
     * Display a listing of the logged-in user's certification applications.
     *
     * GET /apply-for-certification
     */
    public function index()
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            $applications = CertificationApplication::with('catalogue')
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('email', $user->email);
                })
                ->latest('id')
                ->get();

            if ($applications->isEmpty()) {
                return $this->errorResponse([], 'No certification applications found.', 404);
            }

            $mappedData = $applications->map(function ($item) {
                return [
                    'id'                         => $item->id,
                    'reference_number'           => $item->reference_number,
                    'catalogue_id'               => $item->catalogue_id,
                    'certification_title'        => $item->catalogue?->title ?? '',
                    'first_name'                 => $item->first_name,
                    'last_name'                  => $item->last_name,
                    'applicant_name'             => $item->name,
                    'email'                      => $item->email,
                    'phone'                      => $item->phone,
                    'organization'               => $item->organization,
                    'status'                     => $item->status,
                    'admin_notes'                => $item->admin_notes ?? '',
                    'submission_date'            => $item->created_at->format('F j, Y'),
                    'created_at'                 => $item->created_at->toDateTimeString(),
                ];
            });

            $response = [
                'applications' => $mappedData,
            ];

            return $this->successResponse($response, 'Certification applications fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch certification applications.', 500);
        }
    }

    /**
     * Display the specified certification application details.
     *
     * GET /apply-for-certification/{id}
     */
    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse([], 'User not found.', 404);
            }

            $application = CertificationApplication::with('catalogue')->find($id);

            if (!$application) {
                return $this->errorResponse([], 'Certification application not found.', 404);
            }

            if ($application->user_id !== $user->id && $application->email !== $user->email) {
                return $this->errorResponse([], 'Unauthorized access to this application.', 403);
            }

            $response = [
                'application' => [
                    'id'                         => $application->id,
                    'reference_number'           => $application->reference_number,
                    'first_name'                 => $application->first_name,
                    'last_name'                  => $application->last_name,
                    'applicant_name'             => $application->name,
                    'email'                      => $application->email,
                    'phone'                      => $application->phone,
                    'country'                    => $application->country,
                    'city'                       => $application->city,
                    'current_job_title'          => $application->current_job_title,
                    'organization'               => $application->organization,
                    'linkedin_profile'           => $application->linkedin_profile ?? '',
                    'years_of_experience'        => $application->years_of_experience,
                    'primary_area_of_experience' => $application->primary_area_of_experience,
                    'professional_role'          => $application->professional_role,
                    'resume_cv'                  => $application->resume_cv ? asset($application->resume_cv) : null,
                    'catalogue_id'               => $application->catalogue_id,
                    'certification_title'        => $application->catalogue?->title ?? '',
                    'confirm_accuracy'           => $application->confirm_accuracy,
                    'agree_policies'             => $application->agree_policies,
                    'status'                     => $application->status,
                    'admin_notes'                => $application->admin_notes ?? '',
                    'submission_date'            => $application->created_at->format('F j, Y'),
                    'created_at'                 => $application->created_at->toDateTimeString(),
                    'updated_at'                 => $application->updated_at->toDateTimeString(),
                ]
            ];

            return $this->successResponse($response, 'Certification application details fetched successfully.', 200);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to fetch certification application details.', 500);
        }
    }
}

