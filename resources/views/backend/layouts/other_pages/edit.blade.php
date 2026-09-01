@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Content Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Other Pages Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <form action="{{ route('admin.other-pages.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @php
                    $slugs = [
                        'privacy-policy' => 'Privacy Policy',
                        'terms-of-use' => 'Terms of Use',
                        'terms-purchase' => 'Terms & Conditions of Purchase',
                        'refund-policy' => 'Refund Policy',
                        'disclaimer' => 'Disclaimer'
                    ];
                @endphp

                @foreach ($slugs as $slug => $defaultTitle)
                    @php
                        $prefix = str_replace('-', '_', $slug);
                        $pageData = $pages[$slug] ?? null;
                    @endphp

                    <div class="card mb-4">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">{{ $pageData ? $pageData->title : $defaultTitle }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row gy-4">
                                {{-- Title --}}
                                <div class="col-md-12">
                                    <label for="{{ $prefix }}_title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="{{ $prefix }}_title" id="{{ $prefix }}_title"
                                        class="form-control @error($prefix . '_title') is-invalid @enderror"
                                        value="{{ old($prefix . '_title', $pageData->title ?? $defaultTitle) }}"
                                        placeholder="Enter title">
                                    @error($prefix . '_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Content File --}}
                                <div class="col-md-6">
                                    <label for="{{ $prefix }}_file" class="form-label">{{ $pageData->title ?? $defaultTitle }} File (.html)</label>
                                    <input type="file" name="{{ $prefix }}_file" id="{{ $prefix }}_file" 
                                        class="form-control @error($prefix . '_file') is-invalid @enderror" accept=".html,.txt">
                                    <small class="text-muted">Only .html or .txt files are accepted.</small>
                                    @error($prefix . '_file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    @if ($pageData && $pageData->content_file)
                                        <div class="mt-2 d-flex align-items-center gap-3">
                                            <a href="{{ asset($pageData->content_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                                <i class="fa-solid fa-file-code me-1"></i> View Current File ({{ basename($pageData->content_file) }})
                                            </a>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remove_{{ $prefix }}_file" id="remove_{{ $prefix }}_file" value="1">
                                                <label class="form-check-label text-danger fw-semibold" for="remove_{{ $prefix }}_file">
                                                    Remove Current File
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Injected Status --}}
                                <div class="col-md-6">
                                    <label for="{{ $prefix }}_injected_status" class="form-label">Injected Status</label>
                                    <select class="form-select" name="{{ $prefix }}_injected_status" id="{{ $prefix }}_injected_status">
                                        <option value="0" {{ old($prefix . '_injected_status', $pageData->injected_status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                        <option value="1" {{ old($prefix . '_injected_status', $pageData->injected_status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                    </select>
                                    @error($prefix . '_injected_status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="card">
                    <div class="card-body text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update All Pages Settings
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
