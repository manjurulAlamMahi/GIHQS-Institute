@extends('backend.app')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Certificate Settings</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Update Certificate Settings</h4>
                </div><!-- end card header -->

                <form action="{{ route('admin.certificate-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row gy-4">



                            {{-- Chairman Name --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="chairman_name" class="form-label">Chairman Name</label>
                                    <input type="text" name="chairman_name" id="chairman_name" class="form-control" placeholder="Enter chairman's name"
                                        value="{{ old('chairman_name', $setting->chairman_name ?? '') }}">
                                    @error('chairman_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Executive Director Name --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="executive_director_name" class="form-label">Executive Director Name</label>
                                    <input type="text" name="executive_director_name" id="executive_director_name" class="form-control" placeholder="Enter executive director's name"
                                        value="{{ old('executive_director_name', $setting->executive_director_name ?? '') }}">
                                    @error('executive_director_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Chairman Signature --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="chairman_signature" class="form-label">Chairman Signature Image</label>
                                    <input type="file" name="chairman_signature" id="chairman_signature" class="form-control dropify" data-allowed-file-extensions="jpg jpeg png svg webp"
                                        data-default-file="{{ $setting && $setting->chairman_signature ? asset($setting->chairman_signature) : '' }}">
                                    <input type="hidden" name="remove_chairman_signature" id="remove_chairman_signature_hidden" value="0">
                                    @error('chairman_signature')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Executive Director Signature --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="executive_director_signature" class="form-label">Executive Director Signature Image</label>
                                    <input type="file" name="executive_director_signature" id="executive_director_signature" class="form-control dropify" data-allowed-file-extensions="jpg jpeg png svg webp"
                                        data-default-file="{{ $setting && $setting->executive_director_signature ? asset($setting->executive_director_signature) : '' }}">
                                    <input type="hidden" name="remove_executive_director_signature" id="remove_executive_director_signature_hidden" value="0">
                                    @error('executive_director_signature')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="col-xxl-12 col-md-12 mt-4">
                                <button type="submit" class="btn btn-primary">Update Settings</button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection

{{-- Push the script --}}
@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Dropify
            $('.dropify').dropify({
                messages: {
                    'default': 'Drag or click',
                    'replace': 'Drag to replace',
                    'remove': 'Remove',
                    'error': 'Something went wrong.'
                }
            });

            // Detect Dropify remove
            $('.dropify').on('dropify.beforeClear', function(event, element) {
                let name = $(this).attr('name'); // certificate_template, chairman_signature, executive_director_signature
                $('#remove_' + name + '_hidden').val(1);
            });
        });
    </script>
@endpush
