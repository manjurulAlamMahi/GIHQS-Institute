@extends('backend.app')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between page-title-box">
                <h4 class="mb-0">HTML Documents — {{ $catalogue->title }}</h4>
                <a href="{{ route('admin.catalogues.edit', $catalogue->id) }}" class="btn btn-light btn-sm">
                    <i class="ri-arrow-left-line align-bottom me-1"></i> Back to catalogue
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="alert alert-warning d-flex align-items-start" role="alert">
        <i class="ri-shield-keyhole-line fs-18 me-2"></i>
        <div>
            <strong>Uploaded HTML runs its own JavaScript.</strong>
            Documents are served from the API domain and sealed inside an iframe, so they cannot
            read anything belonging to the dashboard — but only upload files from a source you trust.
            Files are stored exactly as uploaded and are never rewritten.
        </div>
    </div>

    <div class="row">
        {{-- Upload --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Upload a document</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.catalogue-html-resources.store', $catalogue->id) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="e.g. Healthcare RCA Toolkit" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="kind" class="form-select @error('kind') is-invalid @enderror" required>
                                @foreach ($kinds as $kind)
                                    <option value="{{ $kind }}" @selected(old('kind') === $kind)>
                                        {{ ucwords(str_replace('_', ' ', $kind)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kind') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">A label for grouping only. It does not change how the document renders.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">HTML file <span class="text-danger">*</span></label>
                            <input type="file" name="file" accept=".html,.txt"
                                   class="form-control @error('file') is-invalid @enderror" required>
                            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Standalone .html file, up to 10 MB.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Access key <span class="text-muted small">(optional)</span></label>
                            <input type="text" name="access_key" value="{{ old('access_key') }}"
                                   class="form-control @error('access_key') is-invalid @enderror"
                                   placeholder="e.g. RCA-TOOLKIT-2026">
                            @error('access_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">
                                Leave blank and anyone who owns the course can open it. Set a key and users
                                must enter it once before they can open the document.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Access lasts <span class="text-muted small">(days)</span></label>
                            <input type="number" name="license_validity_days" min="1" max="3650"
                                   value="{{ old('license_validity_days') }}"
                                   class="form-control @error('license_validity_days') is-invalid @enderror"
                                   placeholder="blank = never expires">
                            @error('license_validity_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Order</label>
                            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"
                                   class="form-control">
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_public" value="1"
                                   id="is_public" @checked(old('is_public'))>
                            <label class="form-check-label" for="is_public">
                                Public — readable without buying the course
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-upload-2-line align-bottom me-1"></i> Upload
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Existing --}}
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Documents ({{ $resources->count() }})</h5></div>
                <div class="card-body">
                    @forelse ($resources as $resource)
                        <div class="border rounded p-3 mb-3">
                            <form action="{{ route('admin.catalogue-html-resources.update', $resource->id) }}"
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label mb-1">Title</label>
                                        <input type="text" name="title" value="{{ $resource->title }}"
                                               class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label mb-1">Type</label>
                                        <select name="kind" class="form-select form-select-sm">
                                            @foreach ($kinds as $kind)
                                                <option value="{{ $kind }}" @selected($resource->kind === $kind)>
                                                    {{ ucwords(str_replace('_', ' ', $kind)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label mb-1">Order</label>
                                        <input type="number" name="sort_order" min="0"
                                               value="{{ $resource->sort_order }}"
                                               class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_public" value="1"
                                                   id="public-{{ $resource->id }}" @checked($resource->is_public)>
                                            <label class="form-check-label small" for="public-{{ $resource->id }}">Public</label>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label mb-1">Access key <span class="text-muted small">(blank = open)</span></label>
                                        <input type="text" name="access_key" value="{{ $resource->access_key }}"
                                               class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label mb-1">Access lasts (days)</label>
                                        <input type="number" name="license_validity_days" min="1" max="3650"
                                               value="{{ $resource->license_validity_days }}"
                                               class="form-control form-control-sm" placeholder="never expires">
                                    </div>
                                    <div class="col-md-3"></div>

                                    <div class="col-md-9">
                                        <label class="form-label mb-1">Replace file <span class="text-muted small">(optional)</span></label>
                                        <input type="file" name="file" accept=".html,.txt" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-sm btn-primary flex-fill">Save</button>
                                    </div>
                                </div>
                            </form>

                            {{-- Who has redeemed this document's key --}}
                            @if ($resource->requiresLicense())
                                <div class="mt-3 pt-3 border-top">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <strong class="small">Access granted to ({{ $resource->licenses->count() }})</strong>
                                        <span class="text-muted" style="font-size:11px">
                                            A shared key can be passed on, so this lists who redeemed it.
                                        </span>
                                    </div>

                                    @forelse ($resource->licenses as $license)
                                        @php
                                            $expired = $license->expires_at && $license->expires_at->isPast();
                                        @endphp
                                        <div class="d-flex align-items-center justify-content-between py-1 small">
                                            <div>
                                                {{ $license->user?->email ?? 'deleted user' }}
                                                @if ($license->revoked_at)
                                                    <span class="badge bg-danger-subtle text-danger ms-1">revoked</span>
                                                @elseif ($expired)
                                                    <span class="badge bg-warning-subtle text-warning ms-1">expired</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success ms-1">active</span>
                                                @endif
                                                <span class="text-muted ms-1">
                                                    redeemed {{ $license->granted_at?->format('d M Y') }}
                                                    @if ($license->expires_at)
                                                        &middot; expires {{ $license->expires_at->format('d M Y') }}
                                                    @endif
                                                </span>
                                            </div>
                                            <form method="POST"
                                                  action="{{ $license->revoked_at
                                                      ? route('admin.html-resource-licenses.restore', $license->id)
                                                      : route('admin.html-resource-licenses.revoke', $license->id) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm {{ $license->revoked_at ? 'btn-ghost-success' : 'btn-ghost-danger' }}">
                                                    {{ $license->revoked_at ? 'Restore' : 'Revoke' }}
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <div class="text-muted small">Nobody has redeemed this key yet.</div>
                                    @endforelse
                                </div>
                            @endif

                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="small text-muted">
                                    <i class="ri-lock-line align-bottom me-1"></i>
                                    @if ($resource->is_public)
                                        Public — no sign in needed
                                    @elseif ($resource->requiresLicense())
                                        Course access + access key
                                    @else
                                        Course access only
                                    @endif
                                </span>
                                <form action="{{ route('admin.catalogue-html-resources.destroy', $resource->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete “{{ $resource->title }}”? The uploaded file is removed too.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost-danger">
                                        <i class="ri-delete-bin-line align-bottom"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            No HTML documents yet. Upload one using the form on the left.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
