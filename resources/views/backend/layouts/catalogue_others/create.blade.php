@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Catalogue Others Setup</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.catalogue-others.index') }}">Others</a></li>
                        <li class="breadcrumb-item active">Add Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('admin.catalogue-others.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Card 1: Catalogue Selection --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Select Catalogue Item</h4>
                        <a href="{{ route('admin.catalogue-others.index') }}" class="btn btn-sm btn-secondary">
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
                                            {{ $cat->title }} ({{ $cat->service_type }})
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
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <span class="badge bg-warning text-dark me-1 exam-serial">01</span>
                                            Select Published Exam <span class="text-danger">*</span>
                                        </label>
                                        <select name="exam_id" class="form-select" required>
                                            <option value="">-- Select Exam --</option>
                                            @foreach ($publishedExams as $exam)
                                                <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Pass Mark (%)</label>
                                        <input type="number" name="pass_mark" class="form-control" min="0" max="100" step="0.01" placeholder="e.g. 80">
                                    </div>
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

                {{-- Card 3: Resources Repeater --}}
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

                {{-- Card 4: Live Links Setup --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Live Links Setup</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-live-links">
                            <div data-repeater-list="live_links">
                                <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                    <div class="col-md-5">
                                        <label class="form-label">
                                            <span class="badge bg-info me-1 link-serial">01</span>
                                            Link Title
                                        </label>
                                        <input type="text" name="link_title" class="form-control" placeholder="Enter link title">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Link URL <span class="text-danger">*</span></label>
                                        <input type="url" name="link_url" class="form-control" placeholder="Enter complete URL (e.g. https://...)" required>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button data-repeater-create type="button" class="btn btn-info btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Link
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Videos (Files) Setup --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Video Files Setup</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-videos">
                            <div data-repeater-list="videos">
                                <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <span class="badge bg-primary me-1 video-serial">01</span>
                                            Video Title
                                        </label>
                                        <input type="text" name="video_title" class="form-control" placeholder="Enter video title">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Video File <span class="text-danger">*</span></label>
                                        <input type="file" name="video_file" class="form-control" accept="video/*" required>
                                        <small class="text-muted">Allows mp4, mov, avi, etc.</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Thumbnail Image</label>
                                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                        <small class="text-muted">Allows jpg, png, etc.</small>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button data-repeater-create type="button" class="btn btn-primary btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Video File
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card 6: Video Links Setup --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Video Links Setup</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-video-links">
                            <div data-repeater-list="video_links">
                                <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                    <div class="col-md-5">
                                        <label class="form-label">
                                            <span class="badge bg-secondary me-1 video-link-serial">01</span>
                                            Video Link Title
                                        </label>
                                        <input type="text" name="video_link_title" class="form-control" placeholder="Enter link title">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Video Link URL <span class="text-danger">*</span></label>
                                        <input type="url" name="video_link_url" class="form-control" placeholder="Enter complete video link (e.g. https://...)" required>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button data-repeater-create type="button" class="btn btn-secondary btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Video Link
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Details
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function reindexExams() {
            $('.repeater-exams [data-repeater-item]').each(function(i) {
                $(this).find('.exam-serial').text(String(i + 1).padStart(2, '0'));
            });
        }

        function reindexResources() {
            $('.repeater-resources [data-repeater-item]').each(function(i) {
                $(this).find('.resource-serial').text(String(i + 1).padStart(2, '0'));
            });
        }

        function reindexLiveLinks() {
            $('.repeater-live-links [data-repeater-item]').each(function(i) {
                $(this).find('.link-serial').text(String(i + 1).padStart(2, '0'));
            });
        }

        function reindexVideos() {
            $('.repeater-videos [data-repeater-item]').each(function(i) {
                $(this).find('.video-serial').text(String(i + 1).padStart(2, '0'));
            });
        }

        function reindexVideoLinks() {
            $('.repeater-video-links [data-repeater-item]').each(function(i) {
                $(this).find('.video-link-serial').text(String(i + 1).padStart(2, '0'));
            });
        }

        $(document).ready(function() {
            reindexExams();
            reindexResources();
            reindexLiveLinks();
            reindexVideos();
            reindexVideoLinks();

            $('.repeater-exams').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('select').val('');
                    $(this).find('input[type="number"]').val('');
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

            $('.repeater-resources').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="file"]').val('');
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

            $('.repeater-live-links').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="url"]').val('');
                    reindexLiveLinks();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this link row?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexLiveLinks();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: true
            });

            $('.repeater-videos').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="file"]').val('');
                    reindexVideos();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this video row?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexVideos();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: true
            });

            $('.repeater-video-links').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="url"]').val('');
                    reindexVideoLinks();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this video link row?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexVideoLinks();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: true
            });
        });
    </script>
@endpush
