@extends('backend.app')

@push('styles')
<style>
    /* Premium visual styles */
    .stat-card-gradient-1 {
        background: linear-gradient(135deg, rgba(26, 60, 52, 0.05) 0%, rgba(38, 79, 68, 0.02) 100%);
        border-left: 5px solid #1a3c34 !important;
    }
    .stat-card-gradient-2 {
        background: linear-gradient(135deg, rgba(10, 179, 156, 0.05) 0%, rgba(10, 179, 156, 0.02) 100%);
        border-left: 5px solid #0ab39c !important;
    }
    .stat-card-gradient-3 {
        background: linear-gradient(135deg, rgba(240, 101, 72, 0.05) 0%, rgba(240, 101, 72, 0.02) 100%);
        border-left: 5px solid #f06548 !important;
    }

    .exam-card-hover {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid var(--vz-border-color, #e9ebec);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .exam-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(26, 60, 52, 0.08);
        border-color: rgba(26, 60, 52, 0.2);
    }

    .premium-badge-pub {
        background: rgba(10, 179, 156, 0.1) !important;
        color: #0ab39c !important;
        font-weight: 600;
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 30px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .premium-badge-draft {
        background: rgba(240, 101, 72, 0.1) !important;
        color: #f06548 !important;
        font-weight: 600;
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 30px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-number {
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
        color: #1a3c34;
    }

    .btn-premium-action {
        border-radius: 8px;
        padding: 7px 14px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-premium-action:hover {
        transform: translateY(-1px);
    }

    .btn-premium-delete {
        border-radius: 8px;
        padding: 7px 10px;
        transition: all 0.2s ease;
    }
    .btn-premium-delete:hover {
        background-color: #f06548 !important;
        color: #fff !important;
        transform: translateY(-1px);
    }

    .empty-state-card {
        border: 2px dashed var(--vz-border-color, #e9ebec);
        background: transparent;
        transition: border-color 0.2s ease;
    }
    .empty-state-card:hover {
        border-color: #1a3c34;
    }
</style>
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0 text-dark fw-bold">Exam Questions Module</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Exams</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Stats Row -->
    <div class="row mb-2">
        <div class="col-xl-4 col-md-6">
            <div class="card card-animate stat-card-gradient-1 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase fw-bold text-muted text-truncate mb-2 fs-12">Total Exams</p>
                            <h4 class="stat-number">{{ $exams->count() }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-white text-primary rounded-circle fs-3 shadow-sm border border-light">
                                <i class="ri-book-open-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="card card-animate stat-card-gradient-2 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase fw-bold text-muted text-truncate mb-2 fs-12">Published Exams</p>
                            <h4 class="stat-number text-success">{{ $exams->where('status', 'published')->count() }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-white text-success rounded-circle fs-3 shadow-sm border border-light">
                                <i class="ri-checkbox-circle-fill"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card card-animate stat-card-gradient-3 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase fw-bold text-muted text-truncate mb-2 fs-12">Draft Exams</p>
                            <h4 class="stat-number text-warning">{{ $exams->where('status', 'draft')->count() }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-white text-warning rounded-circle fs-3 shadow-sm border border-light">
                                <i class="ri-draft-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Action Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm border border-light">
                <div>
                    <h5 class="mb-0 text-dark fw-bold">Question Sets Database</h5>
                    <p class="text-muted fs-13 mb-0">Create, manage, and structure your custom exam queries.</p>
                </div>
                <a href="{{ route('admin.exams.create') }}" class="btn btn-success px-4 py-2 fw-semibold">
                    <i class="ri-add-line align-bottom me-1"></i> Add New Exam
                </a>
            </div>
        </div>
    </div>

    @if($exams->isEmpty())
        <!-- Empty State -->
        <div class="row justify-content-center my-5">
            <div class="col-md-6">
                <div class="card empty-state-card text-center p-5 shadow-sm">
                    <div class="avatar-lg mx-auto mb-4">
                        <div class="avatar-title bg-light text-muted display-4 rounded-circle">
                            <i class="ri-folder-open-line"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold">No Exams Setup Yet</h4>
                    <p class="text-muted mb-4">You haven't structured any question exams in the workspace. Get started by creating your first set.</p>
                    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary px-4 py-2">
                        <i class="ri-add-line align-bottom me-1"></i> Create First Exam
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Exams Grid -->
        <div class="row">
            @foreach($exams as $exam)
                @php
                    $totalOptions = $exam->questions->sum(fn($q) => $q->options->count());
                @endphp
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card exam-card-hover h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column justify-content-between p-4">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    @if($exam->isPublished())
                                        <span class="premium-badge-pub">Published</span>
                                    @else
                                        <span class="premium-badge-draft">Draft</span>
                                    @endif
                                    <div class="text-muted fs-11"><i class="ri-time-line"></i> {{ $exam->created_at->diffForHumans() }}</div>
                                </div>
                                <h5 class="text-dark fw-bold fs-17 mb-3 line-clamp">{{ $exam->name }}</h5>
                                
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="bg-light p-2.5 rounded text-center">
                                            <span class="d-block text-muted fs-11 text-uppercase fw-semibold">Questions</span>
                                            <span class="fs-15 fw-bold text-dark">{{ $exam->questions_count }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-light p-2.5 rounded text-center">
                                            <span class="d-block text-muted fs-11 text-uppercase fw-semibold">Total Choices</span>
                                            <span class="fs-15 fw-bold text-dark">{{ $totalOptions }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 pt-3 mt-3 border-top border-light">
                                <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-sm btn-soft-primary btn-premium-action flex-grow-1">
                                    <i class="ri-pencil-fill align-bottom me-1"></i> Edit
                                </a>

                                <form action="{{ route('admin.exams.toggle-status', $exam) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-soft-success btn-premium-action w-100">
                                        <i class="ri-refresh-line align-bottom me-1"></i> 
                                        {{ $exam->isPublished() ? 'Set to Draft' : 'Publish Set' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="flex-grow-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-soft-danger btn-premium-delete delete-button" title="Delete Exam">
                                        <i class="ri-delete-bin-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
