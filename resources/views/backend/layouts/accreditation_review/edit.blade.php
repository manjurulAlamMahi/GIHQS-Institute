@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Content Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Accreditation Review</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('admin.accreditation-review.update', $accreditationReview->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Card 1: Main Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Accreditation Review Panel Header</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Title 1 --}}
                            <div class="col-md-4">
                                <label for="title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="title1" id="title1"
                                    class="form-control @error('title1') is-invalid @enderror"
                                    value="{{ old('title1', $accreditationReview->title1) }}"
                                    placeholder="Enter Title 1">
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title 2 --}}
                            <div class="col-md-4">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2"
                                    class="form-control @error('title2') is-invalid @enderror"
                                    value="{{ old('title2', $accreditationReview->title2) }}"
                                    placeholder="Enter Title 2">
                                @error('title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-md-4">
                                <label for="tagline" class="form-label">Tagline</label>
                                <input type="text" name="tagline" id="tagline"
                                    class="form-control @error('tagline') is-invalid @enderror"
                                    value="{{ old('tagline', $accreditationReview->tagline) }}"
                                    placeholder="Enter Tagline">
                                @error('tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Short Description --}}
                            <div class="col-md-12">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea name="short_description" id="short_description"
                                    class="form-control @error('short_description') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Enter Short Description">{{ old('short_description', $accreditationReview->short_description) }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Purpose Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Purpose / Role of the Panel</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Purpose Tagline --}}
                            <div class="col-md-6">
                                <label for="purpose_tagline" class="form-label">Purpose Tagline</label>
                                <input type="text" name="purpose_tagline" id="purpose_tagline"
                                    class="form-control @error('purpose_tagline') is-invalid @enderror"
                                    value="{{ old('purpose_tagline', $accreditationReview->purpose_tagline) }}"
                                    placeholder="Enter Purpose Tagline">
                                @error('purpose_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Purpose Title --}}
                            <div class="col-md-6">
                                <label for="purpose_title" class="form-label">Purpose Title</label>
                                <input type="text" name="purpose_title" id="purpose_title"
                                    class="form-control @error('purpose_title') is-invalid @enderror"
                                    value="{{ old('purpose_title', $accreditationReview->purpose_title) }}"
                                    placeholder="Enter Purpose Title">
                                @error('purpose_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Purpose Short Description --}}
                            <div class="col-md-12">
                                <label for="purpose_short_description" class="form-label">Purpose Short Description</label>
                                <textarea name="purpose_short_description" id="purpose_short_description"
                                    class="form-control @error('purpose_short_description') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Enter Purpose Short Description">{{ old('purpose_short_description', $accreditationReview->purpose_short_description) }}</textarea>
                                @error('purpose_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Evaluation Responsibilities Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Evaluation Responsibilities</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-3">
                            {{-- Review Title --}}
                            <div class="col-md-12">
                                <label for="review_title" class="form-label">Review Title (e.g. Evaluation Responsibilities)</label>
                                <input type="text" name="review_title" id="review_title"
                                    class="form-control @error('review_title') is-invalid @enderror"
                                    value="{{ old('review_title', $accreditationReview->review_title) }}"
                                    placeholder="Enter Review Title">
                                @error('review_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3">Evaluation Responsibility Features</h5>

                            <div class="repeater-features">
                                <div data-repeater-list="features">
                                    @if ($accreditationReview->features->count() > 0)
                                        @foreach ($accreditationReview->features as $feature)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $feature->id }}">

                                                <div class="col">
                                                    <label class="form-label">
                                                        <span class="badge bg-secondary me-1 serial-badge">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                        Feature Description <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea name="description" class="form-control" rows="2"
                                                        placeholder="Enter responsibility feature description">{{ $feature->description }}</textarea>
                                                </div>

                                                <div class="col-auto mt-4">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                        <i class="fa-regular fa-trash-can"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        {{-- Empty template item when no features exist --}}
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">

                                            <div class="col">
                                                <label class="form-label">
                                                    <span class="badge bg-secondary me-1 serial-badge">01</span>
                                                    Feature Description <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="description" class="form-control" rows="2"
                                                    placeholder="Enter responsibility feature description"></textarea>
                                            </div>

                                            <div class="col-auto mt-4">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="fa-solid fa-plus"></i> Add Feature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Panel Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Panel Formation In Progress</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Panel Title --}}
                            <div class="col-md-12">
                                <label for="panel_title" class="form-label">Panel Title</label>
                                <input type="text" name="panel_title" id="panel_title"
                                    class="form-control @error('panel_title') is-invalid @enderror"
                                    value="{{ old('panel_title', $accreditationReview->panel_title) }}"
                                    placeholder="Enter Panel Title">
                                @error('panel_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Panel Short Description --}}
                            <div class="col-md-12">
                                <label for="panel_short_description" class="form-label">Panel Short Description</label>
                                <textarea name="panel_short_description" id="panel_short_description"
                                    class="form-control @error('panel_short_description') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Enter Panel Short Description">{{ old('panel_short_description', $accreditationReview->panel_short_description) }}</textarea>
                                @error('panel_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Appointment Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Appointment Terms</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Appointment Title --}}
                            <div class="col-md-12">
                                <label for="appointment_title" class="form-label">Appointment Title</label>
                                <input type="text" name="appointment_title" id="appointment_title"
                                    class="form-control @error('appointment_title') is-invalid @enderror"
                                    value="{{ old('appointment_title', $accreditationReview->appointment_title) }}"
                                    placeholder="Enter Appointment Title">
                                @error('appointment_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Appointment Short Description --}}
                            <div class="col-md-12">
                                <label for="appointment_short_description" class="form-label">Appointment Short Description</label>
                                <textarea name="appointment_short_description" id="appointment_short_description"
                                    class="form-control @error('appointment_short_description') is-invalid @enderror"
                                    rows="5"
                                    placeholder="Enter Appointment Short Description">{{ old('appointment_short_description', $accreditationReview->appointment_short_description) }}</textarea>
                                @error('appointment_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 6: Conflict Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Conflict of Interest</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Conflict Title --}}
                            <div class="col-md-12">
                                <label for="conflict_title" class="form-label">Conflict Title</label>
                                <input type="text" name="conflict_title" id="conflict_title"
                                    class="form-control @error('conflict_title') is-invalid @enderror"
                                    value="{{ old('conflict_title', $accreditationReview->conflict_title) }}"
                                    placeholder="Enter Conflict Title">
                                @error('conflict_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Conflict Short Description --}}
                            <div class="col-md-12">
                                <label for="conflict_short_description" class="form-label">Conflict Short Description</label>
                                <textarea name="conflict_short_description" id="conflict_short_description"
                                    class="form-control @error('conflict_short_description') is-invalid @enderror"
                                    rows="5"
                                    placeholder="Enter Conflict Short Description">{{ old('conflict_short_description', $accreditationReview->conflict_short_description) }}</textarea>
                                @error('conflict_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 7: Expression Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Expressions of Interest (CKEditor)</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Expression Title --}}
                            <div class="col-md-12">
                                <label for="expression_title" class="form-label">Expression Title</label>
                                <input type="text" name="expression_title" id="expression_title"
                                    class="form-control @error('expression_title') is-invalid @enderror"
                                    value="{{ old('expression_title', $accreditationReview->expression_title) }}"
                                    placeholder="Enter Expression Title">
                                @error('expression_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Expression Description (CKEditor) --}}
                            <div class="col-md-12">
                                <label for="expression_description" class="form-label">Expression Description</label>
                                <textarea name="expression_description" id="expression_description"
                                    class="form-control ckeditor @error('expression_description') is-invalid @enderror"
                                    rows="6"
                                    placeholder="Enter Expression Description">{{ old('expression_description', $accreditationReview->expression_description) }}</textarea>
                                @error('expression_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 8: Page Content Injection --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <i class="ri-file-code-line me-2 fs-18 text-secondary"></i>
                        <h4 class="card-title mb-0 flex-grow-1">Page Content Injection</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            {{-- Content File --}}
                            <div class="col-md-6">
                                <label for="content_file" class="form-label">Content File (.html)</label>
                                <input type="file" name="content_file" id="content_file" class="form-control @error('content_file') is-invalid @enderror" accept=".html,.txt">
                                <small class="text-muted">Only .html or .txt files are accepted.</small>
                                @error('content_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @if ($accreditationReview->content_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($accreditationReview->content_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-file-code me-1"></i> View Current File ({{ basename($accreditationReview->content_file) }})
                                        </a>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_content_file" id="remove_content_file" value="1">
                                            <label class="form-check-label text-danger fw-semibold" for="remove_content_file">
                                                Remove Current File
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Injected Status --}}
                            <div class="col-md-6">
                                <label for="injected_status" class="form-label">Injected Status</label>
                                <select class="form-select" name="injected_status" id="injected_status">
                                    <option value="0" {{ old('injected_status', $accreditationReview->injected_status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    <option value="1" {{ old('injected_status', $accreditationReview->injected_status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Accreditation Review
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function reindexFeatures() {
            $('.repeater-features [data-repeater-item]').each(function(i) {
                $(this).find('.serial-badge').text(String(i + 1).padStart(2, '0'));
            });
        }

        $(document).ready(function() {
            reindexFeatures();

            $('.repeater-features').repeater({
                show: function() {
                    $(this).slideDown();
                    // Clear textarea and hidden ID in the cloned item
                    $(this).find('textarea').val('');
                    $(this).find('input[type="hidden"]').val('');
                    reindexFeatures();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this feature?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexFeatures();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });
        });
    </script>
@endpush
