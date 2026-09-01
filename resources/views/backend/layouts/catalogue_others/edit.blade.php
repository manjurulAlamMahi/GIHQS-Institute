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
                        <li class="breadcrumb-item active">Edit Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <form action="{{ route('admin.catalogue-others.update', $catalogue->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Card 1: Selected Catalogue Item --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Selected Catalogue Item</h4>
                        <a href="{{ route('admin.catalogue-others.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Catalogue</label>
                                <input type="text" class="form-control bg-light" value="{{ $catalogue->title }} ({{ $catalogue->service_type }})" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Exams Setup --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Exams Setup</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-exams">
                            <div data-repeater-list="exams">
                                @if ($catalogue->exams->count() > 0)
                                    @foreach ($catalogue->exams as $exam)
                                        <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $exam->id }}">

                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <span class="badge bg-warning text-dark me-1 exam-serial">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                    Select Published Exam <span class="text-danger">*</span>
                                                </label>
                                                <select name="exam_id" class="form-select" required>
                                                    <option value="">-- Select Exam --</option>
                                                    @foreach ($publishedExams as $pExam)
                                                        <option value="{{ $pExam->id }}" {{ $exam->exam_id == $pExam->id ? 'selected' : '' }}>{{ $pExam->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Pass Mark (%)</label>
                                                <input type="number" name="pass_mark" value="{{ $exam->pass_mark }}" class="form-control" min="0" max="100" step="0.01" placeholder="e.g. 80">
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                        <input type="hidden" name="id" value="">

                                        <div class="col-md-6">
                                            <label class="form-label">
                                                <span class="badge bg-warning text-dark me-1 exam-serial">01</span>
                                                Select Published Exam <span class="text-danger">*</span>
                                            </label>
                                            <select name="exam_id" class="form-select" required>
                                                <option value="">-- Select Exam --</option>
                                                @foreach ($publishedExams as $pExam)
                                                    <option value="{{ $pExam->id }}">{{ $pExam->name }}</option>
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
                                @endif
                            </div>
                            <button data-repeater-create type="button" class="btn btn-warning btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Exam
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Resources Setup --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Resources Setup</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-resources">
                            <div data-repeater-list="resources">
                                @if ($catalogue->resources->count() > 0)
                                    @foreach ($catalogue->resources as $res)
                                        <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $res->id }}">

                                            <div class="col-md-5">
                                                <label class="form-label">
                                                    <span class="badge bg-success me-1 resource-serial">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                    Resource Title <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="resource_title" value="{{ $res->resource_title }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Resource File (any format)</label>
                                                <input type="file" name="resource_file" class="form-control">

                                                @if ($res->resource_file)
                                                    <div class="mt-2 d-flex align-items-center gap-3 file-preview-container">
                                                        <a href="{{ asset($res->resource_file) }}" target="_blank" class="btn btn-sm btn-info text-white btn-preview">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> View Current
                                                        </a>
                                                        <div class="form-check remove-file-container">
                                                            <input class="form-check-input remove-file-checkbox" type="checkbox" name="remove_resource_file" id="remove_res_{{ $res->id }}" value="1">
                                                            <label class="form-check-label text-danger fw-semibold" for="remove_res_{{ $res->id }}">
                                                                Remove
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                        <input type="hidden" name="id" value="">

                                        <div class="col-md-5">
                                            <label class="form-label">
                                                <span class="badge bg-success me-1 resource-serial">01</span>
                                                Resource Title <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="resource_title" class="form-control" placeholder="Enter resource title" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Resource File (any format)</label>
                                            <input type="file" name="resource_file" class="form-control">
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                <i class="fa-regular fa-trash-can"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                @endif
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
                                @if ($catalogue->liveLinks->count() > 0)
                                    @foreach ($catalogue->liveLinks as $link)
                                        <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $link->id }}">

                                            <div class="col-md-5">
                                                <label class="form-label">
                                                    <span class="badge bg-info me-1 link-serial">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                    Link Title
                                                </label>
                                                <input type="text" name="link_title" value="{{ $link->link_title }}" class="form-control" placeholder="Enter link title">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Link URL <span class="text-danger">*</span></label>
                                                <input type="url" name="link_url" value="{{ $link->link_url }}" class="form-control" placeholder="Enter complete URL (e.g. https://...)" required>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                        <input type="hidden" name="id" value="">

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
                                @endif
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
                                @if ($catalogue->videos->count() > 0)
                                    @foreach ($catalogue->videos as $vid)
                                        <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $vid->id }}">

                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <span class="badge bg-primary me-1 video-serial">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                    Video Title
                                                </label>
                                                <input type="text" name="video_title" value="{{ $vid->video_title }}" class="form-control" placeholder="Enter video title">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Video File</label>
                                                <input type="file" name="video_file" class="form-control" accept="video/*">

                                                @if ($vid->video_file)
                                                    <div class="mt-2 d-flex align-items-center gap-3 file-preview-container">
                                                        <a href="{{ asset($vid->video_file) }}" target="_blank" class="btn btn-sm btn-info text-white btn-preview">
                                                            <i class="fa-solid fa-video me-1"></i> View Current
                                                        </a>
                                                        <div class="form-check remove-file-container">
                                                            <input class="form-check-input remove-file-checkbox" type="checkbox" name="remove_video_file" id="remove_vid_{{ $vid->id }}" value="1">
                                                            <label class="form-check-label text-danger fw-semibold" for="remove_vid_{{ $vid->id }}">
                                                                Remove
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Thumbnail Image</label>
                                                <input type="file" name="thumbnail" class="form-control" accept="image/*">

                                                @if ($vid->thumbnail)
                                                    <div class="mt-2 d-flex align-items-center gap-3 file-preview-container">
                                                        <a href="{{ asset($vid->thumbnail) }}" target="_blank" class="btn btn-sm btn-info text-white btn-preview">
                                                            <i class="fa-solid fa-image me-1"></i> View Current
                                                        </a>
                                                        <div class="form-check remove-file-container">
                                                            <input class="form-check-input remove-file-checkbox" type="checkbox" name="remove_thumbnail" id="remove_thumb_{{ $vid->id }}" value="1">
                                                            <label class="form-check-label text-danger fw-semibold" for="remove_thumb_{{ $vid->id }}">
                                                                Remove
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                        <input type="hidden" name="id" value="">

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
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Thumbnail Image</label>
                                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                <i class="fa-regular fa-trash-can"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                @endif
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
                                @if ($catalogue->videoLinks->count() > 0)
                                    @foreach ($catalogue->videoLinks as $vLink)
                                        <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $vLink->id }}">

                                            <div class="col-md-5">
                                                <label class="form-label">
                                                    <span class="badge bg-secondary me-1 video-link-serial">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                    Video Link Title
                                                </label>
                                                <input type="text" name="video_link_title" value="{{ $vLink->video_link_title }}" class="form-control" placeholder="Enter link title">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Video Link URL <span class="text-danger">*</span></label>
                                                <input type="url" name="video_link_url" value="{{ $vLink->video_link_url }}" class="form-control" placeholder="Enter complete video link (e.g. https://...)" required>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div data-repeater-item class="row mb-3 align-items-end p-3 border rounded bg-light">
                                        <input type="hidden" name="id" value="">

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
                                @endif
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
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Details
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
                    $(this).find('input[type="hidden"]').val('');
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
                initEmpty: {{ $catalogue->exams->count() > 0 ? 'false' : 'true' }}
            });

            $('.repeater-resources').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="file"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                    $(this).find('.file-preview-container').remove();
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
                initEmpty: {{ $catalogue->resources->count() > 0 ? 'false' : 'true' }}
            });

            $('.repeater-live-links').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="url"]').val('');
                    $(this).find('input[type="hidden"]').val('');
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
                initEmpty: {{ $catalogue->liveLinks->count() > 0 ? 'false' : 'true' }}
            });

            $('.repeater-videos').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="file"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                    $(this).find('.file-preview-container').remove();
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
                initEmpty: {{ $catalogue->videos->count() > 0 ? 'false' : 'true' }}
            });

            $('.repeater-video-links').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="url"]').val('');
                    $(this).find('input[type="hidden"]').val('');
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
                initEmpty: {{ $catalogue->videoLinks->count() > 0 ? 'false' : 'true' }}
            });
        });
    </script>
@endpush
