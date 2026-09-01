@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Membership Packages</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.membership-packages.index') }}">Membership Packages</a></li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Create Membership Package</h4>
                    <a href="{{ route('admin.membership-packages.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <form action="{{ route('admin.membership-packages.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <div class="row gy-4">

                            {{-- Package Name --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label">Package Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Enter package name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Package Title --}}
                            <div class="col-md-6">
                                <label for="title" class="form-label">Package Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title') }}" placeholder="Enter package title">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Price --}}
                            <div class="col-md-6">
                                <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price') }}" placeholder="Enter price">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Discount Percentage --}}
                            <div class="col-md-6">
                                <label for="discount_percentage" class="form-label">Discount Percentage (%) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="100" name="discount_percentage" id="discount_percentage" class="form-control @error('discount_percentage') is-invalid @enderror"
                                    value="{{ old('discount_percentage', '0.00') }}" placeholder="Enter discount percentage (e.g. 30.00)">
                                @error('discount_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label for="package_status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="package_status">
                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Validity Days --}}
                            <div class="col-md-6">
                                <label for="validity_days" class="form-label">Validity Period (Days)</label>
                                <input type="number" name="validity_days" id="validity_days" class="form-control @error('validity_days') is-invalid @enderror"
                                    value="{{ old('validity_days') }}" placeholder="Enter validity days (e.g. 30)">
                                @error('validity_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Exam Attempt Limit --}}
                            <div class="col-md-6">
                                <label for="exam_attempt_limit" class="form-label">Exam Attempt Limit <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="exam_attempt_limit" id="exam_attempt_limit" class="form-control @error('exam_attempt_limit') is-invalid @enderror"
                                    value="{{ old('exam_attempt_limit', '1') }}" placeholder="Enter maximum exam attempts (e.g. 3)">
                                @error('exam_attempt_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            {{-- Short Description --}}
                            <div class="col-md-12">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea name="short_description" id="short_description" class="form-control @error('short_description') is-invalid @enderror"
                                    rows="3" placeholder="Enter short description">{{ old('short_description') }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                        <div class="row g-2">
                                            <div class="col-md-8">
                                                <label class="form-label">
                                                    <span class="badge bg-secondary me-1 serial-badge">01</span>
                                                    Feature Description <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="description" class="form-control" rows="1" placeholder="Enter feature description"></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Badge</label>
                                                <input type="text" name="badge" class="form-control" placeholder="e.g. New, Popular">
                                            </div>
                                        </div>

                                        <label class="form-label mt-2">Note</label>
                                        <textarea name="note" class="form-control" rows="1" placeholder="Enter note (optional)"></textarea>
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
                    $(this).find('input').val('');
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
