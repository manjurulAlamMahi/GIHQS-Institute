@extends('backend.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Accreditation Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item">Accreditation</li>
                        <li class="breadcrumb-item active">Eligibility, Process, Domain, Insights</li>
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
            <form action="{{ route('admin.accreditation-details.update', $eligibility->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Card 1: Section 1 - Eligibility --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 1: Eligibility</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            <div class="col-md-6">
                                <label for="eligibility_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="eligibility_title1" id="eligibility_title1" class="form-control"
                                    value="{{ old('eligibility_title1', $eligibility->title1) }}" placeholder="Enter Title 1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="eligibility_title2" class="form-label">Title 2</label>
                                <input type="text" name="eligibility_title2" id="eligibility_title2" class="form-control"
                                    value="{{ old('eligibility_title2', $eligibility->title2) }}" placeholder="Enter Title 2">
                            </div>
                            <div class="col-md-12">
                                <label for="eligibility_description" class="form-label">Description</label>
                                <textarea name="eligibility_description" id="eligibility_description" class="form-control" rows="3" placeholder="Enter Description">{{ old('eligibility_description', $eligibility->description) }}</textarea>
                            </div>
                        </div>

                        {{-- Eligibility Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3 fs-15 text-muted">Eligibility Features</h5>
                            <div class="repeater-eligibility">
                                <div data-repeater-list="eligibility_features">
                                    @if ($eligibility->features->count() > 0)
                                        @foreach ($eligibility->features as $item)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <div class="col-md-5">
                                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" required placeholder="Enter feature title" value="{{ $item->title }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="1" placeholder="Enter description">{{ $item->description }}</textarea>
                                                </div>
                                                <div class="col-md-1 text-end mt-4">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                        <i class="ri-delete-bin-line"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">
                                            <div class="col-md-5">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" required placeholder="Enter feature title">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Enter description"></textarea>
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
                                    <i class="ri-add-line"></i> Add Feature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Section 2 - Process --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 2: Process</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            <div class="col-md-6">
                                <label for="process_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="process_title1" id="process_title1" class="form-control"
                                    value="{{ old('process_title1', $process->title1) }}" placeholder="Enter Title 1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="process_title2" class="form-label">Title 2</label>
                                <input type="text" name="process_title2" id="process_title2" class="form-control"
                                    value="{{ old('process_title2', $process->title2) }}" placeholder="Enter Title 2">
                            </div>
                            <div class="col-md-12">
                                <label for="process_description" class="form-label">Description</label>
                                <textarea name="process_description" id="process_description" class="form-control" rows="3" placeholder="Enter Description">{{ old('process_description', $process->description) }}</textarea>
                            </div>
                        </div>

                        {{-- Process Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3 fs-15 text-muted">Process Features</h5>
                            <div class="repeater-process">
                                <div data-repeater-list="process_features">
                                    @if ($process->features->count() > 0)
                                        @foreach ($process->features as $item)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <div class="col-md-2">
                                                    <label class="form-label">Serial</label>
                                                    <input type="text" name="serial" class="form-control" placeholder="e.g. 01" value="{{ $item->serial }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" required placeholder="Enter title" value="{{ $item->title }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Subtitle</label>
                                                    <input type="text" name="subtitle" class="form-control" placeholder="Enter subtitle" value="{{ $item->subtitle }}">
                                                </div>
                                                <div class="col-md-12 mt-2">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="2" placeholder="Enter description">{{ $item->description }}</textarea>
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
                                                <label class="form-label">Serial</label>
                                                <input type="text" name="serial" class="form-control" placeholder="e.g. 01">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" required placeholder="Enter title">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Subtitle</label>
                                                <input type="text" name="subtitle" class="form-control" placeholder="Enter subtitle">
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Enter description"></textarea>
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
                                    <i class="ri-add-line"></i> Add Process Feature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Section 3 - Domain --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 3: Domain</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            <div class="col-md-6">
                                <label for="domain_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="domain_title1" id="domain_title1" class="form-control"
                                    value="{{ old('domain_title1', $domain->title1) }}" placeholder="Enter Title 1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="domain_title2" class="form-label">Title 2</label>
                                <input type="text" name="domain_title2" id="domain_title2" class="form-control"
                                    value="{{ old('domain_title2', $domain->title2) }}" placeholder="Enter Title 2">
                            </div>
                            <div class="col-md-12">
                                <label for="domain_description" class="form-label">Description</label>
                                <textarea name="domain_description" id="domain_description" class="form-control" rows="3" placeholder="Enter Description">{{ old('domain_description', $domain->description) }}</textarea>
                            </div>
                        </div>

                        {{-- Domain Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3 fs-15 text-muted">Domain Features</h5>
                            <div class="repeater-domain">
                                <div data-repeater-list="domain_features">
                                    @if ($domain->features->count() > 0)
                                        @foreach ($domain->features as $item)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <div class="col-md-1">
                                                    <label class="form-label">Domain Serial</label>
                                                    <input type="text" name="domain_serial" class="form-control" placeholder="e.g. D01" value="{{ $item->domain_serial }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" required placeholder="Enter domain title" value="{{ $item->title }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="2" placeholder="Enter description">{{ $item->description }}</textarea>
                                                </div>
                                                <div class="col-md-1 text-end mt-4">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                        <i class="ri-delete-bin-line"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">
                                            <div class="col-md-2">
                                                <label class="form-label">Domain Serial</label>
                                                <input type="text" name="domain_serial" class="form-control" placeholder="e.g. D01">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" required placeholder="Enter domain title">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Enter description"></textarea>
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
                                    <i class="ri-add-line"></i> Add Domain Feature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Section 4 - Insights --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Section 4: Insights</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            <div class="col-md-6">
                                <label for="insight_title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="insight_title1" id="insight_title1" class="form-control"
                                    value="{{ old('insight_title1', $insight->title1) }}" placeholder="Enter Title 1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="insight_title2" class="form-label">Title 2</label>
                                <input type="text" name="insight_title2" id="insight_title2" class="form-control"
                                    value="{{ old('insight_title2', $insight->title2) }}" placeholder="Enter Title 2">
                            </div>
                            <div class="col-md-12">
                                <label for="insight_description" class="form-label">Description</label>
                                <textarea name="insight_description" id="insight_description" class="form-control" rows="3" placeholder="Enter Description">{{ old('insight_description', $insight->description) }}</textarea>
                            </div>
                        </div>

                        {{-- Insights Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3 fs-15 text-muted">Insights Features</h5>
                            <div class="repeater-insight">
                                <div data-repeater-list="insight_features">
                                    @if ($insight->features->count() > 0)
                                        @foreach ($insight->features as $item)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <div class="col-md-3">
                                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" required placeholder="Enter title" value="{{ $item->title }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Tagline</label>
                                                    <input type="text" name="tagline" class="form-control" placeholder="Enter tagline" value="{{ $item->tagline }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="3" placeholder="Enter description">{{ $item->description }}</textarea>
                                                </div>
                                                <div class="col-md-1 text-end mt-4">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm w-100">
                                                        <i class="ri-delete-bin-line"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">
                                            <div class="col-md-3">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" required placeholder="Enter title">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tagline</label>
                                                <input type="text" name="tagline" class="form-control" placeholder="Enter tagline">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="2" placeholder="Enter description"></textarea>
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
                                    <i class="ri-add-line"></i> Add Insight Feature
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4 py-2">
                        <i class="ri-save-line me-1"></i> Update Accreditation Details
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // 1. Eligibility Repeater
            $('.repeater-eligibility').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('textarea').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this feature?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            // 2. Process Repeater
            $('.repeater-process').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('textarea').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this process feature?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            // 3. Domain Repeater
            $('.repeater-domain').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('textarea').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this domain feature?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            // 4. Insight Repeater
            $('.repeater-insight').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('textarea').val('');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this insight?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });
        });
    </script>
@endpush
