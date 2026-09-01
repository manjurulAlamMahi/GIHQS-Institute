@extends('backend.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Accreditation Header</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item">Accreditation</li>
                        <li class="breadcrumb-item active">Accreditation Header</li>
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
            <form action="{{ route('admin.accreditation-header.update', $header->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Card 1: Accreditation Header Settings --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Header Configuration</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Title 1 --}}
                            <div class="col-md-4">
                                <label for="title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="title1" id="title1" class="form-control @error('title1') is-invalid @enderror"
                                    value="{{ old('title1', $header->title1) }}" placeholder="Enter Title 1" required>
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title 2 --}}
                            <div class="col-md-4">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2" class="form-control @error('title2') is-invalid @enderror"
                                    value="{{ old('title2', $header->title2) }}" placeholder="Enter Title 2">
                                @error('title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-md-4">
                                <label for="tagline" class="form-label">Tagline</label>
                                <input type="text" name="tagline" id="tagline" class="form-control @error('tagline') is-invalid @enderror"
                                    value="{{ old('tagline', $header->tagline) }}" placeholder="Enter Tagline">
                                @error('tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter Description">{{ old('description', $header->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Note --}}
                            <div class="col-md-12">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="2" placeholder="Enter Note or Notice">{{ old('note', $header->note) }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Apply Button Text --}}
                            <div class="col-md-4">
                                <label for="apply_btn_text" class="form-label">Apply Button Text</label>
                                <input type="text" name="apply_btn_text" id="apply_btn_text" class="form-control @error('apply_btn_text') is-invalid @enderror"
                                    value="{{ old('apply_btn_text', $header->apply_btn_text) }}" placeholder="e.g. Apply Now">
                                @error('apply_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Download Button Text --}}
                            <div class="col-md-4">
                                <label for="download_btn_text" class="form-label">Download Button Text</label>
                                <input type="text" name="download_btn_text" id="download_btn_text" class="form-control @error('download_btn_text') is-invalid @enderror"
                                    value="{{ old('download_btn_text', $header->download_btn_text) }}" placeholder="e.g. Download Brochure">
                                @error('download_btn_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Download File --}}
                            <div class="col-md-4">
                                <label for="download_file" class="form-label">Download File</label>
                                <input type="file" name="download_file" id="download_file" class="form-control @error('download_file') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                @error('download_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @if ($header->download_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($header->download_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="ri-file-download-line me-1"></i> Current File ({{ basename($header->download_file) }})
                                        </a>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_download_file" id="remove_download_file" value="1">
                                            <label class="form-check-label text-danger fw-semibold" for="remove_download_file">
                                                Remove File
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Accreditation Tags Repeater --}}
                        <div class="border-top pt-3 mt-4">
                            <h5 class="card-title mb-3 fs-15 text-muted">Accreditation Tags</h5>
                            <div class="repeater-tags">
                                <div data-repeater-list="tags">
                                    @if ($header->tags->count() > 0)
                                        @foreach ($header->tags as $item)
                                            <div data-repeater-item class="row mb-3 align-items-center p-2 border rounded bg-light gy-2">
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <div class="col-md-10">
                                                    <label class="form-label">Tag Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="tagname" class="form-control" required placeholder="Enter tag name" value="{{ $item->tagname }}">
                                                </div>
                                                <div class="col-md-2 text-end mt-4">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                        <i class="ri-delete-bin-line"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-center p-2 border rounded bg-light gy-2">
                                            <input type="hidden" name="id" value="">
                                            <div class="col-md-10">
                                                <label class="form-label">Tag Name <span class="text-danger">*</span></label>
                                                <input type="text" name="tagname" class="form-control" required placeholder="Enter tag name">
                                            </div>
                                            <div class="col-md-2 text-end mt-4">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                    <i class="ri-delete-bin-line"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="ri-add-line"></i> Add Tag
                                </button>
                            </div>
                        </div>

                        {{-- Accreditation Keyfacts Repeater --}}
                        <div class="border-top pt-3 mt-4">
                            <h5 class="card-title mb-3 fs-15 text-muted">Accreditation Key Facts</h5>
                            <div class="repeater-keyfacts">
                                <div data-repeater-list="keyfacts">
                                    @if ($header->keyfacts->count() > 0)
                                        @foreach ($header->keyfacts as $item)
                                            <div data-repeater-item class="row mb-3 align-items-center p-2 border rounded bg-light gy-2">
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <div class="col-md-4">
                                                    <label class="form-label">Fact Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" required placeholder="e.g. 99% or 150+" value="{{ $item->title }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Fact Subtitle</label>
                                                    <input type="text" name="subtitle" class="form-control" placeholder="e.g. Approval Rate" value="{{ $item->subtitle }}">
                                                </div>
                                                <div class="col-md-2 text-end mt-4">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                        <i class="ri-delete-bin-line"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-center p-2 border rounded bg-light gy-2">
                                            <input type="hidden" name="id" value="">
                                            <div class="col-md-4">
                                                <label class="form-label">Fact Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" required placeholder="e.g. 99% or 150+">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Fact Subtitle</label>
                                                <input type="text" name="subtitle" class="form-control" placeholder="e.g. Approval Rate">
                                            </div>
                                            <div class="col-md-2 text-end mt-4">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                    <i class="ri-delete-bin-line"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="ri-add-line"></i> Add Key Fact
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Page Content Injection Section --}}
                <div class="card mb-4 shadow-sm border-light-subtle">
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
                        <i class="ri-save-line me-1"></i> Update Accreditation Header
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {


            // 1. Tags Repeater
            $('.repeater-tags').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this tag?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            // 2. Key Facts Repeater
            $('.repeater-keyfacts').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this key fact?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });
        });
    </script>
@endpush
