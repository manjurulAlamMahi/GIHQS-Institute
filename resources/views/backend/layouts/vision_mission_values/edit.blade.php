@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Content Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Vision Mission Values</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.vision-mission-values.update', $vmv->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- ==================== General Section ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-file-text-line me-2 fs-18 text-primary"></i>
                        <h5 class="card-title mb-0">General Section</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <label for="tagline" class="form-label">Tagline</label>
                                <input type="text" name="tagline" id="tagline" class="form-control @error('tagline') is-invalid @enderror" value="{{ old('tagline', $vmv->tagline) }}"
                                    placeholder="Enter tagline">
                                @error('tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="title1" class="form-label">Title 1</label>
                                <input type="text" name="title1" id="title1" class="form-control @error('title1') is-invalid @enderror" value="{{ old('title1', $vmv->title1) }}"
                                    placeholder="Enter title 1">
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2" class="form-control @error('title2') is-invalid @enderror" value="{{ old('title2', $vmv->title2) }}"
                                    placeholder="Enter title 2">
                                @error('title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea name="short_description" id="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="3" placeholder="Enter short description">{{ old('short_description', $vmv->short_description) }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== Mission Section ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-rocket-line me-2 fs-18 text-success"></i>
                        <h5 class="card-title mb-0">Mission</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <label for="mission_tagline" class="form-label">Mission Tagline</label>
                                <input type="text" name="mission_tagline" id="mission_tagline" class="form-control @error('mission_tagline') is-invalid @enderror"
                                    value="{{ old('mission_tagline', $vmv->mission_tagline) }}" placeholder="Enter mission tagline">
                                @error('mission_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="mission_title" class="form-label">Mission Title</label>
                                <input type="text" name="mission_title" id="mission_title" class="form-control @error('mission_title') is-invalid @enderror"
                                    value="{{ old('mission_title', $vmv->mission_title) }}" placeholder="Enter mission title">
                                @error('mission_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="mission_short_description" class="form-label">Mission Short Description</label>
                                <textarea name="mission_short_description" id="mission_short_description" class="form-control @error('mission_short_description') is-invalid @enderror" rows="3"
                                    placeholder="Enter mission short description">{{ old('mission_short_description', $vmv->mission_short_description) }}</textarea>
                                @error('mission_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== Vision Section ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-eye-line me-2 fs-18 text-info"></i>
                        <h5 class="card-title mb-0">Vision</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <label for="vision_tagline" class="form-label">Vision Tagline</label>
                                <input type="text" name="vision_tagline" id="vision_tagline" class="form-control @error('vision_tagline') is-invalid @enderror"
                                    value="{{ old('vision_tagline', $vmv->vision_tagline) }}" placeholder="Enter vision tagline">
                                @error('vision_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="vision_title" class="form-label">Vision Title</label>
                                <input type="text" name="vision_title" id="vision_title" class="form-control @error('vision_title') is-invalid @enderror"
                                    value="{{ old('vision_title', $vmv->vision_title) }}" placeholder="Enter vision title">
                                @error('vision_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="vision_short_description" class="form-label">Vision Short Description</label>
                                <textarea name="vision_short_description" id="vision_short_description" class="form-control @error('vision_short_description') is-invalid @enderror" rows="3"
                                    placeholder="Enter vision short description">{{ old('vision_short_description', $vmv->vision_short_description) }}</textarea>
                                @error('vision_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== Value Section ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-heart-line me-2 fs-18 text-danger"></i>
                        <h5 class="card-title mb-0">Values</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <label for="value_tagline" class="form-label">Value Tagline</label>
                                <input type="text" name="value_tagline" id="value_tagline" class="form-control @error('value_tagline') is-invalid @enderror"
                                    value="{{ old('value_tagline', $vmv->value_tagline) }}" placeholder="Enter value tagline">
                                @error('value_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="value_title" class="form-label">Value Title</label>
                                <input type="text" name="value_title" id="value_title" class="form-control @error('value_title') is-invalid @enderror"
                                    value="{{ old('value_title', $vmv->value_title) }}" placeholder="Enter value title">
                                @error('value_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="value_title2" class="form-label">Value Title 2</label>
                                <input type="text" name="value_title2" id="value_title2" class="form-control @error('value_title2') is-invalid @enderror"
                                    value="{{ old('value_title2', $vmv->value_title2) }}" placeholder="Enter value title 2">
                                @error('value_title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="value_short_description" class="form-label">Value Short Description</label>
                                <textarea name="value_short_description" id="value_short_description" class="form-control @error('value_short_description') is-invalid @enderror" rows="3"
                                    placeholder="Enter value short description">{{ old('value_short_description', $vmv->value_short_description) }}</textarea>
                                @error('value_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== Global Perspective Section ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-global-line me-2 fs-18 text-warning"></i>
                        <h5 class="card-title mb-0">Global Perspective</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <label for="global_perspective_tagline" class="form-label">Global Perspective Section Tag / Icon Letter</label>
                                <input type="text" name="global_perspective_tagline" id="global_perspective_tagline" class="form-control @error('global_perspective_tagline') is-invalid @enderror"
                                    value="{{ old('global_perspective_tagline', $vmv->global_perspective_tagline) }}" placeholder="Enter Section Tag / Icon Letter">
                                @error('global_perspective_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="global_perspective_title" class="form-label">Global Perspective Title</label>
                                <input type="text" name="global_perspective_title" id="global_perspective_title" class="form-control @error('global_perspective_title') is-invalid @enderror"
                                    value="{{ old('global_perspective_title', $vmv->global_perspective_title) }}" placeholder="Enter title">
                                @error('global_perspective_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="global_perspective_short_description" class="form-label">Global Perspective Short Description</label>
                                <textarea name="global_perspective_short_description" id="global_perspective_short_description" class="form-control @error('global_perspective_short_description') is-invalid @enderror"
                                    rows="3" placeholder="Enter short description">{{ old('global_perspective_short_description', $vmv->global_perspective_short_description) }}</textarea>
                                @error('global_perspective_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== Integrity Section ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-shield-check-line me-2 fs-18 text-primary"></i>
                        <h5 class="card-title mb-0">Integrity</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <label for="integrity_tagline" class="form-label">Integrity Section Tag / Icon Letter</label>
                                <input type="text" name="integrity_tagline" id="integrity_tagline" class="form-control @error('integrity_tagline') is-invalid @enderror"
                                    value="{{ old('integrity_tagline', $vmv->integrity_tagline) }}" placeholder="Enter Section Tag / Icon Letter">
                                @error('integrity_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="integrity_title" class="form-label">Integrity Title</label>
                                <input type="text" name="integrity_title" id="integrity_title" class="form-control @error('integrity_title') is-invalid @enderror"
                                    value="{{ old('integrity_title', $vmv->integrity_title) }}" placeholder="Enter title">
                                @error('integrity_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="integrity_short_description" class="form-label">Integrity Short Description</label>
                                <textarea name="integrity_short_description" id="integrity_short_description" class="form-control @error('integrity_short_description') is-invalid @enderror" rows="3"
                                    placeholder="Enter short description">{{ old('integrity_short_description', $vmv->integrity_short_description) }}</textarea>
                                @error('integrity_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== Human Centered Section ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-user-heart-line me-2 fs-18 text-info"></i>
                        <h5 class="card-title mb-0">Human Centered</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <label for="human_centered_tagline" class="form-label">Human Centered Section Tag / Icon Letter</label>
                                <input type="text" name="human_centered_tagline" id="human_centered_tagline" class="form-control @error('human_centered_tagline') is-invalid @enderror"
                                    value="{{ old('human_centered_tagline', $vmv->human_centered_tagline) }}" placeholder="Enter Section Tag / Icon Letter">
                                @error('human_centered_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="human_centered_title" class="form-label">Human Centered Title</label>
                                <input type="text" name="human_centered_title" id="human_centered_title" class="form-control @error('human_centered_title') is-invalid @enderror"
                                    value="{{ old('human_centered_title', $vmv->human_centered_title) }}" placeholder="Enter title">
                                @error('human_centered_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="human_centered_short_description" class="form-label">Human Centered Short Description</label>
                                <textarea name="human_centered_short_description" id="human_centered_short_description" class="form-control @error('human_centered_short_description') is-invalid @enderror" rows="3"
                                    placeholder="Enter short description">{{ old('human_centered_short_description', $vmv->human_centered_short_description) }}</textarea>
                                @error('human_centered_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== Quality & Excellence Section ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-star-line me-2 fs-18 text-warning"></i>
                        <h5 class="card-title mb-0">Quality & Excellence</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <label for="quality_excellence_tagline" class="form-label">Quality & Excellence Section Tag / Icon Letter</label>
                                <input type="text" name="quality_excellence_tagline" id="quality_excellence_tagline" class="form-control @error('quality_excellence_tagline') is-invalid @enderror"
                                    value="{{ old('quality_excellence_tagline', $vmv->quality_excellence_tagline) }}" placeholder="Enter Section Tag / Icon Letter">
                                @error('quality_excellence_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="quality_excellence_title" class="form-label">Quality & Excellence Title</label>
                                <input type="text" name="quality_excellence_title" id="quality_excellence_title" class="form-control @error('quality_excellence_title') is-invalid @enderror"
                                    value="{{ old('quality_excellence_title', $vmv->quality_excellence_title) }}" placeholder="Enter title">
                                @error('quality_excellence_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="quality_excellence_short_description" class="form-label">Quality & Excellence Short Description</label>
                                <textarea name="quality_excellence_short_description" id="quality_excellence_short_description" class="form-control @error('quality_excellence_short_description') is-invalid @enderror"
                                    rows="3" placeholder="Enter short description">{{ old('quality_excellence_short_description', $vmv->quality_excellence_short_description) }}</textarea>
                                @error('quality_excellence_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== Safety Leadership Section ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-medal-line me-2 fs-18 text-danger"></i>
                        <h5 class="card-title mb-0">Safety Leadership</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <label for="safety_leadership_tagline" class="form-label">Safety Leadership Section Tag / Icon Letter</label>
                                <input type="text" name="safety_leadership_tagline" id="safety_leadership_tagline" class="form-control @error('safety_leadership_tagline') is-invalid @enderror"
                                    value="{{ old('safety_leadership_tagline', $vmv->safety_leadership_tagline) }}" placeholder="Enter Section Tag / Icon Letter">
                                @error('safety_leadership_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="safety_leadership_title" class="form-label">Safety Leadership Title</label>
                                <input type="text" name="safety_leadership_title" id="safety_leadership_title" class="form-control @error('safety_leadership_title') is-invalid @enderror"
                                    value="{{ old('safety_leadership_title', $vmv->safety_leadership_title) }}" placeholder="Enter title">
                                @error('safety_leadership_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="safety_leadership_short_description" class="form-label">Safety Leadership Short Description</label>
                                <textarea name="safety_leadership_short_description" id="safety_leadership_short_description" class="form-control @error('safety_leadership_short_description') is-invalid @enderror"
                                    rows="3" placeholder="Enter short description">{{ old('safety_leadership_short_description', $vmv->safety_leadership_short_description) }}</textarea>
                                @error('safety_leadership_short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== Content File & Injected Status ==================== --}}
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="ri-file-code-line me-2 fs-18 text-secondary"></i>
                        <h5 class="card-title mb-0">Page Content Injection</h5>
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

                                @if ($vmv->content_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($vmv->content_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-file-code me-1"></i> View Current File ({{ basename($vmv->content_file) }})
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
                                    <option value="0" {{ old('injected_status', $vmv->injected_status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    <option value="1" {{ old('injected_status', $vmv->injected_status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="d-flex justify-content-end mb-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Vision Mission Values
                    </button>
                </div>

            </div>
        </div>
    </form>
@endsection
