<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Models\AdminSetting;
use App\Models\User;
use App\Mail\ClientFormSubmissionMail;
use App\Mail\AdminFormSubmissionMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ContactMessageController extends Controller
{
    use ApiResponse;

    /**
     * Store a new contact message.
     *
     * POST /about-contact-message
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name'          => 'required|string|max:255',
                'last_name'           => 'required|string|max:255',
                'email'               => 'required|email|max:255',
                'phone'               => 'nullable|string|max:50',
                'organization'        => 'nullable|string|max:255',
                'service_of_interest' => 'nullable|string|max:255',
                'message'             => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
            }

            $contactMessage = ContactMessage::create([
                'user_id'             => Auth::guard('api')->id(),
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'email'               => $request->email,
                'phone'               => $request->phone        ?? null,
                'organization'        => $request->organization  ?? null,
                'service_of_interest' => $request->service_of_interest ?? null,
                'message'             => $request->message,
                'status'              => 'pending',
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
                    'First Name'          => $contactMessage->first_name,
                    'Last Name'           => $contactMessage->last_name,
                    'Email Address'       => $contactMessage->email,
                    'Phone Number'        => $contactMessage->phone ?? 'N/A',
                    'Organization'        => $contactMessage->organization ?? 'N/A',
                    'Service of Interest' => $contactMessage->service_of_interest ?? 'N/A',
                    'Message'             => $contactMessage->message,
                ];

                $clientMail = new ClientFormSubmissionMail(
                    $contactMessage->name,
                    'Contact Message',
                    $contactMessage->reference_number,
                    $contactMessage->created_at->format('Y-m-d'),
                    $summaryData,
                    'We will review your message and get back to you within 24-48 business hours.',
                    $supportContact
                );

                // 4. Prepare Admin Email
                $clientInfo = [
                    'name'         => $contactMessage->name,
                    'email'        => $contactMessage->email,
                    'phone'        => $contactMessage->phone ?? 'N/A',
                    'organization' => $contactMessage->organization ?? 'N/A',
                ];

                $adminMail = new AdminFormSubmissionMail(
                    'Contact Message',
                    $contactMessage->reference_number,
                    $contactMessage->created_at->format('Y-m-d H:i:s'),
                    $clientInfo,
                    $summaryData,
                    route('admin.contact-messages.show', $contactMessage->id),
                    []
                );

                // 5. Dispatch Mails
                Mail::to($contactMessage->email)->send($clientMail);
                Mail::to($adminEmail)->send($adminMail);

            } catch (Throwable $e) {
                // Log failure gracefully without throwing exception
                Log::error('Failed to send Contact Message notification emails', [
                    'submission_id' => $contactMessage->id,
                    'error_message' => $e->getMessage(),
                    'trace'         => $e->getTraceAsString(),
                ]);
            }

            $response = [
                'id'                  => $contactMessage->id,
                'first_name'          => $contactMessage->first_name,
                'last_name'           => $contactMessage->last_name,
                'email'               => $contactMessage->email,
                'phone'               => $contactMessage->phone               ?? '',
                'organization'        => $contactMessage->organization        ?? '',
                'service_of_interest' => $contactMessage->service_of_interest ?? '',
                'message'             => $contactMessage->message,
                'status'              => $contactMessage->status,
                'submitted_at'        => $contactMessage->created_at->toDateTimeString(),
            ];

            return $this->successResponse($response, 'Your message has been submitted successfully.', 201);

        } catch (Throwable $th) {
            return $this->errorResponse([], 'Failed to submit contact message.', 500);
        }
    }
}
