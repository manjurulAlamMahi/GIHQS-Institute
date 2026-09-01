@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Sliders</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Slider Create</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Slider Create</h4>
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-sm btn-primary">Back</a>
                </div>

                <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="row gy-4">

                            {{-- Position --}}
                            <div class="col-xxl-12 col-md-12">
                                <label for="page_position" class="form-label">Page Position</label>
                                <select class="form-select @error('page_position') is-invalid @enderror" name="page_position" id="page_position">
                                    <option value="" disabled selected>Choose Position</option>
                                    <option value="home" {{ old('page_position') == 'home' ? 'selected' : '' }}>Home </option>
                                    <option value="about" {{ old('page_position') == 'about' ? 'selected' : '' }}>About</option>
                                    <option value="contact" {{ old('page_position') == 'contact' ? 'selected' : '' }}>Contact</option>
                                </select>
                                @error('page_position')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Image Field --}}
                            <div class="col-xxl-12 col-md-12">
                                <div>
                                    <label for="image" class="form-label">Slider Image</label>
                                    <input type="file" name="image" id="image" class="form-control dropify" data-allowed-file-extensions="jpg jpeg png gif webp">
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Status Field --}}
                            <div class="col-xxl-12 col-md-12">
                                <label class="form-label" for="statusSelect">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect">
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Published</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Unpublished</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-xxl-12 col-md-12">
                                <button type="submit" class="btn btn-primary">Save Slider</button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
