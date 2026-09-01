@extends('backend.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Apply Accreditation Hero</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item">Accreditation</li>
                        <li class="breadcrumb-item active">Apply Accreditation Hero</li>
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
            <form action="{{ route('admin.accreditation-apply-hero.update', $hero->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Card 1: Accreditation Hero Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Accreditation Hero Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-md-4">
                                <label for="title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="title1" id="title1"
                                    class="form-control @error('title1') is-invalid @enderror"
                                    value="{{ old('title1', $hero->title1) }}" placeholder="Enter Title 1" required>
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2"
                                    class="form-control @error('title2') is-invalid @enderror"
                                    value="{{ old('title2', $hero->title2) }}" placeholder="Enter Title 2">
                            </div>

                            <div class="col-md-4">
                                <label for="tagline" class="form-label">Tagline</label>
                                <input type="text" name="tagline" id="tagline"
                                    class="form-control @error('tagline') is-invalid @enderror"
                                    value="{{ old('tagline', $hero->tagline) }}" placeholder="Enter Tagline">
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="3" placeholder="Enter Description">{{ old('description', $hero->description) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" id="note"
                                    class="form-control @error('note') is-invalid @enderror"
                                    rows="2" placeholder="Enter Note or Notice">{{ old('note', $hero->note) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Eligibility Snapshot Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Eligibility Snapshot Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            <div class="col-md-6">
                                <label for="snapshot_title" class="form-label">Title</label>
                                <input type="text" name="snapshot_title" id="snapshot_title"
                                    class="form-control"
                                    value="{{ old('snapshot_title', $snapshot->title) }}" placeholder="Enter Section Title">
                            </div>

                            <div class="col-md-12">
                                <label for="snapshot_description" class="form-label">Description</label>
                                <textarea name="snapshot_description" id="snapshot_description"
                                    class="form-control" rows="3"
                                    placeholder="Enter Section Description">{{ old('snapshot_description', $snapshot->description) }}</textarea>
                            </div>
                        </div>

                        {{-- Snapshot Features Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3 fs-15 text-muted">Eligibility Key Points</h5>
                            <div class="repeater-snapshot">
                                <div data-repeater-list="features">
                                    @if ($snapshot->features->count() > 0)
                                        @foreach ($snapshot->features as $item)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <div class="col-md-4">
                                                    <label class="form-label">Key Point <span class="text-danger">*</span></label>
                                                    <input type="text" name="keypoints" class="form-control"
                                                        placeholder="e.g. Licensed Facility" value="{{ $item->keypoints }}">
                                                </div>
                                                <div class="col-md-7">
                                                    <label class="form-label">Details</label>
                                                    <textarea name="details" class="form-control" rows="1"
                                                        placeholder="Enter details...">{{ $item->details }}</textarea>
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
                                            <div class="col-md-4">
                                                <label class="form-label">Key Point <span class="text-danger">*</span></label>
                                                <input type="text" name="keypoints" class="form-control"
                                                    placeholder="e.g. Licensed Facility">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Details</label>
                                                <textarea name="details" class="form-control" rows="2"
                                                    placeholder="Enter details..."></textarea>
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
                                    <i class="ri-add-line"></i> Add Key Point
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4 py-2">
                        <i class="ri-save-line me-1"></i> Update Apply Accreditation Hero
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('.repeater-snapshot').repeater({
        show: function () {
            $(this).slideDown();
            $(this).find('input[type="text"]').val('');
            $(this).find('textarea').val('');
            $(this).find('input[type="hidden"]').val('');
        },
        hide: function (deleteElement) {
            if (confirm('Are you sure you want to delete this key point?')) {
                $(this).slideUp(deleteElement);
            }
        },
        isFirstItemUndeletable: false,
        initEmpty: false
    });
});
</script>
@endpush
