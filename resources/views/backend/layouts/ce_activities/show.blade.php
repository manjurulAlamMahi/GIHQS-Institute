@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">CE Activities</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.ce-activities.index') }}">CE Activities</a></li>
                        <li class="breadcrumb-item active">View Details</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">CE Activity Details</h4>
                </div>

                <div class="card-body">
                    <div class="row gy-4">

                        {{-- User Information --}}
                        <div class="col-xxl-12 col-md-12">
                            <h5 class="text-primary border-bottom pb-2">1. Submitted By</h5>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>User Name</strong></label>
                                <p>{{ $activity->user ? $activity->user->full_name : 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>User Email</strong></label>
                                <p>
                                    @if($activity->user)
                                        <a href="mailto:{{ $activity->user->email }}">{{ $activity->user->email }}</a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- CE Activity Details --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">2. Activity Details</h5>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Related Certification</strong></label>
                                <p><strong>{{ $activity->certification ? $activity->certification->title : 'N/A' }}</strong></p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Related Domain</strong></label>
                                <p>{{ $activity->domain ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Activity Type</strong></label>
                                <p>{{ $activity->activity_type ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Activity Title</strong></label>
                                <p>{{ $activity->activity_title ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Provider / Organization</strong></label>
                                <p>{{ $activity->provider ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Completion Date</strong></label>
                                <p>{{ $activity->completion_date ? $activity->completion_date->format('d M Y') : 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>CE Credits Earned</strong></label>
                                <p>{{ $activity->credits_earned ?? '0.00' }}</p>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-6">
                            <div>
                                <label class="form-label"><strong>Current Status</strong></label>
                                <p>
                                    @php
                                        $status = trim($activity->status);
                                        if (empty($status)) $status = 'pending';

                                        $badgeClass = match (strtolower($status)) {
                                            'pending' => 'bg-danger',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-dark',
                                            default => 'bg-secondary',
                                        };

                                        $displayStatus = strtolower($status) == 'pending' ? 'Pending Review' : ucfirst($status);
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $displayStatus }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="col-xxl-12 col-md-12">
                            <div>
                                <label class="form-label"><strong>Description / Notes</strong></label>
                                <p>{{ $activity->description ?? 'No description provided.' }}</p>
                            </div>
                        </div>

                        {{-- Supporting Evidence --}}
                        <div class="col-xxl-12 col-md-12">
                            <div>
                                <label class="form-label"><strong>Supporting Evidence</strong></label>
                                <p>
                                    @if($activity->evidence_file)
                                        <a href="{{ asset($activity->evidence_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="ri-file-download-line me-1"></i> View / Download Evidence
                                        </a>
                                    @else
                                        <span class="text-muted">No evidence uploaded.</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Administrative Action Form --}}
                        <div class="col-xxl-12 col-md-12 mt-4">
                            <h5 class="text-primary border-bottom pb-2">3. Administrative Action</h5>
                            <form action="{{ route('admin.ce-activities.update-status', $activity->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <label for="statusSelect" class="form-label"><strong>Update Status</strong></label>
                                        <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect">
                                            <option value="pending" {{ $activity->status == 'pending' ? 'selected' : '' }}>Pending Review</option>
                                            <option value="approved" {{ $activity->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ $activity->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="adminNotes" class="form-label"><strong>Admin Review Notes</strong></label>
                                        <textarea class="form-control @error('admin_notes') is-invalid @enderror" id="adminNotes" name="admin_notes" rows="4" placeholder="Enter review feedback here...">{{ old('admin_notes', $activity->admin_notes) }}</textarea>
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

                        {{-- Back and Delete Buttons --}}
                        <div class="col-xxl-12 col-md-12 mt-3 border-top pt-3">
                            <a href="{{ route('admin.ce-activities.index') }}" class="btn btn-secondary">Back to List</a>
                            <form action="{{ route('admin.ce-activities.destroy', $activity->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger delete-button">Delete CE Activity</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
