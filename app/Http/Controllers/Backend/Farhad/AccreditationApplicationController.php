<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\AccreditationApplication;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Mail\AccreditationStatusMail;
use App\Mail\AccreditationPaymentLinkMail;
use App\Traits\LogsEmails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class AccreditationApplicationController extends Controller
{
    use LogsEmails;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $applications = AccreditationApplication::query()->latest('id');

            return DataTables::of($applications)
                ->addIndexColumn()
                ->addColumn('applicant_name', function ($row) {
                    return '<strong>' . e($row->applicant_name) . '</strong>';
                })
                ->addColumn('email_address', function ($row) {
                    return e($row->email_address);
                })
                ->addColumn('program_name', function ($row) {
                    return e($row->program_name);
                })
                ->addColumn('program_type', function ($row) {
                    return e($row->program_type);
                })
                ->addColumn('payment_amount', function ($row) {
                    if ($row->payment_amount > 0) {
                        return '$' . number_format($row->payment_amount, 2);
                    }
                    return '<span class="text-muted">Not Set</span>';
                })
                ->addColumn('payment_status', function ($row) {
                    $pStatus = strtolower($row->payment_status ?? 'unpaid');
                    $badgeClass = match ($pStatus) {
                        'paid'      => 'bg-success',
                        'pending'   => 'bg-warning text-dark',
                        'expired'   => 'bg-dark',
                        'cancelled' => 'bg-danger',
                        default     => 'bg-secondary',
                    };
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($pStatus) . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $status = $row->computed_status ?: 'pending';
                    $badgeClass = 'bg-secondary';

                    $lowerStatus = strtolower($status);
                    if ($lowerStatus == 'pending') $badgeClass = 'bg-warning text-dark';
                    if ($lowerStatus == 'under_review') $badgeClass = 'bg-info text-white';
                    if ($lowerStatus == 'valid' || $lowerStatus == 'accepted') $badgeClass = 'bg-success';
                    if ($lowerStatus == 'revoked') $badgeClass = 'bg-danger';
                    if ($lowerStatus == 'expired') $badgeClass = 'bg-dark';
                    if ($lowerStatus == 'canceled') $badgeClass = 'bg-secondary';
                    if ($lowerStatus == 'completed') $badgeClass = 'bg-primary';

                    return '<span class="badge ' . $badgeClass . '">' . ucfirst(str_replace('_', ' ', $status)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.accreditation-applications.show', $row->id) . '" class="btn btn-sm btn-info me-1" title="View Application">
                                <i class="fa-regular fa-eye"></i>
                            </a>';
                    $action .= '<form action="' . route('admin.accreditation-applications.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger delete-button" title="Delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>';

                    return $action;
                })
                ->rawColumns(['applicant_name', 'payment_amount', 'payment_status', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.accreditation_applications.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $application = AccreditationApplication::findOrFail($id);
        return view('backend.layouts.accreditation_applications.show', compact('application'));
    }

    /**
     * Generate Stripe Payment Link and Send Email to Customer.
     */
    public function generatePaymentLink(Request $request, $id)
    {
        $application = AccreditationApplication::findOrFail($id);

        $request->validate([
            'payment_amount'      => 'required|numeric|min:0.50',
            'validity_days'       => 'nullable|integer|min:1',
            'payment_description' => 'nullable|string|max:1000',
        ]);

        $stripeSecret = config('services.stripe.secret');
        if (empty($stripeSecret)) {
            return back()->with('error', 'Stripe secret key configuration is missing in .env file.');
        }

        try {
            Stripe::setApiKey($stripeSecret);

            $amountInCents = intval(round($request->payment_amount * 100));

            $successUrl = route('accreditation.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl  = URL::signedRoute('accreditation.checkout.cancel', ['accreditation_application_id' => $application->id]);

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency'     => strtolower($application->payment_currency ?? 'usd'),
                        'product_data' => [
                            'name'        => "Accreditation Service: " . $application->program_name,
                            'description' => $request->payment_description ?: ("Accreditation Program for " . $application->applicant_name . " [Ref: " . $application->reference_number . "]"),
                        ],
                        'unit_amount'  => $amountInCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => $successUrl,
                'cancel_url'  => $cancelUrl,
                'metadata'    => [
                    'type'                         => 'accreditation',
                    'accreditation_application_id' => $application->id,
                    'user_id'                      => $application->user_id,
                    'reference_number'             => $application->reference_number,
                ],
            ]);

            $validityDays = $request->validity_days ? (int) $request->validity_days : ($application->validity_days ?: 365);

            // Save details in DB
            $application->update([
                'payment_amount'      => $request->payment_amount,
                'validity_days'       => $validityDays,
                'payment_description' => $request->payment_description,
                'stripe_session_id'   => $session->id,
                'stripe_payment_link' => $session->url,
                'payment_status'      => 'pending',
                'payment_sent_at'     => now(),
            ]);

            // Send Email Notification to Customer
            try {
                $mail = new AccreditationPaymentLinkMail(
                    $application,
                    (float) $request->payment_amount,
                    $session->url,
                    $request->payment_description
                );
                Mail::to($application->email_address)->send($mail);

                $this->logEmail(
                    $application->user_id,
                    $application->email_address,
                    'user',
                    $mail->envelope()->subject,
                    'accreditation_payment_link',
                    $application
                );
            } catch (\Throwable $mailEx) {
                Log::error('Failed to send Accreditation Payment Link Email: ' . $mailEx->getMessage());
            }

            return back()->with('success', 'Payment link generated successfully and email sent to ' . $application->email_address);

        } catch (\Throwable $th) {
            Log::error('Stripe Payment Link Generation Error: ' . $th->getMessage());
            return back()->with('error', 'Failed to generate Stripe payment link: ' . $th->getMessage());
        }
    }

    /**
     * Update status of the accreditation application.
     */
    public function updateStatus(Request $request, $id)
    {
        $application = AccreditationApplication::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,under_review,valid,accepted,revoked,expired,canceled,completed',
            'payment_status' => 'nullable|string|in:pending,paid,expired,cancelled,unpaid',
            'admin_notes' => 'nullable|string',
            'issued_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);

        $oldStatus = $application->status;
        $newStatus = $request->status;

        $application->status = $newStatus;
        $application->admin_notes = $request->admin_notes;

        if ($request->has('payment_status')) {
            $oldPaymentStatus = $application->payment_status;
            $application->payment_status = $request->payment_status;
            if ($request->payment_status === 'paid' && $oldPaymentStatus !== 'paid') {
                $application->payment_date = now();
                
                // Create a manual purchase record if it doesn't exist
                if ($application->user_id) {
                    \App\Models\Purchase::firstOrCreate(
                        [
                            'user_id' => $application->user_id,
                            'purchase_type' => 'accreditation',
                            'accreditation_application_id' => $application->id,
                            'payment_status' => 'paid',
                        ],
                        [
                            'amount' => $application->payment_amount ?: 0.00,
                            'price_regular' => $application->payment_amount ?: 0.00,
                            'price_purchased' => $application->payment_amount ?: 0.00,
                            'order_status' => 'completed',
                            'payment_method' => 'Manual',
                            'stripe_session_id' => 'MANUAL-' . uniqid(),
                        ]
                    );
                }
            }
        }

        if ($request->has('issued_at')) {
            $application->issued_at = $request->filled('issued_at') ? \Carbon\Carbon::parse($request->issued_at) : null;
        }

        if ($request->has('expires_at')) {
            $application->expires_at = $request->filled('expires_at') ? \Carbon\Carbon::parse($request->expires_at) : null;
        }

        // ONLY generate the certificate if the status is valid AND the application is paid
        if ($newStatus === 'valid' && $application->payment_status === 'paid') {
            if (!$application->issued_at) {
                $application->issued_at = now();
            }
            // If expires_at is not set, we leave it null to signify "Ongoing"

            // Generate PDF certificate
            \App\Services\AccreditationCertificateService::generatePdf($application);
        }

        $application->save();

        if ($application->status !== 'pending') {
            $actionLink = env('FRONTEND_URL', 'https://gihqs.vercel.app') . '/dashboard';

            try {
                $mail = new AccreditationStatusMail(
                    $application,
                    $application->status,
                    $application->admin_notes,
                    $actionLink
                );
                Mail::to($application->email_address)->send($mail);

                // Log email to database
                $this->logEmail(
                    $application->user_id,
                    $application->email_address,
                    'user',
                    $mail->envelope()->subject,
                    'accreditation_' . $application->status,
                    $application
                );
            } catch (\Throwable $e) {
                Log::error('Failed to send accreditation status email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Application status updated successfully');
    }

    /**
     * Manually regenerate accreditation certificate PDF.
     */
    public function regenerateCertificate($id)
    {
        $application = AccreditationApplication::findOrFail($id);

        if ($application->status !== 'valid' || $application->payment_status !== 'paid') {
            return back()->with('error', 'Cannot generate certificate for unpaid or invalid status.');
        }

        $pdfUrl = \App\Services\AccreditationCertificateService::generatePdf($application);

        if ($pdfUrl) {
            return back()->with('success', 'Accreditation certificate generated successfully');
        }

        return back()->with('error', 'Failed to generate accreditation certificate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $application = AccreditationApplication::findOrFail($id);
        $application->delete();
        return redirect()->back()->with('success', 'Accreditation application deleted successfully');
    }
}
