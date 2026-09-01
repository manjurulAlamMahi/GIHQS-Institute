@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Content Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">About Institute</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Edit About Institute</h4>
                </div>

                <form action="{{ route('admin.about-institute.update', $aboutInstitute->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row gy-4">

                            {{-- Title 1 --}}
                            <div class="col-md-6">
                                <label for="title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="title1" id="title1"
                                    class="form-control @error('title1') is-invalid @enderror"
                                    value="{{ old('title1', $aboutInstitute->title1) }}"
                                    placeholder="Enter primary title">
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title 2 --}}
                            <div class="col-md-6">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2"
                                    class="form-control @error('title2') is-invalid @enderror"
                                    value="{{ old('title2', $aboutInstitute->title2) }}"
                                    placeholder="Enter secondary title">
                                @error('title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tag Line --}}
                            <div class="col-md-12">
                                <label for="tag_line" class="form-label">Tag Line</label>
                                <input type="text" name="tag_line" id="tag_line"
                                    class="form-control @error('tag_line') is-invalid @enderror"
                                    value="{{ old('tag_line', $aboutInstitute->tag_line) }}"
                                    placeholder="Enter tag line">
                                @error('tag_line')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description (CKEditor) --}}
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description"
                                    class="form-control ckeditor @error('description') is-invalid @enderror"
                                    rows="6"
                                    placeholder="Enter description">{{ old('description', $aboutInstitute->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Image --}}
                            <div class="col-md-12">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" name="image" id="image"
                                    class="form-control dropify"
                                    data-allowed-file-extensions="jpg jpeg png gif webp"
                                    @if ($aboutInstitute->image) data-default-file="{{ asset($aboutInstitute->image) }}" @endif>
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- ==================== Page Content Injection ==================== --}}
                    <div class="card-header align-items-center d-flex border-top">
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

                                @if ($aboutInstitute->content_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($aboutInstitute->content_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-file-code me-1"></i> View Current File ({{ basename($aboutInstitute->content_file) }})
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
                                    <option value="0" {{ old('injected_status', $aboutInstitute->injected_status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    <option value="1" {{ old('injected_status', $aboutInstitute->injected_status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- FAQ Repeater --}}
                    <div class="card-body border-top">
                        <h5 class="card-title mb-3">FAQs</h5>

                        <div class="repeater-faqs">
                            <div data-repeater-list="faqs">
                                @if ($aboutInstitute->faqs->count() > 0)
                                    @foreach ($aboutInstitute->faqs as $faq)
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $faq->id }}">

                                            <div class="col">
                                                <label class="form-label">
                                                    <span class="badge bg-secondary me-1 serial-badge">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                    FAQ Title <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="faq_title" class="form-control mb-2"
                                                    placeholder="Enter FAQ title"
                                                    value="{{ $faq->faq_title }}">

                                                <label class="form-label">Short Description</label>
                                                <textarea name="faq_short_description" class="form-control" rows="2"
                                                    placeholder="Enter short description">{{ $faq->faq_short_description }}</textarea>
                                            </div>

                                            <div class="col-auto mt-4">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Empty template item when no FAQs exist --}}
                                    <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                        <input type="hidden" name="id" value="">

                                        <div class="col">
                                            <label class="form-label">
                                                <span class="badge bg-secondary me-1 serial-badge">01</span>
                                                FAQ Title <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="faq_title" class="form-control mb-2"
                                                placeholder="Enter FAQ title" value="">

                                            <label class="form-label">Short Description</label>
                                            <textarea name="faq_short_description" class="form-control" rows="2"
                                                placeholder="Enter short description"></textarea>
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
                                <i class="fa-solid fa-plus"></i> Add FAQ
                            </button>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update About Institute
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function reindexFaqs() {
            $('.repeater-faqs [data-repeater-item]').each(function(i) {
                $(this).find('.serial-badge').text(String(i + 1).padStart(2, '0'));
            });
        }

        $(document).ready(function() {
            reindexFaqs();

            $('.repeater-faqs').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('textarea').val('');
                    $(this).find('input[type="hidden"]').val('');
                    reindexFaqs();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this FAQ?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexFaqs();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });
        });
    </script>
@endpush
