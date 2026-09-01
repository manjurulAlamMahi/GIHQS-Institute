@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Catalogue Certifications</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.catalogue-certifications.index') }}">Certifications</a></li>
                        <li class="breadcrumb-item active">Add Certification Exams</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('admin.catalogue-certifications.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Card 1: Catalogue Selection --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Select Catalogue Item</h4>
                        <a href="{{ route('admin.catalogue-certifications.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label for="catalogue_id" class="form-label">Catalogue <span class="text-danger">*</span></label>
                                <select name="catalogue_id" id="catalogue_id" class="form-select @error('catalogue_id') is-invalid @enderror">
                                    <option value="">-- Select Catalogue --</option>
                                    @foreach ($catalogues as $cat)
                                        <option value="{{ $cat->id }}" {{ old('catalogue_id', $selectedCatalogueId) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('catalogue_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Exams Setup Repeater --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Exams Setup</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-exams">
                            <div data-repeater-list="exams">
                                <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <span class="badge bg-warning text-dark me-1 exam-serial">01</span>
                                            Exam Title <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="exam_title" class="form-control" placeholder="Enter exam title" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Exam Link</label>
                                        <input type="text" name="exam_link" class="form-control" placeholder="Enter exam URL (e.g., https://...)">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Pass Mark (%)</label>
                                        <input type="number" name="pass_mark" class="form-control" min="0" max="100" step="0.01" placeholder="e.g. 80">
                                    </div>
                                    {{-- 
                                    <div class="col-md-2">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_premium" value="1" id="exam_is_premium">
                                            <label class="form-check-label fw-semibold" for="exam_is_premium">Premium</label>
                                        </div>
                                    </div>
                                    --}}
                                    <div class="col-md-2 text-end">
                                        <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button data-repeater-create type="button" class="btn btn-warning btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Exam
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="resources_enabled" value="1">
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Resources Setup</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-resources">
                            <div data-repeater-list="resources">
                                <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                    <div class="col-md-5">
                                        <label class="form-label">
                                            <span class="badge bg-success me-1 resource-serial">01</span>
                                            Resource Title <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="resource_title" class="form-control" placeholder="Enter resource title" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Resource File (any format) <span class="text-danger">*</span></label>
                                        <input type="file" name="resource_file" class="form-control" required>
                                        <small class="text-muted">Allows pdf, doc, zip, ppt, txt, etc.</small>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Resource
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Certifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function reindexResources() {
            $('.repeater-resources [data-repeater-item]').each(function(i) {
                $(this).find('.resource-serial').text(String(i + 1).padStart(2, '0'));
            });
        }

        function reindexExams() {
            $('.repeater-exams [data-repeater-item]').each(function(i) {
                $(this).find('.exam-serial').text(String(i + 1).padStart(2, '0'));
            });
        }

        $(document).ready(function() {
            reindexResources();
            reindexExams();

            $('.repeater-resources').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="file"]').val('');
                    $(this).find('input[type="checkbox"]').prop('checked', false);
                    reindexResources();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this resource row?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexResources();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: true
            });

            $('.repeater-exams').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="checkbox"]').prop('checked', false);
                    reindexExams();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this exam row?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexExams();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: true
            });
        });
    </script>
@endpush
