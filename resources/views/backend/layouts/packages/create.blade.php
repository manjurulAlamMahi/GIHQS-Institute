@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Packages</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.packages.index') }}">Packages</a></li>
                        <li class="breadcrumb-item active">Create Package</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Create Package</h4>
                    <a href="{{ route('admin.packages.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <form action="{{ route('admin.packages.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <div class="row gy-4">

                            {{-- Package Name --}}
                            <div class="col-md-8">
                                <label for="name" class="form-label">Package Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Enter package name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    {{-- Features Repeater --}}
                    <div class="card-body border-top">
                        <h5 class="card-title mb-3">Package Features</h5>

                        <div class="repeater-features">
                            <div data-repeater-list="features">
                                {{-- Default empty item --}}
                                <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                    <input type="hidden" name="id" value="">

                                    <div class="col">
                                        <label class="form-label">
                                            <span class="badge bg-secondary me-1 serial-badge">01</span>
                                            Feature Description <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="description" class="form-control" rows="2" placeholder="Enter feature description"></textarea>
                                    </div>

                                    <div class="col-auto mt-4">
                                        <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Feature
                            </button>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Package
                        </button>
                    </div>
                </form>
            </div>
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
