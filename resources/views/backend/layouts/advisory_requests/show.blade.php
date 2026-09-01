@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Advisory Requests</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.advisory-requests.index') }}">Advisory Requests</a></li>
                        <li class="breadcrumb-item active">View Request</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-10">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-double-line me-1 align-middle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-1 align-middle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Advisory Request Details</h4>
                    <span class="badge bg-primary" style="font-size: 14px; padding: 6px 12px;">Ref: {{ $advisoryRequest->reference_number }}</span>
                </div>

                <div class="card-body">
                    <div class="row gy-4">

                        {{-- Organization Information Header --}}
                        <div class="col-xxl-12 col-md-12">
                            <h5 class="text-primary border-bottom pb-2">Organization Information</h5>
                        </div>

                        {{-- Organization Name --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Organization Name</strong></label>
                                <p>{{ $advisoryRequest->organization_name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Organization Type --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Organization Type</strong></label>
                                <p>{{ $advisoryRequest->organization_type ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Representative Information Header --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">Representative Information</h5>
                        </div>

                        {{-- Representative Name --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Full Name</strong></label>
                                <p>{{ $advisoryRequest->full_name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Representative Email --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Work Email</strong></label>
                                <p><a href="mailto:{{ $advisoryRequest->work_email }}">{{ $advisoryRequest->work_email ?? 'N/A' }}</a></p>
                            </div>
                        </div>

                        {{-- Phone Number --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Phone Number</strong></label>
                                <p>
                                    @if($advisoryRequest->phone_number)
                                        <a href="tel:{{ $advisoryRequest->phone_number }}">{{ $advisoryRequest->phone_number }}</a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Country --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Country</strong></label>
                                <p>{{ $advisoryRequest->country ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Request Details Header --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">Request Details</h5>
                        </div>

                        {{-- Service of Interest --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Service of Interest</strong></label>
                                <p>{{ $advisoryRequest->service_of_interest ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Desired Timeline --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Desired Timeline</strong></label>
                                <p>{{ $advisoryRequest->desired_timeline ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Description of Needs --}}
                        <div class="col-xxl-12 col-md-12">
                            <div>
                                <label class="form-label"><strong>Description of Needs</strong></label>
                                <div class="border rounded p-3 bg-light">
                                    <p>{!! nl2br(e($advisoryRequest->description_of_needs ?? 'N/A')) !!}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Status & Date --}}
                        <div class="col-xxl-6 col-md-6 mt-4">
                            <div>
                                <label class="form-label"><strong>Current Request Status</strong></label>
                                <p>
                                    @php
                                        $status = trim($advisoryRequest->status);
                                        if (empty($status)) $status = 'pending';

                                        $badgeClass = match (strtolower($status)) {
                                            'pending' => 'bg-danger',
                                            'accepted' => 'bg-success',
                                            'canceled' => 'bg-dark',
                                            'completed' => 'bg-primary',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6 mt-4">
                            <div>
                                <label class="form-label"><strong>Submitted On</strong></label>
                                <p>{{ $advisoryRequest->created_at->format('d M Y, H:i A') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Payment Overview Card --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">Payment & Validity Management</h5>

                            @if($advisoryRequest->stripe_payment_link || $advisoryRequest->payment_amount > 0)
                                <div class="card border border-info shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-dark fw-semibold"><i class="fa-solid fa-credit-card me-1"></i> Current Payment & Validity Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row gy-3">
                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Fee Amount</label>
                                                <h5 class="text-success fw-bold">${{ number_format($advisoryRequest->payment_amount, 2) }} {{ strtoupper($advisoryRequest->payment_currency ?? 'USD') }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Payment Status</label>
                                                <div>
                                                    @php
                                                        $pStatus = strtolower($advisoryRequest->payment_status ?? 'unpaid');
                                                        $pBadgeClass = match ($pStatus) {
                                                            'paid'      => 'bg-success',
                                                            'pending'   => 'bg-warning text-dark',
                                                            'expired'   => 'bg-dark',
                                                            'cancelled' => 'bg-danger',
                                                            default     => 'bg-secondary',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $pBadgeClass }} fs-13 px-3 py-2">{{ ucfirst($pStatus) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Service Validity</label>
                                                <p class="mb-0 text-dark fw-semibold"><i class="fa-regular fa-clock me-1"></i> {{ $advisoryRequest->validity_days ?: 30 }} Days</p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Expiration Date</label>
                                                <p class="mb-0 text-dark fw-semibold">
                                                    @if($advisoryRequest->expires_at)
                                                        <span class="{{ $advisoryRequest->expires_at->isPast() ? 'text-danger' : 'text-primary' }}">
                                                            {{ $advisoryRequest->expires_at->format('d M Y, H:i A') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Will set upon payment</span>
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Stripe Payment Intent / Ref</label>
                                                <p class="mb-0 text-dark fw-medium"><code>{{ $advisoryRequest->stripe_payment_intent_id ?: 'N/A' }}</code></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Payment Completion Date</label>
                                                <p class="mb-0 text-dark">{{ $advisoryRequest->payment_date ? $advisoryRequest->payment_date->format('d M Y, H:i A') : 'Not Paid Yet' }}</p>
                                            </div>

                                            @if($advisoryRequest->payment_description)
                                                <div class="col-md-12">
                                                    <label class="form-label text-muted mb-1">Payment Description / Notes</label>
                                                    <p class="mb-0 text-muted border rounded p-2 bg-white">{{ $advisoryRequest->payment_description }}</p>
                                                </div>
                                            @endif

                                            @if($advisoryRequest->stripe_payment_link)
                                                <div class="col-md-12">
                                                    <label class="form-label text-muted mb-1">Generated Stripe Checkout Link</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="stripePaymentLinkInput" value="{{ $advisoryRequest->stripe_payment_link }}" readonly>
                                                        <a href="{{ $advisoryRequest->stripe_payment_link }}" target="_blank" class="btn btn-primary"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Link</a>
                                                        <button type="button" class="btn btn-outline-secondary" onclick="copyStripeLink()"><i class="fa-regular fa-copy me-1"></i> Copy</button>
                                                    </div>
                                                    @if($advisoryRequest->payment_sent_at)
                                                        <small class="text-muted mt-1 d-block">Link generated & emailed on: {{ $advisoryRequest->payment_sent_at->format('d M Y, H:i A') }}</small>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Form to Set Amount, Validity & Generate Stripe Link --}}
                            <div class="card border border-primary">
                                <div class="card-header bg-primary-subtle">
                                    <h6 class="mb-0 text-primary fw-semibold"><i class="fa-solid fa-calculator me-1"></i> {{ $advisoryRequest->stripe_payment_link ? 'Regenerate / Update Custom Payment Fee & Validity' : 'Set Custom Fee, Validity & Generate Stripe Payment Link' }}</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.advisory-requests.generate-payment-link', $advisoryRequest->id) }}" method="POST">
                                        @csrf
                                        <div class="row gy-3">
                                            <div class="col-md-4">
                                                <label for="payment_amount" class="form-label"><strong>Payment Amount ($ USD)</strong> <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0.50" class="form-control @error('payment_amount') is-invalid @enderror" id="payment_amount" name="payment_amount" value="{{ old('payment_amount', $advisoryRequest->payment_amount) }}" placeholder="e.g. 500.00" required>
                                                </div>
                                                @error('payment_amount')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label for="validity_days" class="form-label"><strong>Validity Period (Days)</strong> <span class="text-danger">*</span></label>
                                                <input type="number" min="1" class="form-control @error('validity_days') is-invalid @enderror" id="validity_days" name="validity_days" value="{{ old('validity_days', $advisoryRequest->validity_days ?: 30) }}" placeholder="e.g. 30, 60, 90, 365" required>
                                                @error('validity_days')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label for="payment_currency" class="form-label"><strong>Currency</strong></label>
                                                <input type="text" class="form-control" id="payment_currency" value="USD ($)" readonly>
                                            </div>

                                            <div class="col-md-12">
                                                <label for="payment_description" class="form-label"><strong>Payment Description / Note for Invoice Email</strong></label>
                                                <textarea class="form-control @error('payment_description') is-invalid @enderror" id="payment_description" name="payment_description" rows="3" placeholder="Enter invoice details or payment reference notes visible to client...">{{ old('payment_description', $advisoryRequest->payment_description) }}</textarea>
                                                @error('payment_description')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa-solid fa-paper-plane me-1"></i> {{ $advisoryRequest->stripe_payment_link ? 'Update Fee & Resend Payment Link' : 'Generate Stripe Link & Email Client' }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Status & Notes Form --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">Administrative Action & Manual Expiry Override</h5>
                            <form action="{{ route('admin.advisory-requests.update-status', $advisoryRequest->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="row gy-3">
                                    <div class="col-md-3">
                                        <label for="statusSelect" class="form-label"><strong>Update Request Status</strong></label>
                                        <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect">
                                            <option value="pending" {{ $advisoryRequest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="accepted" {{ $advisoryRequest->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                            <option value="canceled" {{ $advisoryRequest->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                            <option value="completed" {{ $advisoryRequest->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="paymentStatusSelect" class="form-label"><strong>Payment Status</strong></label>
                                        <select class="form-select @error('payment_status') is-invalid @enderror" name="payment_status" id="paymentStatusSelect">
                                            <option value="pending" {{ $advisoryRequest->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="paid" {{ $advisoryRequest->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="expired" {{ $advisoryRequest->payment_status == 'expired' ? 'selected' : '' }}>Expired</option>
                                            <option value="cancelled" {{ in_array($advisoryRequest->payment_status, ['cancelled', 'cancelled']) ? 'selected' : '' }}>Cancelled</option>
                                            <option value="unpaid" {{ in_array($advisoryRequest->payment_status, ['unpaid', '']) || is_null($advisoryRequest->payment_status) ? 'selected' : '' }}>Unpaid</option>
                                        </select>
                                        @error('payment_status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="admin_validity_days" class="form-label"><strong>Validity (Days)</strong></label>
                                        <input type="number" min="1" class="form-control @error('validity_days') is-invalid @enderror" id="admin_validity_days" name="validity_days" value="{{ old('validity_days', $advisoryRequest->validity_days ?: 30) }}">
                                        @error('validity_days')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="expires_at" class="form-label"><strong>Expiration Date</strong></label>
                                        <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" value="{{ old('expires_at', $advisoryRequest->expires_at ? $advisoryRequest->expires_at->format('Y-m-d\TH:i') : '') }}">
                                        @error('expires_at')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="adminNotes" class="form-label"><strong>Admin Notes</strong></label>
                                        <textarea class="form-control @error('admin_notes') is-invalid @enderror" id="adminNotes" name="admin_notes" rows="4" placeholder="Enter administrative notes here...">{{ old('admin_notes', $advisoryRequest->admin_notes) }}</textarea>
                                        @error('admin_notes')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-success">Update Status, Validity & Notes</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Back Button --}}
                        <div class="col-xxl-12 col-md-12 mt-3 border-top pt-3">
                            <a href="{{ route('admin.advisory-requests.index') }}" class="btn btn-secondary">Back to Requests</a>
                            <form action="{{ route('admin.advisory-requests.destroy', $advisoryRequest->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger delete-button">Delete Request</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyStripeLink() {
            var copyText = document.getElementById("stripePaymentLinkInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            alert("Stripe Payment Link copied to clipboard!");
        }
    </script>
@endsection
