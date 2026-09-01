@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Manage Exam Overrides</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.exam-overrides.index') }}">Exam Overrides</a></li>
                        <li class="breadcrumb-item active">Manage Override</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Manage Override for: {{ $application->name }}</h4>
                    <span class="badge bg-primary" style="font-size: 14px; padding: 6px 12px;">Ref: {{ $application->reference_number }}</span>
                </div>

                <div class="card-body">
                    <div class="row gy-4">
                        {{-- Context Info --}}
                        <div class="col-xxl-12 col-md-12">
                            <h5 class="text-primary border-bottom pb-2">Applicant & Certification Info</h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Applicant Name:</strong> {{ $application->name }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Email:</strong> <a href="mailto:{{ $application->email }}">{{ $application->email }}</a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Certification Program:</strong> {{ $application->catalogue ? $application->catalogue->title : 'N/A' }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Application Status:</strong> <span class="badge bg-success">{{ ucfirst($application->status) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Exam Overrides Form --}}
                        @if($application->user_id && $application->catalogue && $application->catalogue->exams->count() > 0)
                            <div class="col-xxl-12 col-md-12 mt-4 border-top pt-3">
                                <h5 class="text-primary border-bottom pb-2">Certification Exam Attempts & Override Settings</h5>
                                
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('admin.user-exam-overrides.storeOrUpdate') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $application->user_id }}">
                                    <input type="hidden" name="application_id" value="{{ $application->id }}">
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Exam Title</th>
                                                    <th>Current Attempts</th>
                                                    <th>Default Cooldown Lock Info</th>
                                                    <th>Override Max Attempts</th>
                                                    <th>Override Lock Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($application->catalogue->exams as $exam)
                                                    @php
                                                        $attempts = \App\Models\UserExamResult::where('user_id', $application->user_id)
                                                            ->where('catalogue_exam_id', $exam->id)
                                                            ->count();
                                                        
                                                        $override = \App\Models\UserExamOverride::where('user_id', $application->user_id)
                                                            ->where('catalogue_exam_id', $exam->id)
                                                            ->first();
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $exam->exam_title }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info" style="font-size: 12px;">{{ $attempts }} attempts</span>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $lastResult = \App\Models\UserExamResult::where('user_id', $application->user_id)
                                                                    ->where('catalogue_exam_id', $exam->id)
                                                                    ->latest('id')
                                                                    ->first();
                                                                
                                                                $defaultEligibleDate = null;
                                                                if ($lastResult && $lastResult->status === 'failed') {
                                                                    $defaultEligibleDate = \Carbon\Carbon::parse($lastResult->created_at)->addMonths(3)->toDateString();
                                                                }
                                                            @endphp
                                                            @if($defaultEligibleDate)
                                                                <span class="text-warning fw-semibold">Default lockout until: <code>{{ $defaultEligibleDate }}</code></span>
                                                            @else
                                                                <span class="text-muted">No lockout active</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="number" name="overrides[{{ $exam->id }}][max_attempts]" 
                                                                   class="form-control form-control-sm" style="width: 100px;" 
                                                                   value="{{ $override ? $override->max_attempts : '' }}" 
                                                                   placeholder="Default: 1" min="1">
                                                        </td>
                                                        <td>
                                                            <input type="date" name="overrides[{{ $exam->id }}][retake_eligible_date]" 
                                                                   class="form-control form-control-sm mb-1" 
                                                                   value="{{ $override ? $override->retake_eligible_date : '' }}">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="overrides[{{ $exam->id }}][unlock_immediately]" value="1" id="unlock_{{ $exam->id }}" {{ ($override && $override->ignore_cooldown) ? 'checked' : '' }}>
                                                                <label class="form-check-label text-success fw-semibold" for="unlock_{{ $exam->id }}" style="font-size: 11px;">
                                                                    Unlock immediately (Ignore Lockout)
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button type="submit" name="submit_exam_id" value="{{ $exam->id }}" class="btn btn-sm btn-success">
                                                                <i class="fa-solid fa-floppy-disk me-1"></i> Save
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="col-xxl-12 col-md-12 mt-4 text-center">
                                <p class="text-muted">No exams associated with this certification.</p>
                            </div>
                        @endif

                        {{-- Back Button --}}
                        <div class="col-xxl-12 col-md-12 mt-3 border-top pt-3">
                            <a href="{{ route('admin.exam-overrides.index') }}" class="btn btn-secondary">Back to Overrides</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
