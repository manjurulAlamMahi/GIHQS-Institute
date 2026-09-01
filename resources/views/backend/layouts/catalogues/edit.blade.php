@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Development Catalogue</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.catalogues.index') }}">Catalogue</a></li>
                        <li class="breadcrumb-item active">Edit Item</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Edit Catalogue Item — {{ $item->title }}</h4>
                    <a href="{{ route('admin.catalogues.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <form action="{{ route('admin.catalogues.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row gy-4">

                            {{-- Title --}}
                            <div class="col-md-12">
                                <label for="title" class="form-label">Item Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $item->title) }}" placeholder="Enter catalogue item title">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Short Title --}}
                            <div class="col-md-12">
                                <label for="short_title" class="form-label">Short Title</label>
                                <input type="text" name="short_title" id="short_title" class="form-control @error('short_title') is-invalid @enderror"
                                    value="{{ old('short_title', $item->short_title) }}" placeholder="Enter short title (e.g. AIHQSP)">
                                @error('short_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Catalogue Type --}}
                            <div class="col-md-4">
                                <label for="catalogue_type" class="form-label">Catalogue Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('catalogue_type') is-invalid @enderror" name="catalogue_type" id="catalogue_type">
                                    <option value="paid" {{ old('catalogue_type', $item->catalogue_type) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="free" {{ old('catalogue_type', $item->catalogue_type) == 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="members only" {{ old('catalogue_type', $item->catalogue_type) == 'members only' ? 'selected' : '' }}>Members Only</option>
                                </select>
                                @error('catalogue_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Service Type --}}
                            <div class="col-md-4">
                                <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('service_type') is-invalid @enderror" name="service_type" id="service_type">
                                    <option value="Certification" {{ old('service_type', $item->service_type) == 'Certification' ? 'selected' : '' }}>Certification</option>
                                    <option value="Course" {{ old('service_type', $item->service_type) == 'Course' ? 'selected' : '' }}>Course</option>
                                    <option value="Webinar" {{ old('service_type', $item->service_type) == 'Webinar' ? 'selected' : '' }}>Webinar</option>
                                    <option value="Module" {{ old('service_type', $item->service_type) == 'Module' ? 'selected' : '' }}>Module</option>
                                    <option value="Toolkit" {{ old('service_type', $item->service_type) == 'Toolkit' ? 'selected' : '' }}>Toolkit</option>
                                    <option value="Workshop" {{ old('service_type', $item->service_type) == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                                </select>
                                @error('service_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-4">
                                <label for="item_status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="item_status">
                                    <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $item->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Webinar/Workshop Date and Time Fields --}}
                            <div class="col-md-12 webinar-workshop-only-field" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="fixed_date" class="form-label">Fixed Date <span class="text-danger">*</span></label>
                                        <input type="date" name="fixed_date" id="fixed_date" class="form-control @error('fixed_date') is-invalid @enderror"
                                            value="{{ old('fixed_date', $item->fixed_date) }}">
                                        @error('fixed_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                            value="{{ old('start_time', $item->start_time ? substr($item->start_time, 0, 5) : '') }}">
                                        @error('start_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                        <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                            value="{{ old('end_time', $item->end_time ? substr($item->end_time, 0, 5) : '') }}">
                                        @error('end_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Regular Price --}}
                            <div class="col-md-4">
                                <label for="price_regular" class="form-label">Regular Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price_regular" id="price_regular" class="form-control @error('price_regular') is-invalid @enderror"
                                    value="{{ old('price_regular', $item->price_regular) }}" placeholder="45.00">
                                @error('price_regular')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Apply Discount? --}}
                            <div class="col-md-4">
                                <label for="is_discount_active" class="form-label">Apply Discount?</label>
                                <select class="form-select @error('is_discount_active') is-invalid @enderror" name="is_discount_active" id="is_discount_active">
                                    <option value="0" {{ old('is_discount_active', $item->is_discount_active ? '1' : '0') == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('is_discount_active', $item->is_discount_active ? '1' : '0') == '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('is_discount_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Discount Type --}}
                            <div class="col-md-4">
                                <label for="discount_type" class="form-label">Discount Type</label>
                                <select class="form-select @error('discount_type') is-invalid @enderror" name="discount_type" id="discount_type">
                                    <option value="percentage" {{ old('discount_type', $item->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed" {{ old('discount_type', $item->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                @error('discount_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Discount Value --}}
                            <div class="col-md-6">
                                <label for="discount_value" class="form-label">Discount Value</label>
                                <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control @error('discount_value') is-invalid @enderror"
                                    value="{{ old('discount_value', $item->discount_value) }}" placeholder="0.00">
                                @error('discount_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Final Price --}}
                            <div class="col-md-6">
                                <label for="price_final" class="form-label">Final Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price_final" id="price_final" class="form-control @error('price_final') is-invalid @enderror"
                                    value="{{ old('price_final', $item->price_final) }}" placeholder="Enter final price" readonly style="background-color: #e9ecef;">
                                @error('price_final')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Credit Earn --}}
                            <div class="col-md-6">
                                <label for="credit_earn" class="form-label">Credit Earn</label>
                                <input type="number" step="0.01" name="credit_earn" id="credit_earn" class="form-control @error('credit_earn') is-invalid @enderror"
                                    value="{{ old('credit_earn', $item->credit_earn) }}" placeholder="Enter credit earn value">
                                @error('credit_earn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CE Credit Total Required --}}
                            <div class="col-md-6 certification-only-field">
                                <label for="ce_credit_total_required" class="form-label">CE Credit Total Required</label>
                                <input type="number" step="0.01" name="ce_credit_total_required" id="ce_credit_total_required" class="form-control @error('ce_credit_total_required') is-invalid @enderror"
                                    value="{{ old('ce_credit_total_required', $item->ce_credit_total_required) }}" placeholder="Enter total CE credits required">
                                @error('ce_credit_total_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    {{-- Certification Validity (Years) --}}
                                    <div class="col-md-6">
                                        <label for="validity_years" class="form-label">Certification Validity (Years)</label>
                                        <input type="number" name="validity_years" id="validity_years" class="form-control @error('validity_years') is-invalid @enderror"
                                            value="{{ old('validity_years', $item->validity_years ?? 1) }}" placeholder="Enter validity in years (e.g. 1)" min="1">
                                        @error('validity_years')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Certification Seal --}}
                                    <div class="col-md-6">
                                        <label for="certification_seal" class="form-label">Certification Seal</label>
                                        <input type="file" name="certification_seal" id="certification_seal" class="form-control @error('certification_seal') is-invalid @enderror" accept="image/*">
                                        <small class="text-muted">Accepted image types: png, jpg, jpeg, gif, svg, webp. Max size: 2MB.</small>
                                        @error('certification_seal')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        @if ($item->certification_seal)
                                            <div class="mt-2 d-flex align-items-center gap-3">
                                                <div>
                                                    <img src="{{ asset($item->certification_seal) }}" alt="Certification Seal" style="max-height: 50px; object-fit: contain;" class="img-thumbnail">
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remove_certification_seal" id="remove_certification_seal" value="1">
                                                    <label class="form-check-label text-danger fw-semibold" for="remove_certification_seal">
                                                        Remove Seal
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Certification Credential Statement --}}
                                    <div class="col-md-12 mt-3">
                                        <label for="credential_statement" class="form-label">Certification Credential Statement</label>
                                        <textarea name="credential_statement" id="credential_statement" class="form-control @error('credential_statement') is-invalid @enderror"
                                            rows="3" placeholder="Enter certification credential statement">{{ old('credential_statement', $item->credential_statement) }}</textarea>
                                        @error('credential_statement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Special Fields / Checkboxes --}}
                            <div class="col-md-12 module-only-field">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="healthcare_quality_improvement" id="healthcare_quality_improvement" value="1" {{ old('healthcare_quality_improvement', $item->healthcare_quality_improvement) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="healthcare_quality_improvement">Healthcare Quality Improvement</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="patient_safety_risk_management" id="patient_safety_risk_management" value="1" {{ old('patient_safety_risk_management', $item->patient_safety_risk_management) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="patient_safety_risk_management">Patient Safety & Risk Management</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Short Description --}}
                            <div class="col-md-12">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea name="short_description" id="short_description" class="form-control @error('short_description') is-invalid @enderror"
                                    rows="3" placeholder="Enter a brief summary of the catalogue item">{{ old('short_description', $item->short_description) }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Details File --}}
                            <div class="col-md-6">
                                <label for="details_file" class="form-label">Details File (.html)</label>
                                <input type="file" name="details_file" id="details_file" class="form-control @error('details_file') is-invalid @enderror" accept=".html">
                                <small class="text-muted">Only .html files are accepted.</small>
                                @error('details_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @if ($item->details_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($item->details_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-file-code me-1"></i> View Current File ({{ basename($item->details_file) }})
                                        </a>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_details_file" id="remove_details_file" value="1">
                                            <label class="form-check-label text-danger fw-semibold" for="remove_details_file">
                                                Remove Current File
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Overview Video --}}
                            <div class="col-md-6">
                                <label for="overview_video" class="form-label">Overview Video</label>
                                <input type="file" name="overview_video" id="overview_video" class="form-control @error('overview_video') is-invalid @enderror" accept="video/*">
                                <small class="text-muted">Accepted video types: mp4, mov, avi, wmv, webm, ogg. Max size: 100 MB.</small>
                                @error('overview_video')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @if ($item->overview_video)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($item->overview_video) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-film me-1"></i> Play/View Current Video ({{ basename($item->overview_video) }})
                                        </a>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_overview_video" id="remove_overview_video" value="1">
                                            <label class="form-check-label text-danger fw-semibold" for="remove_overview_video">
                                                Remove Current Video
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Story Guide File --}}
                            <div class="col-md-6 certification-only-field">
                                <label for="story_guide_file" class="form-label">Story Guide File (.html)</label>
                                <input type="file" name="story_guide_file" id="story_guide_file" class="form-control @error('story_guide_file') is-invalid @enderror" accept=".html">
                                <small class="text-muted">Only .html files are accepted.</small>
                                @error('story_guide_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @if ($item->story_guide_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($item->story_guide_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-file-code me-1"></i> View Current File ({{ basename($item->story_guide_file) }})
                                        </a>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_story_guide_file" id="remove_story_guide_file" value="1">
                                            <label class="form-check-label text-danger fw-semibold" for="remove_story_guide_file">
                                                Remove Current File
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Badges / Flags --}}
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_feature" id="is_feature" value="1" {{ old('is_feature', $item->is_feature) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_feature">Featured</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_trending" id="is_trending" value="1" {{ old('is_trending', $item->is_trending) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_trending">Trending</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-inline mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_popular" id="is_popular" value="1" {{ old('is_popular', $item->is_popular) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_popular">Popular</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Features Repeater --}}
                    <div class="card-body border-top">
                        <h5 class="card-title mb-3">Catalogue Item Features</h5>

                        <div class="repeater-features">
                            <div data-repeater-list="features">
                                @if ($item->features->count() > 0)
                                    @foreach ($item->features as $feature)
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="{{ $feature->id }}">

                                            <div class="col">
                                                <label class="form-label">
                                                    <span class="badge bg-secondary me-1 serial-badge">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                    Feature Description <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="description" class="form-control" rows="2"
                                                    placeholder="Enter feature description">{{ $feature->description }}</textarea>
                                            </div>

                                            <div class="col-auto mt-4">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
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
                                @endif
                            </div>

                            <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                <i class="fa-solid fa-plus"></i> Add Feature
                            </button>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Catalogue Item
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

            function calculateFinalPrice() {
                var catalogueType = $('#catalogue_type').val();
                if (catalogueType === 'free' || catalogueType === 'members only') {
                    $('#price_regular, #price_final').val('0.00');
                    return;
                }

                var regularPrice = parseFloat($('#price_regular').val()) || 0;
                var isDiscountActive = $('#is_discount_active').val() === '1';
                var discountType = $('#discount_type').val();
                var discountValue = parseFloat($('#discount_value').val()) || 0;

                var finalPrice = regularPrice;

                if (isDiscountActive) {
                    if (discountType === 'percentage') {
                        finalPrice = regularPrice - (regularPrice * (discountValue / 100));
                    } else if (discountType === 'fixed') {
                        finalPrice = regularPrice - discountValue;
                    }
                }

                finalPrice = Math.max(0, finalPrice);
                $('#price_final').val(finalPrice.toFixed(2));
            }

            function togglePriceFields() {
                var type = $('#catalogue_type').val();
                if (type === 'free' || type === 'members only') {
                    $('#price_regular, #price_final, #discount_value').val('0.00').prop('readonly', true).css('background-color', '#e9ecef');
                    $('#is_discount_active, #discount_type').val('0').prop('disabled', true);
                } else {
                    $('#price_regular').prop('readonly', false).css('background-color', '');
                    $('#discount_value').prop('readonly', false).css('background-color', '');
                    $('#is_discount_active, #discount_type').prop('disabled', false);
                    $('#price_final').prop('readonly', true).css('background-color', '#e9ecef');
                }
                calculateFinalPrice();
            }

            $('#price_regular, #is_discount_active, #discount_type, #discount_value').on('input change', calculateFinalPrice);
            $('#catalogue_type').on('change', togglePriceFields);
            togglePriceFields();

            function toggleCertificationFields() {
                var serviceType = $('#service_type').val();
                if (serviceType === 'Certification') {
                    $('.certification-only-field').show();
                } else {
                    $('.certification-only-field').hide();
                }
            }
            $('#service_type').on('change', toggleCertificationFields);
            toggleCertificationFields();

            function toggleModuleFields() {
                var serviceType = $('#service_type').val();
                if (serviceType === 'Module') {
                    $('.module-only-field').show();
                } else {
                    $('.module-only-field').hide();
                }
            }
            $('#service_type').on('change', toggleModuleFields);
            toggleModuleFields();

            function toggleWebinarWorkshopFields() {
                var serviceType = $('#service_type').val();
                if (serviceType === 'Webinar' || serviceType === 'Workshop') {
                    $('.webinar-workshop-only-field').show();
                } else {
                    $('.webinar-workshop-only-field').hide();
                }
            }
            $('#service_type').on('change', toggleWebinarWorkshopFields);
            toggleWebinarWorkshopFields();

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
