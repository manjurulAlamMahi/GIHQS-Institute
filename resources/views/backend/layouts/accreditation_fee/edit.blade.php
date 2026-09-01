@extends('backend.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Accreditation Fees</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item">Accreditation</li>
                        <li class="breadcrumb-item active">Accreditation Fees</li>
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
            <form id="fees-form" action="{{ route('admin.accreditation-fees.update', $fee->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Card: Fees Section Header --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Fees Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <label for="title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="title1" id="title1"
                                    class="form-control @error('title1') is-invalid @enderror"
                                    value="{{ old('title1', $fee->title1) }}" placeholder="Enter Title 1" required>
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2"
                                    class="form-control @error('title2') is-invalid @enderror"
                                    value="{{ old('title2', $fee->title2) }}" placeholder="Enter Title 2">
                                @error('title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="3" placeholder="Enter Description">{{ old('description', $fee->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Pricing Plans --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Pricing Plans</h4>
                    </div>
                    <div class="card-body">
                        {{-- Plans container --}}
                        <div id="plans-container">
                            @if ($fee->plans->count() > 0)
                                @foreach ($fee->plans as $planIndex => $plan)
                                    <div class="plan-card mb-4 p-3 border rounded bg-light" data-plan-index="{{ $planIndex }}">
                                        {{-- Hidden IDs --}}
                                        <input type="hidden" name="plans[{{ $planIndex }}][id]" value="{{ $plan->id }}">

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 fw-semibold text-primary">
                                                <i class="ri-price-tag-3-line me-1"></i> Plan #<span class="plan-number">{{ $planIndex + 1 }}</span>
                                            </h6>
                                            <button type="button" class="btn btn-danger btn-sm remove-plan-btn">
                                                <i class="ri-delete-bin-line"></i> Remove Plan
                                            </button>
                                        </div>

                                        <div class="row gy-3 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Plan Title <span class="text-danger">*</span></label>
                                                <input type="text" name="plans[{{ $planIndex }}][title]"
                                                    class="form-control" required placeholder="e.g. Standard Plan"
                                                    value="{{ $plan->title }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Price</label>
                                                <input type="text" name="plans[{{ $planIndex }}][price]"
                                                    class="form-control" placeholder="e.g. $2,500"
                                                    value="{{ $plan->price }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Description</label>
                                                <textarea name="plans[{{ $planIndex }}][description]"
                                                    class="form-control" rows="1"
                                                    placeholder="Short description of this plan">{{ $plan->description }}</textarea>
                                            </div>
                                        </div>

                                        {{-- Plan Features --}}
                                        <div class="border-top pt-3 mt-2">
                                            <h6 class="mb-3 text-muted fs-13">Plan Features</h6>
                                            <div class="plan-features-container">
                                                @foreach ($plan->features as $featIndex => $feat)
                                                    <div class="feature-row d-flex align-items-center gap-2 mb-2">
                                                        <input type="hidden"
                                                            name="plans[{{ $planIndex }}][features][{{ $featIndex }}][id]"
                                                            value="{{ $feat->id }}">
                                                        <input type="text"
                                                            name="plans[{{ $planIndex }}][features][{{ $featIndex }}][feature]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Enter feature" required
                                                            value="{{ $feat->feature }}">
                                                        <button type="button" class="btn btn-danger btn-sm remove-feature-btn flex-shrink-0">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-success btn-sm add-feature-btn mt-1">
                                                <i class="ri-add-line"></i> Add Feature
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button type="button" id="add-plan-btn" class="btn btn-primary btn-sm mt-2">
                            <i class="ri-add-line"></i> Add Plan
                        </button>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4 py-2">
                        <i class="ri-save-line me-1"></i> Update Accreditation Fees
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    /* =====================================================================
       Helpers — recalculate all name attributes after any structural change
       ===================================================================== */

    function reindexAll() {
        $('#plans-container .plan-card').each(function (pIdx) {
            // Update plan number badge
            $(this).attr('data-plan-index', pIdx);
            $(this).find('.plan-number').text(pIdx + 1);

            // Plan-level fields
            $(this).find('input[name^="plans["]').each(function () {
                $(this).attr('name', $(this).attr('name').replace(/plans\[\d+\]/, 'plans[' + pIdx + ']'));
            });
            $(this).find('textarea[name^="plans["]').each(function () {
                $(this).attr('name', $(this).attr('name').replace(/plans\[\d+\]/, 'plans[' + pIdx + ']'));
            });

            // Feature-level fields
            $(this).find('.plan-features-container .feature-row').each(function (fIdx) {
                $(this).find('input').each(function () {
                    var name = $(this).attr('name') || '';
                    // Replace plans[X][features][Y] pattern
                    name = name.replace(/plans\[\d+\]\[features\]\[\d+\]/, 'plans[' + pIdx + '][features][' + fIdx + ']');
                    $(this).attr('name', name);
                });
            });
        });
    }

    /* =====================================================================
       ADD PLAN button
       ===================================================================== */

    $('#add-plan-btn').on('click', function () {
        var pIdx = $('#plans-container .plan-card').length;

        var planHtml = `
            <div class="plan-card mb-4 p-3 border rounded bg-light" data-plan-index="${pIdx}">
                <input type="hidden" name="plans[${pIdx}][id]" value="">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-semibold text-primary">
                        <i class="ri-price-tag-3-line me-1"></i> Plan #<span class="plan-number">${pIdx + 1}</span>
                    </h6>
                    <button type="button" class="btn btn-danger btn-sm remove-plan-btn">
                        <i class="ri-delete-bin-line"></i> Remove Plan
                    </button>
                </div>
                <div class="row gy-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Plan Title <span class="text-danger">*</span></label>
                        <input type="text" name="plans[${pIdx}][title]" class="form-control" required placeholder="e.g. Standard Plan">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Price</label>
                        <input type="text" name="plans[${pIdx}][price]" class="form-control" placeholder="e.g. $2,500">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Description</label>
                        <textarea name="plans[${pIdx}][description]" class="form-control" rows="2" placeholder="Short description of this plan"></textarea>
                    </div>
                </div>
                <div class="border-top pt-3 mt-2">
                    <h6 class="mb-3 text-muted fs-13">Plan Features</h6>
                    <div class="plan-features-container"></div>
                    <button type="button" class="btn btn-success btn-sm add-feature-btn mt-1">
                        <i class="ri-add-line"></i> Add Feature
                    </button>
                </div>
            </div>`;

        $('#plans-container').append(planHtml);
        reindexAll();
    });

    /* =====================================================================
       REMOVE PLAN button (delegated)
       ===================================================================== */

    $(document).on('click', '.remove-plan-btn', function () {
        if (!confirm('Are you sure you want to remove this plan and all its features?')) return;
        $(this).closest('.plan-card').remove();
        reindexAll();
    });

    /* =====================================================================
       ADD FEATURE button (delegated)
       ===================================================================== */

    $(document).on('click', '.add-feature-btn', function () {
        var $planCard = $(this).closest('.plan-card');
        var pIdx     = $planCard.attr('data-plan-index');
        var fIdx     = $planCard.find('.plan-features-container .feature-row').length;

        var featureHtml = `
            <div class="feature-row d-flex align-items-center gap-2 mb-2">
                <input type="hidden" name="plans[${pIdx}][features][${fIdx}][id]" value="">
                <input type="text"
                    name="plans[${pIdx}][features][${fIdx}][feature]"
                    class="form-control form-control-sm"
                    placeholder="Enter feature" required>
                <button type="button" class="btn btn-danger btn-sm remove-feature-btn flex-shrink-0">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>`;

        $planCard.find('.plan-features-container').append(featureHtml);
        reindexAll();
    });

    /* =====================================================================
       REMOVE FEATURE button (delegated)
       ===================================================================== */

    $(document).on('click', '.remove-feature-btn', function () {
        $(this).closest('.feature-row').remove();
        reindexAll();
    });

    /* =====================================================================
       Reindex on page load to ensure clean indexes from the server-side data
       ===================================================================== */
    reindexAll();
});
</script>
@endpush
