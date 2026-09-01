@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Certification Applications</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.certification-applications.index') }}">Certification Applications</a></li>
                        <li class="breadcrumb-item active">View Application</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Application Details</h4>
                    <span class="badge bg-primary" style="font-size: 14px; padding: 6px 12px;">Ref: {{ $application->reference_number }}</span>
                </div>

                <div class="card-body">
                    <div class="row gy-4">

                        {{-- 1. Applicant Information Header --}}
                        <div class="col-xxl-12 col-md-12">
                            <h5 class="text-primary border-bottom pb-2">1. Applicant Information</h5>
                        </div>

                        {{-- Name --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Full Name</strong></label>
                                <p>{{ $application->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Email</strong></label>
                                <p><a href="mailto:{{ $application->email }}">{{ $application->email ?? 'N/A' }}</a></p>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Phone</strong></label>
                                <p>
                                    @if($application->phone)
                                        <a href="tel:{{ $application->phone }}">{{ $application->phone }}</a>
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

                        {{-- Current Job Title --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Current Job Title</strong></label>
                                <p>{{ $application->current_job_title ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Organization --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Organization</strong></label>
                                <p>{{ $application->organization ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- LinkedIn Profile --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>LinkedIn Profile</strong></label>
                                <p>
                                    @if($application->linkedin_profile)
                                        <a href="{{ $application->linkedin_profile }}" target="_blank">{{ $application->linkedin_profile }}</a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- 2. Professional Background Header --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">2. Professional Background</h5>
                        </div>

                        {{-- Years of Experience --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Years of Experience</strong></label>
                                <p>{{ $application->years_of_experience ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Professional Role --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Professional Role</strong></label>
                                <p>{{ $application->professional_role ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Primary Area of Experience --}}
                        <div class="col-xxl-12 col-md-12">
                            <div>
                                <label class="form-label"><strong>Primary Area of Experience</strong></label>
                                <p>{{ $application->primary_area_of_experience ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Resume/CV --}}
                        <div class="col-xxl-12 col-md-12">
                            <div>
                                <label class="form-label"><strong>Resume / CV</strong></label>
                                <p>
                                    @if($application->resume_cv)
                                        <a href="{{ asset($application->resume_cv) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="ri-file-download-line me-1"></i> View / Download CV
                                        </a>
                                    @else
                                        <span class="text-muted">No resume uploaded.</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- 3. Certification Selection Header --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">3. Certification Selection</h5>
                        </div>

                        {{-- Selected Certification --}}
                        <div class="col-xxl-12 col-md-12">
                            <div>
                                <label class="form-label"><strong>Selected Certification Programme</strong></label>
                                <p>
                                    @if($application->catalogue)
                                        <strong>{{ $application->catalogue->title }}</strong>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- 4. Confirmations Header --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">4. Confirmations</h5>
                        </div>

                        {{-- Accuracy Confirmed --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Accuracy Confirmed?</strong></label>
                                <p>
                                    @if($application->confirm_accuracy)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Policies Agreed --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Policies Agreed?</strong></label>
                                <p>
                                    @if($application->agree_policies)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Status & Date --}}
                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Current Status</strong></label>
                                <p>
                                    @php
                                        $status = trim($application->status);
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

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Submitted On</strong></label>
                                <p>{{ $application->created_at->format('d M Y, H:i A') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Status & Notes Form --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">5. Administrative Action</h5>
                            <form action="{{ route('admin.certification-applications.update-status', $application->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <label for="statusSelect" class="form-label"><strong>Update Status</strong></label>
                                        <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect">
                                            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="accepted" {{ $application->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                            <option value="canceled" {{ $application->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                            <option value="completed" {{ $application->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="adminNotes" class="form-label"><strong>Admin Notes</strong></label>
                                        <textarea class="form-control @error('admin_notes') is-invalid @enderror" id="adminNotes" name="admin_notes" rows="4" placeholder="Enter administrative notes here...">{{ old('admin_notes', $application->admin_notes) }}</textarea>
                                        @error('admin_notes')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-success">Update Status & Notes</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Back Button --}}
                        <div class="col-xxl-12 col-md-12 mt-3 border-top pt-3">
                            <a href="{{ route('admin.certification-applications.index') }}" class="btn btn-secondary">Back to Applications</a>
                            <form action="{{ route('admin.certification-applications.destroy', $application->id) }}" method="POST" style="display:inline-block;">
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
