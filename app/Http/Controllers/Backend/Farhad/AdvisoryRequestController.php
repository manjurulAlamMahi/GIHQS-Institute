<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\AdvisoryRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Mail\AdvisoryStatusMail;
use App\Mail\AdvisoryPaymentLinkMail;
use App\Traits\LogsEmails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class AdvisoryRequestController extends Controller
{
    use LogsEmails;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $requests = AdvisoryRequest::query()->latest('id');

            return DataTables::of($requests)
                ->addIndexColumn()
                ->addColumn('organization_name', function ($row) {
                    return e($row->organization_name);
                })
                ->addColumn('full_name', function ($row) {
                    return '<strong>' . e($row->full_name) . '</strong>';
                })
                ->addColumn('work_email', function ($row) {
                    return e($row->work_email);
                })
                ->addColumn('service_of_interest', function ($row) {
                    return e($row->service_of_interest);
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
                    $status = $row->status ? trim($row->status) : 'pending';
                    $badgeClass = 'bg-secondary';

                    $lowerStatus = strtolower($status);
                    if ($lowerStatus == 'pending') $badgeClass = 'bg-danger';
                    if ($lowerStatus == 'accepted') $badgeClass = 'bg-success';
                    if ($lowerStatus == 'canceled') $badgeClass = 'bg-dark';
                    if ($lowerStatus == 'completed') $badgeClass = 'bg-primary';

                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.advisory-requests.show', $row->id) . '" class="btn btn-sm btn-info me-1" title="View & Manage Payment">
                                <i class="fa-regular fa-eye"></i>
                            </a>';
                    $action .= '<form action="' . route('admin.advisory-requests.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger delete-button" title="Delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>';

                    return $action;
                })
                ->rawColumns(['full_name', 'payment_amount', 'payment_status', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.advisory_requests.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $advisoryRequest = AdvisoryRequest::findOrFail($id);
        return view('backend.layouts.advisory_requests.show', compact('advisoryRequest'));
    }

    /**
     * Generate Stripe Payment Link and Send Email to Customer.
     */
    public function generatePaymentLink(Request $request, $id)
    {
        $advisoryRequest = AdvisoryRequest::findOrFail($id);

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

            $successUrl = route('advisory.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl  = URL::signedRoute('advisory.checkout.cancel', ['advisory_request_id' => $advisoryRequest->id]);

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency'     => strtolower($advisoryRequest->payment_currency ?? 'usd'),
                        'product_data' => [
                            'name'        => "Advisory Service: " . $advisoryRequest->service_of_interest,
                            'description' => $request->payment_description ?: ("Advisory Consultation for " . $advisoryRequest->organization_name . " [Ref: " . $advisoryRequest->reference_number . "]"),
                        ],
                        'unit_amount'  => $amountInCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => $successUrl,
                'cancel_url'  => $cancelUrl,
                'metadata'    => [
                    'type'                => 'advisory_request',
                    'advisory_request_id' => $advisoryRequest->id,
                    'user_id'             => $advisoryRequest->user_id,
                    'reference_number'    => $advisoryRequest->reference_number,
                ],
            ]);

            $validityDays = $request->validity_days ? (int) $request->validity_days : ($advisoryRequest->validity_days ?: 30);

            // Save details in DB
            $advisoryRequest->update([
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
                $mail = new AdvisoryPaymentLinkMail(
                    $advisoryRequest,
                    (float) $request->payment_amount,
                    $session->url,
                    $request->payment_description
                );
                Mail::to($advisoryRequest->work_email)->send($mail);

                $this->logEmail(
                    $advisoryRequest->user_id,
                    $advisoryRequest->work_email,
                    'user',
                    $mail->envelope()->subject,
                    'advisory_payment_link',
                    $advisoryRequest
                );
            } catch (\Throwable $mailEx) {
                Log::error('Failed to send Advisory Payment Link Email: ' . $mailEx->getMessage());
            }

            return back()->with('success', 'Payment link generated successfully and email sent to ' . $advisoryRequest->work_email);

        } catch (\Throwable $th) {
            Log::error('Stripe Payment Link Generation Error: ' . $th->getMessage());
            return back()->with('error', 'Failed to generate Stripe payment link: ' . $th->getMessage());
        }
    }

    /**
     * Update status of the advisory request.
     */
    public function updateStatus(Request $request, $id)
    {
        $advisoryRequest = AdvisoryRequest::findOrFail($id);

        $request->validate([
            'status'         => 'required|in:pending,accepted,canceled,completed',
            'payment_status' => 'nullable|string|in:pending,paid,expired,cancelled,unpaid',
            'admin_notes'    => 'nullable|string',
            'validity_days'  => 'nullable|integer|min:1',
            'expires_at'     => 'nullable|date',
        ]);

        $oldStatus = $advisoryRequest->status;
        $advisoryRequest->status = $request->status;
        $advisoryRequest->admin_notes = $request->admin_notes;

        if ($request->has('payment_status')) {
            $oldPaymentStatus = $advisoryRequest->payment_status;
            $advisoryRequest->payment_status = $request->payment_status;
            if ($request->payment_status === 'paid' && $oldPaymentStatus !== 'paid') {
                $advisoryRequest->payment_date = now();
                
                // Create a manual purchase record if it doesn't exist
                if ($advisoryRequest->user_id) {
                    \App\Models\Purchase::firstOrCreate(
                        [
                            'user_id'             => $advisoryRequest->user_id,
                            'purchase_type'       => 'advisory',
                            'advisory_request_id' => $advisoryRequest->id,
                            'payment_status'      => 'paid',
                        ],
                        [
                            'amount'            => $advisoryRequest->payment_amount ?: 0.00,
                            'price_regular'     => $advisoryRequest->payment_amount ?: 0.00,
                            'price_purchased'   => $advisoryRequest->payment_amount ?: 0.00,
                            'order_status'      => 'completed',
                            'payment_method'    => 'Manual',
                            'stripe_session_id' => 'MANUAL-' . uniqid(),
                        ]
                    );
                }
            }
        }
        
        if ($request->filled('validity_days')) {
            $advisoryRequest->validity_days = (int) $request->validity_days;
        }

        if ($request->filled('expires_at')) {
            $advisoryRequest->expires_at = $request->expires_at;
        }

        $advisoryRequest->save();

        if ($oldStatus !== $advisoryRequest->status) {
            if (in_array($advisoryRequest->status, ['accepted', 'canceled'])) {
                $actionLink = env('FRONTEND_URL', 'https://gihqs.vercel.app') . '/dashboard';

                try {
                    $mail = new AdvisoryStatusMail(
                        $advisoryRequest,
                        $advisoryRequest->status,
                        $advisoryRequest->admin_notes,
                        $actionLink
                    );
                    Mail::to($advisoryRequest->work_email)->send($mail);

                    // Log email to database
                    $this->logEmail(
                        $advisoryRequest->user_id,
                        $advisoryRequest->work_email,
                        'user',
                        $mail->envelope()->subject,
                        'advisory_' . $advisoryRequest->status,
                        $advisoryRequest
                    );
                } catch (\Throwable $e) {
                    Log::error('Failed to send advisory status email: ' . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Request status and notes updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $advisoryRequest = AdvisoryRequest::findOrFail($id);
        $advisoryRequest->delete();
        return redirect()->back()->with('success', 'Advisory request deleted successfully');
    }
}
