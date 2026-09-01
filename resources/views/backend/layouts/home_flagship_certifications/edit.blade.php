@extends('backend.app')
@push('styles')
    <style>
        /* Compact Dropify Styles for Flagship Certifications Icons */
        .repeater-certificates .dropify-wrapper .dropify-message p {
            font-size: 13px !important;
            font-weight: 500;
        }

        .repeater-certificates .dropify-wrapper .dropify-message span.file-icon {
            font-size: 18px !important;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Home Page Module</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home Page</a></li>
                        <li class="breadcrumb-item active">Flagship Certifications</li>
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
            <form action="{{ route('admin.home-flagship-certifications.update', $homeRecognizedPathway->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Card 1: Recognized Pathways Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Recognized Pathways Header</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Title 1 --}}
                            <div class="col-md-6">
                                <label for="title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="title1" id="title1"
                                    class="form-control @error('title1') is-invalid @enderror"
                                    value="{{ old('title1', $homeRecognizedPathway->title1) }}"
                                    placeholder="Enter Title 1" required>
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title 2 --}}
                            <div class="col-md-6">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2"
                                    class="form-control @error('title2') is-invalid @enderror"
                                    value="{{ old('title2', $homeRecognizedPathway->title2) }}"
                                    placeholder="Enter Title 2">
                                @error('title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-md-12">
                                <label for="tagline" class="form-label">Tagline</label>
                                <input type="text" name="tagline" id="tagline"
                                    class="form-control @error('tagline') is-invalid @enderror"
                                    value="{{ old('tagline', $homeRecognizedPathway->tagline) }}"
                                    placeholder="Enter Tagline">
                                @error('tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Enter Description">{{ old('description', $homeRecognizedPathway->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Certifications Repeater Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Flagship Certifications</h4>
                    </div>
                    <div class="card-body">
                        <div class="repeater-certificates">
                            <div data-repeater-list="certificates">
                                @if ($homeRecognizedPathway->certificates->count() > 0)
                                    @foreach ($homeRecognizedPathway->certificates as $index => $item)
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $item->id }}">

                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Short Title</label>
                                                <input type="text" name="short_title" class="form-control" value="{{ $item->short_title }}" placeholder="e.g. GIHQS-BP">
                                            </div>

                                            <div class="col-md-8 mb-2">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ $item->title }}" placeholder="Full Certification Title" required>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Icon (Image/SVG)</label>
                                                <input type="file" name="icon" class="form-control dropify-click-here" data-allowed-file-extensions="svg png jpeg jpg" data-height="75"
                                                    data-default-file="{{ $item->icon ? asset($item->icon) : '' }}">
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Tagline</label>
                                                <input type="text" name="tagline" class="form-control" value="{{ $item->tagline }}" placeholder="Tagline">
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Headline</label>
                                                <input type="text" name="headline" class="form-control" value="{{ $item->headline }}" placeholder="Headline">
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Description">{{ $item->description }}</textarea>
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Audience</label>
                                                <input type="text" name="audience" class="form-control" value="{{ $item->audience }}" placeholder="e.g. Healthcare professionals">
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Tags (comma-separated)</label>
                                                <input type="text" name="tags" class="form-control" value="{{ $item->tags }}" placeholder="e.g. Quality, Safety, Process">
                                            </div>

                                            <div class="col-md-10 mb-2">
                                                <label class="form-label">Button Text</label>
                                                <input type="text" name="button_text" class="form-control" value="{{ $item->button_text }}" placeholder="e.g. View Pathway">
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

                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Short Title</label>
                                            <input type="text" name="short_title" class="form-control" placeholder="e.g. GIHQS-BP">
                                        </div>

                                        <div class="col-md-8 mb-2">
                                            <label class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" placeholder="Full Certification Title" required>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Icon (Image/SVG)</label>
                                            <input type="file" name="icon" class="form-control dropify-click-here" data-allowed-file-extensions="svg png jpeg jpg" data-height="75">
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Tagline</label>
                                            <input type="text" name="tagline" class="form-control" placeholder="Tagline">
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Headline</label>
                                            <input type="text" name="headline" class="form-control" placeholder="Headline">
                                        </div>

                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="2" placeholder="Description"></textarea>
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Audience</label>
                                            <input type="text" name="audience" class="form-control" placeholder="e.g. Healthcare professionals">
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Tags (comma-separated)</label>
                                            <input type="text" name="tags" class="form-control" placeholder="e.g. Quality, Safety, Process">
                                        </div>

                                        <div class="col-md-10 mb-2">
                                            <label class="form-label">Button Text</label>
                                            <input type="text" name="button_text" class="form-control" placeholder="e.g. View Pathway">
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
                                <i class="fa-solid fa-plus"></i> Add Certification
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Page Content Injection --}}
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

                                @if ($homeRecognizedPathway->content_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($homeRecognizedPathway->content_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-file-code me-1"></i> View Current File ({{ basename($homeRecognizedPathway->content_file) }})
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
                                    <option value="0" {{ old('injected_status', $homeRecognizedPathway->injected_status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    <option value="1" {{ old('injected_status', $homeRecognizedPathway->injected_status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Flagship Certifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
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

            $('.repeater-certificates').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('textarea, input[type="text"]').val('');
                    $(this).find('input[type="file"]').val('');
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
                    if (confirm('Are you sure you want to delete this certification?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });
        });
    </script>
@endpush
