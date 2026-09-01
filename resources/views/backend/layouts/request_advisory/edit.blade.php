@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Request Advisory Consultation</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item">Advisory</li>
                        <li class="breadcrumb-item active">Request Advisory Consultation</li>
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
            <form action="{{ route('admin.request-advisory-consultation.update', $consultation->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Card 1: Section Details --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Request Advisory Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Title 1 --}}
                            <div class="col-md-4">
                                <label for="title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="title1" id="title1" class="form-control @error('title1') is-invalid @enderror"
                                    value="{{ old('title1', $consultation->title1) }}" placeholder="Enter Title 1" required>
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title 2 --}}
                            <div class="col-md-4">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2" class="form-control @error('title2') is-invalid @enderror"
                                    value="{{ old('title2', $consultation->title2) }}" placeholder="Enter Title 2">
                                @error('title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-md-4">
                                <label for="tagline" class="form-label">Tagline</label>
                                <input type="text" name="tagline" id="tagline" class="form-control @error('tagline') is-invalid @enderror"
                                    value="{{ old('tagline', $consultation->tagline) }}" placeholder="Enter Tagline">
                                @error('tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter Description">{{ old('description', $consultation->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Consultation
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
