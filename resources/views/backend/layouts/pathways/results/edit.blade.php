@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Pathway Results</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Result Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Result Edit</h4>
                    <a href="{{ route('admin.pathway-results.index') }}" class="btn btn-sm btn-primary">Back</a>
                </div>

                <form action="{{ route('admin.pathway-results.update', $result->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row gy-4">

                            {{-- Title --}}
                            <div class="col-xxl-12 col-md-12">
                                <div>
                                    <label for="title" class="form-label">Result Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Enter Title" value="{{ old('title', $result->title) }}" required>
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="col-xxl-12 col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter Description">{{ old('description', $result->description) }}</textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Badges --}}
                            <div class="col-xxl-12 col-md-12">
                                <div>
                                    <label for="badges" class="form-label">Badges / Tags (comma separated)</label>
                                    <input type="text" name="badges" id="badges" class="form-control @error('badges') is-invalid @enderror" placeholder="e.g. Apply, Accreditation, Institutional" value="{{ old('badges', $badgesString) }}">
                                    <small class="text-muted">Separate multiple tags with a comma (,)</small>
                                    @error('badges')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Info Box Text --}}
                            <div class="col-xxl-12 col-md-12">
                                <label for="info_box_text" class="form-label">Info Box Highlighted Text</label>
                                <textarea name="info_box_text" id="info_box_text" class="form-control @error('info_box_text') is-invalid @enderror" rows="2" placeholder="e.g. This route is appropriate when...">{{ old('info_box_text', $result->info_box_text) }}</textarea>
                                @error('info_box_text')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Primary Button --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="primary_button_text" class="form-label">Primary Button Text <span class="text-danger">*</span></label>
                                    <input type="text" name="primary_button_text" id="primary_button_text" class="form-control @error('primary_button_text') is-invalid @enderror" placeholder="e.g. Apply for Accreditation" value="{{ old('primary_button_text', $result->primary_button_text) }}" required>
                                    @error('primary_button_text')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="primary_button_url" class="form-label">Primary Button URL</label>
                                    <input type="text" name="primary_button_url" id="primary_button_url" class="form-control @error('primary_button_url') is-invalid @enderror" placeholder="e.g. /accreditation/apply" value="{{ old('primary_button_url', $result->primary_button_url) }}">
                                    @error('primary_button_url')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Secondary Button --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="secondary_button_text" class="form-label">Secondary Button Text <span class="text-danger">*</span></label>
                                    <input type="text" name="secondary_button_text" id="secondary_button_text" class="form-control @error('secondary_button_text') is-invalid @enderror" placeholder="e.g. View Accreditation" value="{{ old('secondary_button_text', $result->secondary_button_text) }}" required>
                                    @error('secondary_button_text')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="secondary_button_url" class="form-label">Secondary Button URL</label>
                                    <input type="text" name="secondary_button_url" id="secondary_button_url" class="form-control @error('secondary_button_url') is-invalid @enderror" placeholder="e.g. /accreditation/overview" value="{{ old('secondary_button_url', $result->secondary_button_url) }}">
                                    @error('secondary_button_url')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Status Field --}}
                            <div class="col-xxl-12 col-md-12">
                                <label class="form-label" for="statusSelect">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect">
                                    <option value="1" {{ old('status', $result->status) == 1 ? 'selected' : '' }}>Published</option>
                                    <option value="0" {{ old('status', $result->status) == 0 ? 'selected' : '' }}>Unpublished</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-xxl-12 col-md-12">
                                <button type="submit" class="btn btn-primary">Update Result</button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
