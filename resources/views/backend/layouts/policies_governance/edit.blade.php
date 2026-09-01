@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Content Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Policies & Governance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('admin.policies-governance.update', $policiesGovernance->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Card 1: Main Header Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Policies & Governance Header</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Title 1 --}}
                            <div class="col-md-6">
                                <label for="title1" class="form-label">Title 1 <span class="text-danger">*</span></label>
                                <input type="text" name="title1" id="title1"
                                    class="form-control @error('title1') is-invalid @enderror"
                                    value="{{ old('title1', $policiesGovernance->title1) }}"
                                    placeholder="Enter Title 1 (e.g. Policies &)">
                                @error('title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title 2 --}}
                            <div class="col-md-6">
                                <label for="title2" class="form-label">Title 2</label>
                                <input type="text" name="title2" id="title2"
                                    class="form-control @error('title2') is-invalid @enderror"
                                    value="{{ old('title2', $policiesGovernance->title2) }}"
                                    placeholder="Enter Title 2 (e.g. Governance)">
                                @error('title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-md-12">
                                <label for="tagline" class="form-label">Tagline</label>
                                <input type="text" name="tagline" id="tagline"
                                    class="form-control @error('tagline') is-invalid @enderror"
                                    value="{{ old('tagline', $policiesGovernance->tagline) }}"
                                    placeholder="Enter Tagline">
                                @error('tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12">
                                <label for="description" class="form-label">Header Description</label>
                                <textarea name="description" id="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="4"
                                    placeholder="Enter Header Description paragraphs">{{ old('description', $policiesGovernance->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Institutional Policies Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Institutional Policies Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-3">
                            {{-- Inst Title --}}
                            <div class="col-md-8">
                                <label for="inst_title" class="form-label">Section Title</label>
                                <input type="text" name="inst_title" id="inst_title"
                                    class="form-control @error('inst_title') is-invalid @enderror"
                                    value="{{ old('inst_title', $policiesGovernance->inst_title) }}"
                                    placeholder="Enter Section Title">
                                @error('inst_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Inst Tag --}}
                            <div class="col-md-4">
                                <label for="inst_tag" class="form-label">Section Tag / Icon Letter</label>
                                <input type="text" name="inst_tag" id="inst_tag"
                                    class="form-control @error('inst_tag') is-invalid @enderror"
                                    value="{{ old('inst_tag', $policiesGovernance->inst_tag) }}"
                                    placeholder="Enter Icon Tag (e.g. I)">
                                @error('inst_tag')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Inst Description --}}
                            <div class="col-md-12">
                                <label for="inst_description" class="form-label">Section Description</label>
                                <textarea name="inst_description" id="inst_description"
                                    class="form-control @error('inst_description') is-invalid @enderror"
                                    rows="2"
                                    placeholder="Enter Section Description">{{ old('inst_description', $policiesGovernance->inst_description) }}</textarea>
                                @error('inst_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Institutional Policies Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3">Institutional Policy Documents</h5>
                            <div class="repeater-inst">
                                <div data-repeater-list="inst_policies">
                                    @php $instDocs = $policiesGovernance->institutionalDocuments; @endphp
                                    @if ($instDocs->count() > 0)
                                        @foreach ($instDocs as $doc)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $doc->id }}">

                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        <span class="badge bg-secondary me-1 serial-badge">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                        Policy Title <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="title" class="form-control" value="{{ $doc->title }}" placeholder="Enter Policy Title">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Upload Document File (PDF/Doc)</label>
                                                    <input type="file" name="file" class="form-control">
                                                    @if ($doc->file)
                                                        <div class="mt-2 current-file-link">
                                                            <a href="{{ asset($doc->file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                                                <i class="fa-solid fa-file-pdf me-1"></i> View Saved File
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-2 mt-4 text-end">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                        <i class="fa-regular fa-trash-can"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">

                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <span class="badge bg-secondary me-1 serial-badge">01</span>
                                                    Policy Title <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="title" class="form-control" placeholder="Enter Policy Title">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Upload Document File (PDF/Doc)</label>
                                                <input type="file" name="file" class="form-control">
                                            </div>

                                            <div class="col-md-2 mt-4 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="fa-solid fa-plus"></i> Add Policy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Certification Policies Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Certification Policies Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-3">
                            {{-- Cert Title --}}
                            <div class="col-md-8">
                                <label for="cert_title" class="form-label">Section Title</label>
                                <input type="text" name="cert_title" id="cert_title"
                                    class="form-control @error('cert_title') is-invalid @enderror"
                                    value="{{ old('cert_title', $policiesGovernance->cert_title) }}"
                                    placeholder="Enter Section Title">
                                @error('cert_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Cert Tag --}}
                            <div class="col-md-4">
                                <label for="cert_tag" class="form-label">Section Tag / Icon Letter</label>
                                <input type="text" name="cert_tag" id="cert_tag"
                                    class="form-control @error('cert_tag') is-invalid @enderror"
                                    value="{{ old('cert_tag', $policiesGovernance->cert_tag) }}"
                                    placeholder="Enter Icon Tag (e.g. C)">
                                @error('cert_tag')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Cert Description --}}
                            <div class="col-md-12">
                                <label for="cert_description" class="form-label">Section Description</label>
                                <textarea name="cert_description" id="cert_description"
                                    class="form-control @error('cert_description') is-invalid @enderror"
                                    rows="2"
                                    placeholder="Enter Section Description">{{ old('cert_description', $policiesGovernance->cert_description) }}</textarea>
                                @error('cert_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Certification Policies Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3">Certification Policy Documents</h5>
                            <div class="repeater-cert">
                                <div data-repeater-list="cert_policies">
                                    @php $certDocs = $policiesGovernance->certificationDocuments; @endphp
                                    @if ($certDocs->count() > 0)
                                        @foreach ($certDocs as $doc)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $doc->id }}">

                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        <span class="badge bg-secondary me-1 serial-badge">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                        Policy Title <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="title" class="form-control" value="{{ $doc->title }}" placeholder="Enter Policy Title">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Upload Document File (PDF/Doc)</label>
                                                    <input type="file" name="file" class="form-control">
                                                    @if ($doc->file)
                                                        <div class="mt-2 current-file-link">
                                                            <a href="{{ asset($doc->file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                                                <i class="fa-solid fa-file-pdf me-1"></i> View Saved File
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-2 mt-4 text-end">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                        <i class="fa-regular fa-trash-can"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">

                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <span class="badge bg-secondary me-1 serial-badge">01</span>
                                                    Policy Title <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="title" class="form-control" placeholder="Enter Policy Title">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Upload Document File (PDF/Doc)</label>
                                                <input type="file" name="file" class="form-control">
                                            </div>

                                            <div class="col-md-2 mt-4 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="fa-solid fa-plus"></i> Add Policy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Accreditation Policies Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Accreditation Policies Section</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-3">
                            {{-- Acc Title --}}
                            <div class="col-md-8">
                                <label for="acc_title" class="form-label">Section Title</label>
                                <input type="text" name="acc_title" id="acc_title"
                                    class="form-control @error('acc_title') is-invalid @enderror"
                                    value="{{ old('acc_title', $policiesGovernance->acc_title) }}"
                                    placeholder="Enter Section Title">
                                @error('acc_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Acc Tag --}}
                            <div class="col-md-4">
                                <label for="acc_tag" class="form-label">Section Tag / Icon Letter</label>
                                <input type="text" name="acc_tag" id="acc_tag"
                                    class="form-control @error('acc_tag') is-invalid @enderror"
                                    value="{{ old('acc_tag', $policiesGovernance->acc_tag) }}"
                                    placeholder="Enter Icon Tag (e.g. A)">
                                @error('acc_tag')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Acc Description --}}
                            <div class="col-md-12">
                                <label for="acc_description" class="form-label">Section Description</label>
                                <textarea name="acc_description" id="acc_description"
                                    class="form-control @error('acc_description') is-invalid @enderror"
                                    rows="2"
                                    placeholder="Enter Section Description">{{ old('acc_description', $policiesGovernance->acc_description) }}</textarea>
                                @error('acc_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Accreditation Policies Repeater --}}
                        <div class="border-top pt-3">
                            <h5 class="card-title mb-3">Accreditation Policy Documents</h5>
                            <div class="repeater-acc">
                                <div data-repeater-list="acc_policies">
                                    @php $accDocs = $policiesGovernance->accreditationDocuments; @endphp
                                    @if ($accDocs->count() > 0)
                                        @foreach ($accDocs as $doc)
                                            <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                                <input type="hidden" name="id" value="{{ $doc->id }}">

                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        <span class="badge bg-secondary me-1 serial-badge">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                        Policy Title <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="title" class="form-control" value="{{ $doc->title }}" placeholder="Enter Policy Title">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Upload Document File (PDF/Doc)</label>
                                                    <input type="file" name="file" class="form-control">
                                                    @if ($doc->file)
                                                        <div class="mt-2 current-file-link">
                                                            <a href="{{ asset($doc->file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                                                <i class="fa-solid fa-file-pdf me-1"></i> View Saved File
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-2 mt-4 text-end">
                                                    <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                        <i class="fa-regular fa-trash-can"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div data-repeater-item class="row mb-3 align-items-start p-3 border rounded bg-light">
                                            <input type="hidden" name="id" value="">

                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <span class="badge bg-secondary me-1 serial-badge">01</span>
                                                    Policy Title <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="title" class="form-control" placeholder="Enter Policy Title">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Upload Document File (PDF/Doc)</label>
                                                <input type="file" name="file" class="form-control">
                                            </div>

                                            <div class="col-md-2 mt-4 text-end">
                                                <button data-repeater-delete type="button" class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create type="button" class="btn btn-success btn-sm mt-1">
                                    <i class="fa-solid fa-plus"></i> Add Policy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Governance Commitment Section --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Governance Commitment</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            {{-- Commitment Title 1 --}}
                            <div class="col-md-6">
                                <label for="commitment_title1" class="form-label">Commitment Title 1</label>
                                <input type="text" name="commitment_title1" id="commitment_title1"
                                    class="form-control @error('commitment_title1') is-invalid @enderror"
                                    value="{{ old('commitment_title1', $policiesGovernance->commitment_title1) }}"
                                    placeholder="Enter Title 1 (e.g. Governance)">
                                @error('commitment_title1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Commitment Title 2 --}}
                            <div class="col-md-6">
                                <label for="commitment_title2" class="form-label">Commitment Title 2</label>
                                <input type="text" name="commitment_title2" id="commitment_title2"
                                    class="form-control @error('commitment_title2') is-invalid @enderror"
                                    value="{{ old('commitment_title2', $policiesGovernance->commitment_title2) }}"
                                    placeholder="Enter Title 2 (e.g. Commitment)">
                                @error('commitment_title2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Commitment Description --}}
                            <div class="col-md-12">
                                <label for="commitment_description" class="form-label">Commitment Description</label>
                                <textarea name="commitment_description" id="commitment_description"
                                    class="form-control @error('commitment_description') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Enter Commitment Description">{{ old('commitment_description', $policiesGovernance->commitment_description) }}</textarea>
                                @error('commitment_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 6: Page Content Injection --}}
                <div class="card mb-4">
                    <div class="card-header align-items-center d-flex">
                        <i class="ri-file-code-line me-2 fs-18 text-secondary"></i>
                        <h4 class="card-title mb-0 flex-grow-1">Page Content Injection</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            {{-- Content File --}}
                            <div class="col-md-6">
                                <label for="content_file" class="form-label">Content File (.html)</label>
                                <input type="file" name="content_file" id="content_file" class="form-control @error('content_file') is-invalid @enderror" accept=".html,.txt">
                                <small class="text-muted">Only .html or .txt files are accepted.</small>
                                @error('content_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                @if ($policiesGovernance->content_file)
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <a href="{{ asset($policiesGovernance->content_file) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-file-code me-1"></i> View Current File ({{ basename($policiesGovernance->content_file) }})
                                        </a>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_content_file" id="remove_content_file" value="1">
                                            <label class="form-check-label text-danger fw-semibold" for="remove_content_file">
                                                Remove Current File
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Injected Status --}}
                            <div class="col-md-6">
                                <label for="injected_status" class="form-label">Injected Status</label>
                                <select class="form-select" name="injected_status" id="injected_status">
                                    <option value="0" {{ old('injected_status', $policiesGovernance->injected_status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    <option value="1" {{ old('injected_status', $policiesGovernance->injected_status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="card mb-5 bg-transparent border-0 shadow-none text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Policies & Governance
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function reindexInst() {
            $('.repeater-inst [data-repeater-item]').each(function(i) {
                $(this).find('.serial-badge').text(String(i + 1).padStart(2, '0'));
            });
        }
        function reindexCert() {
            $('.repeater-cert [data-repeater-item]').each(function(i) {
                $(this).find('.serial-badge').text(String(i + 1).padStart(2, '0'));
            });
        }
        function reindexAcc() {
            $('.repeater-acc [data-repeater-item]').each(function(i) {
                $(this).find('.serial-badge').text(String(i + 1).padStart(2, '0'));
            });
        }

        $(document).ready(function() {
            reindexInst();
            reindexCert();
            reindexAcc();

            $('.repeater-inst').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="file"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                    $(this).find('.current-file-link').remove();
                    reindexInst();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this policy?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexInst();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            $('.repeater-cert').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="file"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                    $(this).find('.current-file-link').remove();
                    reindexCert();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this policy?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexCert();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });

            $('.repeater-acc').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('input[type="text"]').val('');
                    $(this).find('input[type="file"]').val('');
                    $(this).find('input[type="hidden"]').val('');
                    $(this).find('.current-file-link').remove();
                    reindexAcc();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this policy?')) {
                        $(this).slideUp(function() {
                            deleteElement();
                            reindexAcc();
                        });
                    }
                },
                isFirstItemUndeletable: false,
                initEmpty: false
            });
        });
    </script>
@endpush
