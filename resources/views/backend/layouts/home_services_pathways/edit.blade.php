@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Home Page Module</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home Page</a></li>
                        <li class="breadcrumb-item active">Services & Pathways</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('admin.home-services-pathways.update', $homeGihq->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Card 3: Services & Pathways Repeater Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Services & Pathways Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-services">
                            <div data-repeater-list="services_pathways">
                                @if ($homeGihq->servicesPathways->count() > 0)
                                    @foreach ($homeGihq->servicesPathways as $item)
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $item->id }}">

                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Serial</label>
                                                <input type="text" name="serial" class="form-control" value="{{ $item->serial }}" placeholder="e.g. 01">
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Target Audience</label>
                                                <input type="text" name="target_audience" class="form-control" value="{{ $item->target_audience }}" placeholder="Target Audience">
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ $item->title }}" placeholder="Title" required>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Description">{{ $item->description }}</textarea>
                                            </div>

                                            <div class="col-md-10 mb-2">
                                                <label class="form-label">Link Text</label>
                                                <input type="text" name="link_text" class="form-control" value="{{ $item->link_text }}" placeholder="Link Text">
                                            </div>

                                            <div class="col-md-2 mt-4 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                        <input type="hidden" name="id" value="">

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Serial</label>
                                            <input type="text" name="serial" class="form-control" placeholder="e.g. 01">
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Target Audience</label>
                                            <input type="text" name="target_audience" class="form-control" placeholder="Target Audience">
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="Title" required>
                                        </div>

                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="2" placeholder="Description"></textarea>
                                        </div>

                                        <div class="col-md-10 mb-2">
                                            <label class="form-label">Link Text</label>
                                            <input type="text" name="link_text" class="form-control" placeholder="Link Text">
                                        </div>

                                        <div class="col-md-2 mt-4 text-end">
                                            <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                <i class="fa-regular fa-trash-can"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Service/Pathway
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card 1: Home GIHQ Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Home GIHQ Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Title 1 --}}
                            <div class="col-md-6">
                                <label for="title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="title1" id="title1" class="form-control @error('title1') is-invalid @enderror" value="{{ old('title1', $homeGihq->title1) }}"
                                    placeholder="Enter Title 1">
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title 2 --}}
                            <div class="col-md-6">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2" class="form-control @error('title2') is-invalid @enderror" value="{{ old('title2', $homeGihq->title2) }}"
                                    placeholder="Enter Title 2">
                                @error('title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-md-12">
                                <label for="tagline" class="form-label">Tagline</label>
                                <input type="text" name="tagline" id="tagline" class="form-control @error('tagline') is-invalid @enderror" value="{{ old('tagline', $homeGihq->tagline) }}"
                                    placeholder="Enter Tagline">
                                @error('tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter Description">{{ old('description', $homeGihq->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Buttons --}}
                            <div class="col-md-3">
                                <label for="certificate_btn_text" class="form-label">Certificate Button Text</label>
                                <input type="text" name="certificate_btn_text" id="certificate_btn_text" class="form-control @error('certificate_btn_text') is-invalid @enderror"
                                    value="{{ old('certificate_btn_text', $homeGihq->certificate_btn_text) }}" placeholder="Certificate Button Text">
                                @error('certificate_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="learning_btn_text" class="form-label">Learning Button Text</label>
                                <input type="text" name="learning_btn_text" id="learning_btn_text" class="form-control @error('learning_btn_text') is-invalid @enderror"
                                    value="{{ old('learning_btn_text', $homeGihq->learning_btn_text) }}" placeholder="Learning Button Text">
                                @error('learning_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="advisory_btn_text" class="form-label">Advisory Button Text</label>
                                <input type="text" name="advisory_btn_text" id="advisory_btn_text" class="form-control @error('advisory_btn_text') is-invalid @enderror"
                                    value="{{ old('advisory_btn_text', $homeGihq->advisory_btn_text) }}" placeholder="Advisory Button Text">
                                @error('advisory_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="member_btn_text" class="form-label">Member Button Text</label>
                                <input type="text" name="member_btn_text" id="member_btn_text" class="form-control @error('member_btn_text') is-invalid @enderror"
                                    value="{{ old('member_btn_text', $homeGihq->member_btn_text) }}" placeholder="Member Button Text">
                                @error('member_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Professional Ecosystem Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Professional Ecosystem Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Ecosystem Title --}}
                            <div class="col-md-12">
                                <label for="professional_ecosystem_title" class="form-label">Professional Ecosystem Title</label>
                                <input type="text" name="professional_ecosystem_title" id="professional_ecosystem_title"
                                    class="form-control @error('professional_ecosystem_title') is-invalid @enderror"
                                    value="{{ old('professional_ecosystem_title', $homeGihq->professional_ecosystem_title) }}" placeholder="Enter Professional Ecosystem Title">
                                @error('professional_ecosystem_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Learning Fields --}}
                            <div class="col-md-6">
                                <label for="learning_tagline" class="form-label">Learning Tagline</label>
                                <input type="text" name="learning_tagline" id="learning_tagline" class="form-control @error('learning_tagline') is-invalid @enderror"
                                    value="{{ old('learning_tagline', $homeGihq->learning_tagline) }}" placeholder="Learning Tagline">
                            </div>
                            <div class="col-md-6">
                                <label for="learning_title" class="form-label">Learning Title</label>
                                <input type="text" name="learning_title" id="learning_title" class="form-control @error('learning_title') is-invalid @enderror"
                                    value="{{ old('learning_title', $homeGihq->learning_title) }}" placeholder="Learning Title">
                            </div>
                            <div class="col-md-12">
                                <label for="learning_details" class="form-label">Learning Details</label>
                                <textarea name="learning_details" id="learning_details" class="form-control" rows="2" placeholder="Learning Details">{{ old('learning_details', $homeGihq->learning_details) }}</textarea>
                            </div>

                            {{-- Certificate Fields --}}
                            <div class="col-md-6">
                                <label for="certificate_tagline" class="form-label">Certificate Tagline</label>
                                <input type="text" name="certificate_tagline" id="certificate_tagline" class="form-control @error('certificate_tagline') is-invalid @enderror"
                                    value="{{ old('certificate_tagline', $homeGihq->certificate_tagline) }}" placeholder="Certificate Tagline">
                            </div>
                            <div class="col-md-6">
                                <label for="certificate_title" class="form-label">Certificate Title</label>
                                <input type="text" name="certificate_title" id="certificate_title" class="form-control @error('certificate_title') is-invalid @enderror"
                                    value="{{ old('certificate_title', $homeGihq->certificate_title) }}" placeholder="Certificate Title">
                            </div>
                            <div class="col-md-12">
                                <label for="certificate_details" class="form-label">Certificate Details</label>
                                <textarea name="certificate_details" id="certificate_details" class="form-control" rows="2" placeholder="Certificate Details">{{ old('certificate_details', $homeGihq->certificate_details) }}</textarea>
                            </div>

                            {{-- Lead Fields --}}
                            <div class="col-md-6">
                                <label for="lead_tagline" class="form-label">Lead Tagline</label>
                                <input type="text" name="lead_tagline" id="lead_tagline" class="form-control @error('lead_tagline') is-invalid @enderror"
                                    value="{{ old('lead_tagline', $homeGihq->lead_tagline) }}" placeholder="Lead Tagline">
                            </div>
                            <div class="col-md-6">
                                <label for="lead_title" class="form-label">Lead Title</label>
                                <input type="text" name="lead_title" id="lead_title" class="form-control @error('lead_title') is-invalid @enderror"
                                    value="{{ old('lead_title', $homeGihq->lead_title) }}" placeholder="Lead Title">
                            </div>
                            <div class="col-md-12">
                                <label for="lead_details" class="form-label">Lead Details</label>
                                <textarea name="lead_details" id="lead_details" class="form-control" rows="2" placeholder="Lead Details">{{ old('lead_details', $homeGihq->lead_details) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: The GIHQS Professional Pathways Repeater Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">The GIHQS Professional Pathways Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-professional">
                            <div data-repeater-list="professional_pathways">
                                @if ($homeGihq->professionalPathways->count() > 0)
                                    @foreach ($homeGihq->professionalPathways as $item)
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $item->id }}">

                                            <div class="col-md-2 mb-2">
                                                <label class="form-label">Serial</label>
                                                <input type="text" name="serial" class="form-control" value="{{ $item->serial }}" placeholder="e.g. 01">
                                            </div>

                                            <div class="col-md-10 mb-2">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ $item->title }}" placeholder="Title" required>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Description">{{ $item->description }}</textarea>
                                            </div>

                                            <div class="col-md-10 mb-2">
                                                <label class="form-label">Link Text</label>
                                                <input type="text" name="link_text" class="form-control" value="{{ $item->link_text }}" placeholder="Link Text (nullable)">
                                            </div>

                                            <div class="col-md-2 mt-4 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                        <input type="hidden" name="id" value="">

                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Serial</label>
                                            <input type="text" name="serial" class="form-control" placeholder="e.g. 01">
                                        </div>

                                        <div class="col-md-10 mb-2">
                                            <label class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="Title" required>
                                        </div>

                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="2" placeholder="Description"></textarea>
                                        </div>

                                        <div class="col-md-10 mb-2">
                                            <label class="form-label">Link Text</label>
                                            <input type="text" name="link_text" class="form-control" placeholder="Link Text (nullable)">
                                        </div>

                                        <div class="col-md-2 mt-4 text-end">
                                            <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                <i class="fa-regular fa-trash-can"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Professional Pathway
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card 4.5: Choose Your Next Step Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Choose Your Next Step Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Title 1 --}}
                            <div class="col-md-6">
                                <label for="next_step_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="next_step[title1]" id="next_step_title1" class="form-control @error('next_step.title1') is-invalid @enderror"
                                    value="{{ old('next_step.title1', $homeGihq->nextStep->title1 ?? '') }}" placeholder="Enter Title 1" required>
                                @error('next_step.title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title 2 --}}
                            <div class="col-md-6">
                                <label for="next_step_title2" class="form-label">Title 2</label>
                                <input type="text" name="next_step[title2]" id="next_step_title2" class="form-control @error('next_step.title2') is-invalid @enderror"
                                    value="{{ old('next_step.title2', $homeGihq->nextStep->title2 ?? '') }}" placeholder="Enter Title 2">
                                @error('next_step.title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-md-12">
                                <label for="next_step_tagline" class="form-label">Tagline</label>
                                <input type="text" name="next_step[tagline]" id="next_step_tagline" class="form-control @error('next_step.tagline') is-invalid @enderror"
                                    value="{{ old('next_step.tagline', $homeGihq->nextStep->tagline ?? '') }}" placeholder="Enter Tagline">
                                @error('next_step.tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Button Texts --}}
                            <div class="col-md-3">
                                <label for="next_step_certificate_btn_text" class="form-label">Certificate Button Text</label>
                                <input type="text" name="next_step[certificate_btn_text]" id="next_step_certificate_btn_text"
                                    class="form-control @error('next_step.certificate_btn_text') is-invalid @enderror"
                                    value="{{ old('next_step.certificate_btn_text', $homeGihq->nextStep->certificate_btn_text ?? '') }}" placeholder="Certificate Button Text">
                                @error('next_step.certificate_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="next_step_learning_btn_text" class="form-label">Learning Button Text</label>
                                <input type="text" name="next_step[learning_btn_text]" id="next_step_learning_btn_text"
                                    class="form-control @error('next_step.learning_btn_text') is-invalid @enderror"
                                    value="{{ old('next_step.learning_btn_text', $homeGihq->nextStep->learning_btn_text ?? '') }}" placeholder="Learning Button Text">
                                @error('next_step.learning_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="next_step_advisory_btn_text" class="form-label">Advisory Button Text</label>
                                <input type="text" name="next_step[advisory_btn_text]" id="next_step_advisory_btn_text"
                                    class="form-control @error('next_step.advisory_btn_text') is-invalid @enderror"
                                    value="{{ old('next_step.advisory_btn_text', $homeGihq->nextStep->advisory_btn_text ?? '') }}" placeholder="Advisory Button Text">
                                @error('next_step.advisory_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="next_step_member_btn_text" class="form-label">Member Button Text</label>
                                <input type="text" name="next_step[member_btn_text]" id="next_step_member_btn_text" class="form-control @error('next_step.member_btn_text') is-invalid @enderror"
                                    value="{{ old('next_step.member_btn_text', $homeGihq->nextStep->member_btn_text ?? '') }}" placeholder="Member Button Text">
                                @error('next_step.member_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Page Content Injection --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <i class="ri-file-code-line me-2 fs-18 text-secondary"></i>
                        <h4 class="card-title mb-0 flex-grow-1">Page Content Injection</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <label for="content_file" class="form-label">Content File (.html)</label>
                                <input type="file" name="content_file" id="content_file" class="form-control @error('content_file') is-invalid @enderror" accept=".html,.txt">
                                <small class="text-muted">Only .html or .txt files are accepted.</small>
                                @error('content_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @if ($homeGihq->content_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($homeGihq->content_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-file-code me-1"></i> View Current File ({{ basename($homeGihq->content_file) }})
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

                            <div class="col-md-6">
                                <label for="injected_status" class="form-label">Injected Status</label>
                                <select class="form-select" name="injected_status" id="injected_status">
                                    <option value="0" {{ old('injected_status', $homeGihq->injected_status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    <option value="1" {{ old('injected_status', $homeGihq->injected_status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Services & Pathways
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.repeater-services').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('textarea, input[type="text"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this item?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            $('.repeater-professional').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('textarea, input[type="text"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this item?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });
        });
    </script>
@endpush
