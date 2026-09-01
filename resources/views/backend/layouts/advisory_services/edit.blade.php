@extends('backend.app')
@push('styles')
    <style>
        /* Compact Dropify Styles for Advisory Overview Scope Icon */
        .repeater-scope .dropify-wrapper .dropify-message p {
            font-size: 13px !important;
            font-weight: 500;
        }

        .repeater-scope .dropify-wrapper .dropify-message span.file-icon {
            font-size: 18px !important;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Advisory Services</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item">Advisory</li>
                        <li class="breadcrumb-item active">Advisory Services</li>
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
        <div class="col-lg-11">
            <form action="{{ route('admin.advisory-services.update', $header->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Card 1: Section 1 - Advisory Panel Header --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 1: Advisory Panel Header</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Title 1 --}}
                            <div class="col-md-4">
                                <label for="header_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="header_title1" id="header_title1" class="form-control @error('header_title1') is-invalid @enderror"
                                    value="{{ old('header_title1', $header->title1) }}" placeholder="Enter Title 1">
                                @error('header_title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title 2 --}}
                            <div class="col-md-4">
                                <label for="header_title2" class="form-label">Title 2</label>
                                <input type="text" name="header_title2" id="header_title2" class="form-control @error('header_title2') is-invalid @enderror"
                                    value="{{ old('header_title2', $header->title2) }}" placeholder="Enter Title 2">
                                @error('header_title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-md-4">
                                <label for="header_tagline" class="form-label">Tagline</label>
                                <input type="text" name="header_tagline" id="header_tagline" class="form-control @error('header_tagline') is-invalid @enderror"
                                    value="{{ old('header_tagline', $header->tagline) }}" placeholder="Enter Tagline">
                                @error('header_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12">
                                <label for="header_description" class="form-label">Description</label>
                                <textarea name="header_description" id="header_description" class="form-control @error('header_description') is-invalid @enderror" rows="3" placeholder="Enter Description">{{ old('header_description', $header->description) }}</textarea>
                                @error('header_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Section 2 - Focus & Features --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 2: Focus & Features</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            {{-- Focus Title --}}
                            <div class="col-md-12">
                                <label for="focus_title" class="form-label">Title</label>
                                <input type="text" name="focus_title" id="focus_title" class="form-control @error('focus_title') is-invalid @enderror" value="{{ old('focus_title', $focus->title) }}"
                                    placeholder="Enter Focus Title">
                                @error('focus_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Focus Description --}}
                            <div class="col-md-12">
                                <label for="focus_description" class="form-label">Description</label>
                                <textarea name="focus_description" id="focus_description" class="form-control @error('focus_description') is-invalid @enderror" rows="3" placeholder="Enter Focus Description">{{ old('focus_description', $focus->description) }}</textarea>
                                @error('focus_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Focus Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3 fs-15 text-muted">Focus Features</h5>
                            <div class="repeater-focus">
                                <div data-repeater-list="focus_features">
                                    @if ($focus->features->count() > 0)
                                        @foreach ($focus->features as $item)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <div class="col">
                                                    <label class="form-label">
                                                        <span class="badge bg-secondary me-1 serial-badge">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                        Description <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea name="description" class="form-control" rows="2" required placeholder="Enter feature description">{{ $item->description }}</textarea>
                                                </div>
                                                <div class="col-auto mt-4">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                        <i class="ri-delete-bin-line"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">
                                            <div class="col">
                                                <label class="form-label">
                                                    <span class="badge bg-secondary me-1 serial-badge">01</span>
                                                    Description <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="description" class="form-control" rows="2" required placeholder="Enter feature description"></textarea>
                                            </div>
                                            <div class="col-auto mt-4">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="ri-delete-bin-line"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="ri-add-line"></i> Add Focus Feature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Section 3 - Advisory Scope --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 3: Advisory Scope</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            {{-- Scope Title 1 --}}
                            <div class="col-md-6">
                                <label for="scope_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="scope_title1" id="scope_title1" class="form-control @error('scope_title1') is-invalid @enderror"
                                    value="{{ old('scope_title1', $scope->title1) }}" placeholder="Enter Scope Title 1">
                                @error('scope_title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Scope Title 2 --}}
                            <div class="col-md-6">
                                <label for="scope_title2" class="form-label">Title 2</label>
                                <input type="text" name="scope_title2" id="scope_title2" class="form-control @error('scope_title2') is-invalid @enderror"
                                    value="{{ old('scope_title2', $scope->title2) }}" placeholder="Enter Scope Title 2">
                                @error('scope_title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Scope Description --}}
                            <div class="col-md-12">
                                <label for="scope_description" class="form-label">Description</label>
                                <textarea name="scope_description" id="scope_description" class="form-control @error('scope_description') is-invalid @enderror" rows="3" placeholder="Enter Scope Description">{{ old('scope_description', $scope->description) }}</textarea>
                                @error('scope_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Scope Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3 fs-15 text-muted">Scope Features</h5>
                            <div class="repeater-scope">
                                <div data-repeater-list="scope_features">
                                    @if ($scope->features->count() > 0)
                                        @foreach ($scope->features as $item)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $item->id }}">

                                                <div class="col-md-2">
                                                    <label class="form-label">Icon (Image/SVG)</label>
                                                    <input type="file" name="icon" class="form-control dropify-click-here" data-allowed-file-extensions="svg png jpeg jpg" data-height="75"
                                                        data-default-file="{{ $item->icon ? asset($item->icon) : '' }}">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" required placeholder="Enter feature title" value="{{ $item->title }}">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="2" placeholder="Enter feature description">{{ $item->description }}</textarea>
                                                </div>

                                                <div class="col-12 text-end mt-2">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                        <i class="ri-delete-bin-line"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">

                                            <div class="col-md-2">
                                                <label class="form-label">Icon (Image/SVG)</label>
                                                <input type="file" name="icon" class="form-control dropify-click-here" data-allowed-file-extensions="svg png jpeg jpg" data-height="75">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" required placeholder="Enter feature title">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Enter feature description"></textarea>
                                            </div>

                                            <div class="col-12 text-end mt-2">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="ri-delete-bin-line"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="ri-add-line"></i> Add Scope Feature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Section 4 - Deliverable Card --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 4: Deliverable Card</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            {{-- Deliverable Title 1 --}}
                            <div class="col-md-6">
                                <label for="deliverable_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="deliverable_title1" id="deliverable_title1" class="form-control @error('deliverable_title1') is-invalid @enderror"
                                    value="{{ old('deliverable_title1', $deliverable->title1) }}" placeholder="Enter Deliverable Title 1">
                                @error('deliverable_title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deliverable Title 2 --}}
                            <div class="col-md-6">
                                <label for="deliverable_title2" class="form-label">Title 2</label>
                                <input type="text" name="deliverable_title2" id="deliverable_title2" class="form-control @error('deliverable_title2') is-invalid @enderror"
                                    value="{{ old('deliverable_title2', $deliverable->title2) }}" placeholder="Enter Deliverable Title 2">
                                @error('deliverable_title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deliverable Description --}}
                            <div class="col-md-12">
                                <label for="deliverable_description" class="form-label">Description</label>
                                <textarea name="deliverable_description" id="deliverable_description" class="form-control @error('deliverable_description') is-invalid @enderror" rows="3"
                                    placeholder="Enter Deliverable Description">{{ old('deliverable_description', $deliverable->description) }}</textarea>
                                @error('deliverable_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Deliverable Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3 fs-15 text-muted">Deliverable Features</h5>
                            <div class="repeater-deliverable">
                                <div data-repeater-list="deliverable_features">
                                    @if ($deliverable->features->count() > 0)
                                        @foreach ($deliverable->features as $item)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <div class="col">
                                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" required placeholder="Enter feature name" value="{{ $item->name }}">
                                                </div>
                                                <div class="col-auto mt-4">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                        <i class="ri-delete-bin-line"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">
                                            <div class="col">
                                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" required placeholder="Enter feature name">
                                            </div>
                                            <div class="col-auto mt-4">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="ri-delete-bin-line"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="ri-add-line"></i> Add Deliverable Feature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Section 5 - Service Packages --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 5: Service Packages</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            {{-- Service Title 1 --}}
                            <div class="col-md-6">
                                <label for="service_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="service_title1" id="service_title1" class="form-control @error('service_title1') is-invalid @enderror"
                                    value="{{ old('service_title1', $service->title1) }}" placeholder="Enter Service Title 1">
                                @error('service_title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Service Title 2 --}}
                            <div class="col-md-6">
                                <label for="service_title2" class="form-label">Title 2</label>
                                <input type="text" name="service_title2" id="service_title2" class="form-control @error('service_title2') is-invalid @enderror"
                                    value="{{ old('service_title2', $service->title2) }}" placeholder="Enter Service Title 2">
                                @error('service_title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Service Description --}}
                            <div class="col-md-12">
                                <label for="service_description" class="form-label">Description</label>
                                <textarea name="service_description" id="service_description" class="form-control @error('service_description') is-invalid @enderror" rows="3" placeholder="Enter Service Description">{{ old('service_description', $service->description) }}</textarea>
                                @error('service_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Service Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3 fs-15 text-muted">Service Features</h5>
                            <div class="repeater-service">
                                <div data-repeater-list="service_features">
                                    @if ($service->features->count() > 0)
                                        @foreach ($service->features as $item)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $item->id }}">

                                                <div class="col-md-1">
                                                    <label class="form-label">Serial Number</label>
                                                    <input type="text" name="serial_number" class="form-control" placeholder="e.g. 01" value="{{ $item->serial_number }}">
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="form-label">Tagline</label>
                                                    <input type="text" name="tagline" class="form-control" placeholder="e.g. PRESET PACK" value="{{ $item->tagline }}">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" required placeholder="Enter service title" value="{{ $item->title }}">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="2" placeholder="Enter service description">{{ $item->description }}</textarea>
                                                </div>

                                                <div class="col-12 text-end mt-2">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                        <i class="ri-delete-bin-line"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">

                                            <div class="col-md-2">
                                                <label class="form-label">Serial Number</label>
                                                <input type="text" name="serial_number" class="form-control" placeholder="e.g. 01">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Tagline</label>
                                                <input type="text" name="tagline" class="form-control" placeholder="e.g. PRESET PACK">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" required placeholder="Enter service title">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Enter service description"></textarea>
                                            </div>

                                            <div class="col-12 text-end mt-2">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="ri-delete-bin-line"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="ri-add-line"></i> Add Service Feature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 6: Section 6 - Discuss Card --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 6: Discuss Card</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Discuss Title 1 --}}
                            <div class="col-md-4">
                                <label for="discuss_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="discuss_title1" id="discuss_title1" class="form-control @error('discuss_title1') is-invalid @enderror"
                                    value="{{ old('discuss_title1', $discuss->title1) }}" placeholder="Enter Discuss Title 1">
                                @error('discuss_title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Discuss Title 2 --}}
                            <div class="col-md-4">
                                <label for="discuss_title2" class="form-label">Title 2</label>
                                <input type="text" name="discuss_title2" id="discuss_title2" class="form-control @error('discuss_title2') is-invalid @enderror"
                                    value="{{ old('discuss_title2', $discuss->title2) }}" placeholder="Enter Discuss Title 2">
                                @error('discuss_title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Discuss Button Text --}}
                            <div class="col-md-4">
                                <label for="discuss_btn_text" class="form-label">Button Text</label>
                                <input type="text" name="discuss_btn_text" id="discuss_btn_text" class="form-control @error('discuss_btn_text') is-invalid @enderror"
                                    value="{{ old('discuss_btn_text', $discuss->button_text) }}" placeholder="Enter Button Text">
                                @error('discuss_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Discuss Description --}}
                            <div class="col-md-12">
                                <label for="discuss_description" class="form-label">Description</label>
                                <textarea name="discuss_description" id="discuss_description" class="form-control @error('discuss_description') is-invalid @enderror" rows="3" placeholder="Enter Discuss Description">{{ old('discuss_description', $discuss->description) }}</textarea>
                                @error('discuss_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 7: Page Content Injection --}}
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

                                @if ($header->content_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($header->content_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="ri-file-search-line me-1"></i> View Current File ({{ basename($header->content_file) }})
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
                                    <option value="0" {{ old('injected_status', $header->injected_status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    <option value="1" {{ old('injected_status', $header->injected_status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4 py-2">
                        <i class="ri-save-line me-1"></i> Update Advisory Services
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // 1. Focus Features Repeater
            $('.repeater-focus').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('textarea').val('');
                    $(this).find('input[type="hidden"]').val('');
                    reindexFocus();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this focus feature?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexFocus();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            function reindexFocus() {
                $('.repeater-focus [data-repeater-item]').each(function(i) {
                    $(this).find('.serial-badge').text(String(i + 1).padStart(2, '0'));
                });
            }
            reindexFocus();

            // Initialize Dropify
            $('.dropify-click-here').dropify({
                messages: {
                    'default': 'Click Here',
                    'replace': 'Click Here',
                    'remove': 'Remove',
                    'error': 'Ooops, something wrong appended.'
                }
            });

            // Force SVG preview for existing default files on page load
            $('.dropify-click-here').each(function() {
                let defaultFile = $(this).attr('data-default-file');
                if (defaultFile && (defaultFile.endsWith('.svg') || defaultFile.includes('.svg?'))) {
                    let wrapper = $(this).closest('.dropify-wrapper');
                    wrapper.find('.dropify-preview').show();
                    wrapper.find('.dropify-render').html($('<img>').attr('src', defaultFile).css('max-height', '100%'));
                }
            });

            // Force SVG preview on file change (using event delegation for repeaters)
            $(document).on('change', '.dropify-click-here', function() {
                let input = this;
                if (input.files && input.files[0]) {
                    let file = input.files[0];
                    if (file.name.endsWith('.svg') || file.type === 'image/svg+xml') {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            let wrapper = $(input).closest('.dropify-wrapper');
                            wrapper.find('.dropify-preview').show();
                            wrapper.find('.dropify-render').html($('<img>').attr('src', e.target.result).css('max-height', '100%'));
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });

            // 2. Scope Features Repeater
            $('.repeater-scope').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('textarea').val('');
                    $(this).find('input[type="hidden"]').val('');

                    // Handle Dropify cloning cleanup
                    let wrapper = $(this).find('.dropify-wrapper');
                    if (wrapper.length) {
                        let originalInput = wrapper.find('input[type="file"]');
                        let inputName = originalInput.attr('name');

                        let cleanInput = $('<input type="file" class="dropify-click-here" data-allowed-file-extensions="svg png jpeg jpg" data-height="75">');
                        cleanInput.attr('name', inputName);

                        wrapper.replaceWith(cleanInput);
                        cleanInput.dropify({
                            messages: {
                                'default': 'Click Here',
                                'replace': 'Click Here',
                                'remove': 'Remove',
                                'error': 'Ooops, something wrong appended.'
                            }
                        });
                    } else {
                        $(this).find('.dropify-click-here').dropify({
                            messages: {
                                'default': 'Click Here',
                                'replace': 'Click Here',
                                'remove': 'Remove',
                                'error': 'Ooops, something wrong appended.'
                            }
                        });
                    }
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this scope feature?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            // 3. Deliverable Features Repeater
            $('.repeater-deliverable').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this deliverable feature?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            // 4. Service Features Repeater
            $('.repeater-service').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('textarea').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this service feature?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });
        });
    </script>
@endpush
