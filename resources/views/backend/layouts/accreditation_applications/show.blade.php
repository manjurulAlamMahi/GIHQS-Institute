@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Accreditation Applications</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.accreditation-applications.index') }}">Accreditation Applications</a></li>
                        <li class="breadcrumb-item active">View Application</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Accreditation Application Details</h4>
                    <span class="badge bg-primary" style="font-size: 14px; padding: 6px 12px;">Ref: {{ $application->reference_number }}</span>
                </div>

                <div class="card-body">
                    <div class="row gy-4">

                        {{-- 1. Applicant Information Header --}}
                        <div class="col-xxl-12 col-md-12">
                            <h5 class="text-primary border-bottom pb-2">1. Applicant Information</h5>
                        </div>

                        {{-- Applicant Category --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Applicant Category</strong></label>
                                <p>{{ $application->applicant_category ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Applicant Name --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Applicant Name</strong></label>
                                <p>{{ $application->applicant_name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Department/Division --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Department / Division</strong></label>
                                <p>{{ $application->department_division ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Country --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Country</strong></label>
                                <p>{{ $application->country ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- City --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>City</strong></label>
                                <p>{{ $application->city ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Website URL --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Website URL</strong></label>
                                <p>
                                    @if($application->website_url)
                                        <a href="{{ $application->website_url }}" target="_blank">{{ $application->website_url }}</a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Year Established --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Year Established</strong></label>
                                <p>{{ $application->year_established ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- 2. Program Information Header --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">2. Program Information</h5>
                        </div>

                        {{-- Program Name --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Program Name</strong></label>
                                <p>{{ $application->program_name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Program Type --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Program Type</strong></label>
                                <p>{{ $application->program_type ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Program Delivery Format --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Delivery Format</strong></label>
                                <p>{{ $application->program_delivery_format ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Estimated Annual Participants --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Estimated Annual Participants</strong></label>
                                <p>{{ $application->estimated_annual_participants ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Primary Language of Instruction --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Primary Language of Instruction</strong></label>
                                <p>{{ $application->primary_language_of_instruction ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Program Launch Date --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Program Launch Date</strong></label>
                                <p>{{ $application->program_launch_date ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- 3. Primary Contact Information Header --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">3. Primary Contact Information</h5>
                        </div>

                        {{-- Contact Person --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Contact Person</strong></label>
                                <p>{{ $application->primary_contact_person ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Title / Position --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Title / Position</strong></label>
                                <p>{{ $application->contact_title_position ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Email Address --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Email Address</strong></label>
                                <p><a href="mailto:{{ $application->email_address }}">{{ $application->email_address ?? 'N/A' }}</a></p>
                            </div>
                        </div>

                        {{-- Phone Number --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Phone Number</strong></label>
                                <p>
                                    @if($application->phone_number)
                                        <a href="tel:{{ $application->phone_number }}">{{ $application->phone_number }}</a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- 4. Supporting Attachments Header --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">4. Supporting Attachments</h5>
                        </div>

                        {{-- Program Overview Doc --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Program Overview Document</strong></label>
                                <p>
                                    @if($application->program_overview_doc)
                                        <a href="{{ asset($application->program_overview_doc) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="ri-file-download-line me-1"></i> View / Download Overview
                                        </a>
                                    @else
                                        <span class="text-muted">No document uploaded.</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Governance Policy Doc --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Governance Policy Document</strong></label>
                                <p>
                                    @if($application->governance_policy_doc)
                                        <a href="{{ asset($application->governance_policy_doc) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="ri-file-download-line me-1"></i> View / Download Policy
                                        </a>
                                    @else
                                        <span class="text-muted">No document uploaded.</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- 5. Additional Information Header --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">5. Additional Information</h5>
                        </div>

                        <div class="col-xxl-12 col-md-12">
                            <div class="border rounded p-3 bg-light">
                                <p>{!! nl2br(e($application->additional_information ?? 'No additional details provided.')) !!}</p>
                            </div>
                        </div>

                        {{-- Status & Date --}}
                        <div class="col-xxl-6 col-md-6 mt-4">
                            <div>
                                <label class="form-label"><strong>Current Status</strong></label>
                                <p>
                                    @php
                                        $status = $application->computed_status ?: 'pending';

                                        $badgeClass = match (strtolower($status)) {
                                            'pending' => 'bg-warning text-dark',
                                            'under_review' => 'bg-info text-white',
                                            'valid', 'accepted' => 'bg-success',
                                            'revoked' => 'bg-danger',
                                            'expired' => 'bg-dark',
                                            'canceled' => 'bg-secondary',
                                            'completed' => 'bg-primary',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="font-size: 13px; padding: 6px 12px;">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6 mt-4">
                            <div>
                                <label class="form-label"><strong>Verification ID</strong></label>
                                <p><code>{{ $application->verification_code ?: $application->reference_number }}</code></p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Issued Date</strong></label>
                                <p>{{ $application->issued_at ? $application->issued_at->format('d M Y') : 'Not Issued' }}</p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Valid Until (Expiry)</strong></label>
                                <p>{{ $application->expires_at ? $application->expires_at->format('d M Y') : 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Certificate PDF Download --}}
                        @if($application->certificate_pdf)
                            <div class="col-xxl-12 col-md-12">
                                <div class="alert alert-success d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class="ri-checkbox-circle-line fs-18 me-2"></i>
                                        <strong>Accreditation Certificate PDF Generated</strong>
                                    </div>
                                    <div>
                                        <a href="{{ asset($application->certificate_pdf) }}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="ri-download-2-line me-1"></i> Download Certificate PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Payment Overview Card --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">Payment & Validity Management</h5>

                            @if($application->stripe_payment_link || $application->payment_amount > 0)
                                <div class="card border border-info shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-dark fw-semibold"><i class="ri-wallet-3-line me-1"></i> Current Payment & Validity Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row gy-3">
                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Fee Amount</label>
                                                <h5 class="text-success fw-bold">${{ number_format($application->payment_amount, 2) }} {{ strtoupper($application->payment_currency ?? 'USD') }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Payment Status</label>
                                                <div>
                                                    @php
                                                        $pStatus = strtolower($application->payment_status ?? 'unpaid');
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
                                                <p class="mb-0 text-dark fw-semibold"><i class="ri-time-line me-1"></i> {{ $application->validity_days ?: 365 }} Days</p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Expiration Date</label>
                                                <p class="mb-0 text-dark fw-semibold">
                                                    @if($application->expires_at)
                                                        <span class="{{ $application->expires_at->isPast() ? 'text-danger' : 'text-primary' }}">
                                                            {{ $application->expires_at->format('d M Y, H:i A') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Will set upon payment</span>
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Stripe Payment Intent / Ref</label>
                                                <p class="mb-0 text-dark fw-medium"><code>{{ $application->stripe_payment_intent_id ?: 'N/A' }}</code></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Payment Completion Date</label>
                                                <p class="mb-0 text-dark">{{ $application->payment_date ? $application->payment_date->format('d M Y, H:i A') : 'Not Paid Yet' }}</p>
                                            </div>

                                            @if($application->payment_description)
                                                <div class="col-md-12">
                                                    <label class="form-label text-muted mb-1">Payment Description / Notes</label>
                                                    <p class="mb-0 text-muted border rounded p-2 bg-white">{{ $application->payment_description }}</p>
                                                </div>
                                            @endif

                                            @if($application->stripe_payment_link)
                                                <div class="col-md-12">
                                                    <label class="form-label text-muted mb-1">Generated Stripe Checkout Link</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="stripePaymentLinkInput" value="{{ $application->stripe_payment_link }}" readonly>
                                                        <a href="{{ $application->stripe_payment_link }}" target="_blank" class="btn btn-primary"><i class="ri-external-link-line me-1"></i> Open Link</a>
                                                        <button type="button" class="btn btn-outline-secondary" onclick="copyStripeLink()"><i class="ri-file-copy-line me-1"></i> Copy</button>
                                                    </div>
                                                    @if($application->payment_sent_at)
                                                        <small class="text-muted mt-1 d-block">Link generated & emailed on: {{ $application->payment_sent_at->format('d M Y, H:i A') }}</small>
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
                                    <h6 class="mb-0 text-primary fw-semibold"><i class="ri-bank-card-line me-1"></i> {{ $application->stripe_payment_link ? 'Regenerate / Update Custom Payment Fee & Validity' : 'Set Custom Fee, Validity & Generate Stripe Payment Link' }}</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.accreditation-applications.generate-payment-link', $application->id) }}" method="POST">
                                        @csrf
                                        <div class="row gy-3">
                                            <div class="col-md-4">
                                                <label for="payment_amount" class="form-label"><strong>Payment Amount ($ USD)</strong> <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0.50" class="form-control @error('payment_amount') is-invalid @enderror" id="payment_amount" name="payment_amount" value="{{ old('payment_amount', $application->payment_amount) }}" placeholder="e.g. 1500.00" required>
                                                </div>
                                                @error('payment_amount')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label for="validity_days" class="form-label"><strong>Validity Period (Days)</strong> <span class="text-danger">*</span></label>
                                                <input type="number" min="1" class="form-control @error('validity_days') is-invalid @enderror" id="validity_days" name="validity_days" value="{{ old('validity_days', $application->validity_days ?: 365) }}" placeholder="e.g. 365, 730" required>
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
                                                <textarea class="form-control @error('payment_description') is-invalid @enderror" id="payment_description" name="payment_description" rows="3" placeholder="Enter invoice details or payment reference notes visible to client...">{{ old('payment_description', $application->payment_description) }}</textarea>
                                                @error('payment_description')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ri-send-plane-fill me-1"></i> {{ $application->stripe_payment_link ? 'Update Fee & Resend Payment Link' : 'Generate Stripe Link & Email Client' }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Status & Notes Form --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">6. Administrative Action</h5>
                            <form action="{{ route('admin.accreditation-applications.update-status', $application->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="row gy-3">
                                     <div class="col-md-3">
                                         <label for="statusSelect" class="form-label"><strong>Update Status</strong></label>
                                         <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect">
                                             <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                             <option value="under_review" {{ $application->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                             <option value="accepted" {{ $application->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                             <option value="valid" {{ $application->status == 'valid' ? 'selected' : '' }}>Valid</option>
                                             <option value="expired" {{ $application->status == 'expired' ? 'selected' : '' }}>Expired</option>
                                             <option value="canceled" {{ $application->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                         </select>
                                         @error('status')
                                             <span class="text-danger">{{ $message }}</span>
                                         @enderror
                                     </div>

                                     <div class="col-md-3">
                                         <label for="paymentStatusSelect" class="form-label"><strong>Payment Status</strong></label>
                                         <select class="form-select @error('payment_status') is-invalid @enderror" name="payment_status" id="paymentStatusSelect">
                                             <option value="pending" {{ $application->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                             <option value="paid" {{ $application->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                             <option value="expired" {{ $application->payment_status == 'expired' ? 'selected' : '' }}>Expired</option>
                                             <option value="cancelled" {{ in_array($application->payment_status, ['cancelled', 'cancelled']) ? 'selected' : '' }}>Cancelled</option>
                                             <option value="unpaid" {{ in_array($application->payment_status, ['unpaid', '']) || is_null($application->payment_status) ? 'selected' : '' }}>Unpaid</option>
                                         </select>
                                         @error('payment_status')
                                             <span class="text-danger">{{ $message }}</span>
                                         @enderror
                                     </div>

                                     <div class="col-md-3">
                                         <label for="issuedAt" class="form-label"><strong>Issue Date</strong></label>
                                         <input type="date" class="form-control" id="issuedAt" name="issued_at" value="{{ $application->issued_at ? $application->issued_at->format('Y-m-d') : '' }}">
                                     </div>

                                     <div class="col-md-3">
                                         <label for="expiresAt" class="form-label"><strong>Expiry Date (1-Year Default)</strong></label>
                                         <input type="date" class="form-control" id="expiresAt" name="expires_at" value="{{ $application->expires_at ? $application->expires_at->format('Y-m-d') : '' }}">
                                     </div>

                                    <div class="col-md-12">
                                        <label for="adminNotes" class="form-label"><strong>Admin Notes</strong></label>
                                        <textarea class="form-control @error('admin_notes') is-invalid @enderror" id="adminNotes" name="admin_notes" rows="4" placeholder="Enter administrative notes here...">{{ old('admin_notes', $application->admin_notes) }}</textarea>
                                        @error('admin_notes')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 d-flex gap-2">
                                        <button type="submit" class="btn btn-success">Update Status & Save</button>
                                    </div>
                                </div>
                            </form>

                            <form action="{{ route('admin.accreditation-applications.regenerate-certificate', $application->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary" {{ ($application->status !== 'valid' || $application->payment_status !== 'paid') ? 'disabled' : '' }}>
                                    <i class="ri-refresh-line me-1"></i> Generate / Regenerate Certificate PDF
                                </button>
                                @if($application->status !== 'valid' || $application->payment_status !== 'paid')
                                    <small class="text-danger ms-2"><i class="ri-error-warning-line align-middle"></i> Certificate generation requires 'Valid' status and 'Paid' payment status.</small>
                                @endif
                            </form>
                        </div>

                        {{-- Back Button --}}
                        <div class="col-xxl-12 col-md-12 mt-3 border-top pt-3">
                            <a href="{{ route('admin.accreditation-applications.index') }}" class="btn btn-secondary">Back to Applications</a>
                            <form action="{{ route('admin.accreditation-applications.destroy', $application->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger delete-button">Delete Application</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyStripeLink() {
            var copyText = document.getElementById("stripePaymentLinkInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            alert("Stripe Payment Link copied to clipboard!");
        }
    </script>
@endpush
