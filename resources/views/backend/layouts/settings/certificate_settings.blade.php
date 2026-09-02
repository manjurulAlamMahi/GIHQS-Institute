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
                                    <div class="form-text">Printed above the title on every certificate.</div>
                                </div>
                            </div>

                            {{-- Chairman Title --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="chairman_title" class="form-label">Chairman Title / Position</label>
                                    <input type="text" name="chairman_title" id="chairman_title" class="form-control"
                                        placeholder="{{ \App\Models\CertificateSetting::DEFAULT_CHAIRMAN_TITLE }}"
                                        value="{{ old('chairman_title', $setting->chairman_title ?? '') }}">
                                    @error('chairman_title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="form-text">
                                        Leave blank to use "{{ \App\Models\CertificateSetting::DEFAULT_CHAIRMAN_TITLE }}".
                                    </div>
                                </div>
                            </div>

                            {{-- Show Chairman section --}}
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="show_chairman" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        name="show_chairman" id="show_chairman" value="1"
                                        @checked(old('show_chairman', $setting->show_chairman ?? true))>
                                    <label class="form-check-label" for="show_chairman">
                                        <strong>Show the Chairman signature section</strong>
                                    </label>
                                    <div class="form-text">
                                        Turning this off removes the whole section &mdash; signature, line,
                                        name, title and caption. The seal stays centred and the other
                                        signature keeps its position.
                                    </div>
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
                                    <div class="form-text">Printed above the title on every certificate.</div>
                                </div>
                            </div>

                            {{-- Executive Director Title --}}
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="executive_director_title" class="form-label">Executive Director Title / Position</label>
                                    <input type="text" name="executive_director_title" id="executive_director_title" class="form-control"
                                        placeholder="{{ \App\Models\CertificateSetting::DEFAULT_EXECUTIVE_DIRECTOR_TITLE }}"
                                        value="{{ old('executive_director_title', $setting->executive_director_title ?? '') }}">
                                    @error('executive_director_title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="form-text">
                                        Leave blank to use "{{ \App\Models\CertificateSetting::DEFAULT_EXECUTIVE_DIRECTOR_TITLE }}".
                                    </div>
                                </div>
                            </div>

                            {{-- Show Executive Director section --}}
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="show_executive_director" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        name="show_executive_director" id="show_executive_director" value="1"
                                        @checked(old('show_executive_director', $setting->show_executive_director ?? true))>
                                    <label class="form-check-label" for="show_executive_director">
                                        <strong>Show the Executive Director signature section</strong>
                                    </label>
                                    <div class="form-text">
                                        Turning this off removes the whole section &mdash; signature, line,
                                        name, title and caption.
                                    </div>
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
