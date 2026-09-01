@extends('backend.app')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Pathway Questions & Flow</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Question Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Question Create</h4>
                    <a href="{{ route('admin.pathway-questions.index') }}" class="btn btn-sm btn-primary">Back</a>
                </div>

                <form action="{{ route('admin.pathway-questions.store') }}" method="POST" id="questionForm">
                    @csrf

                    <div class="card-body">
                        <div class="row gy-4">

                            {{-- Step Number --}}
                            <div class="col-xxl-6 col-md-6">
                                <label for="step_number" class="form-label">Step Level <span class="text-danger">*</span></label>
                                <select class="form-select @error('step_number') is-invalid @enderror" name="step_number" id="step_number" required>
                                    <option value="" disabled {{ old('step_number') ? '' : 'selected' }}>Choose Step Level</option>
                                    <option value="1" {{ old('step_number') == 1 ? 'selected' : '' }}>Step 1 (First Question)</option>
                                    <option value="2" {{ old('step_number') == 2 ? 'selected' : '' }}>Step 2 (Middle Question)</option>
                                    <option value="3" {{ old('step_number') == 3 ? 'selected' : '' }}>Step 3 (Final Question)</option>
                                </select>
                                @error('step_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Status Field --}}
                            <div class="col-xxl-6 col-md-6">
                                <label class="form-label" for="statusSelect">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect">
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Published</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Unpublished</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Question Text --}}
                            <div class="col-xxl-12 col-md-12">
                                <div>
                                    <label for="question_text" class="form-label">Question Text <span class="text-danger">*</span></label>
                                    <input type="text" name="question_text" id="question_text" class="form-control @error('question_text') is-invalid @enderror" placeholder="e.g. Which best reflects your role or organisation?" value="{{ old('question_text') }}" required>
                                    @error('question_text')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Options Header --}}
                            <div class="col-xxl-12 col-md-12 mt-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h5 class="mb-0">Options / Answers <span class="text-danger">*</span></h5>
                                    <button type="button" class="btn btn-sm btn-success" id="addOptionBtn"><i class="ri-add-line"></i> Add Option</button>
                                </div>
                                <hr class="mt-0">
                                
                                <table class="table table-bordered table-centered" id="optionsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Option Text</th>
                                            <th style="width: 25%;">Target Type</th>
                                            <th style="width: 25%;">Target Destination</th>
                                            <th style="width: 10%;">Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody id="optionsContainer">
                                        <!-- Row will be inserted here dynamically -->
                                    </tbody>
                                </table>
                                @error('options')
                                    <span class="text-danger d-block mt-2">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-xxl-12 col-md-12">
                                <button type="submit" class="btn btn-primary">Save Question & Flow</button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Template for new Option Row -->
    <template id="optionRowTemplate">
        <tr class="option-row">
            <td>
                <input type="text" name="options[__INDEX__][option_text]" class="form-control" placeholder="Enter option text" required>
            </td>
            <td>
                <select name="options[__INDEX__][target_type]" class="form-select target-type-select" required>
                    <option value="" disabled selected>Select Target Type</option>
                    <option value="next_question">Next Question</option>
                    <option value="result">Final Result</option>
                </select>
            </td>
            <td>
                <div class="target-question-container d-none">
                    <select name="options[__INDEX__][next_question_id]" class="form-select">
                        <option value="" selected disabled>Select Next Question</option>
                        @foreach($questions as $q)
                            <option value="{{ $q->id }}" data-step="{{ $q->step_number }}">Step {{ $q->step_number }}: {{ Str::limit($q->question_text, 40) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="target-result-container d-none">
                    <select name="options[__INDEX__][result_id]" class="form-select">
                        <option value="" selected disabled>Select Result</option>
                        @foreach($results as $r)
                            <option value="{{ $r->id }}">{{ $r->title }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-row-btn"><i class="fa-regular fa-trash-can"></i></button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let index = 0;

            function applyStepRestrictions() {
                let stepVal = $('#step_number').val();
                if (!stepVal) return;
                
                let stepLevel = parseInt(stepVal);
                
                $('.option-row').each(function() {
                    let row = $(this);
                    let targetTypeSelect = row.find('.target-type-select');
                    let nextQuestionSelect = row.find('.target-question-container select');
                    
                    let nextQuestionOpt = targetTypeSelect.find('option[value="next_question"]');
                    let resultOpt = targetTypeSelect.find('option[value="result"]');
                    
                    if (stepLevel === 1) {
                        nextQuestionOpt.prop('disabled', false).show();
                        resultOpt.prop('disabled', true).hide();
                        
                        if (targetTypeSelect.val() !== 'next_question') {
                            targetTypeSelect.val('next_question').trigger('change');
                        }
                        
                        nextQuestionSelect.find('option').each(function() {
                            let opt = $(this);
                            if (opt.val() === "") return;
                            if (parseInt(opt.attr('data-step')) === 2) {
                                opt.prop('disabled', false).show();
                            } else {
                                opt.prop('disabled', true).hide();
                                if (opt.is(':selected')) {
                                    nextQuestionSelect.val('');
                                }
                            }
                        });
                    } else if (stepLevel === 2) {
                        nextQuestionOpt.prop('disabled', false).show();
                        resultOpt.prop('disabled', false).show();
                        
                        nextQuestionSelect.find('option').each(function() {
                            let opt = $(this);
                            if (opt.val() === "") return;
                            if (parseInt(opt.attr('data-step')) === 3) {
                                opt.prop('disabled', false).show();
                            } else {
                                opt.prop('disabled', true).hide();
                                if (opt.is(':selected')) {
                                    nextQuestionSelect.val('');
                                }
                            }
                        });
                    } else if (stepLevel === 3) {
                        nextQuestionOpt.prop('disabled', true).hide();
                        resultOpt.prop('disabled', false).show();
                        
                        if (targetTypeSelect.val() !== 'result') {
                            targetTypeSelect.val('result').trigger('change');
                        }
                    }
                });
            }

            // Trigger restrictions on step_number change
            $('#step_number').on('change', function() {
                applyStepRestrictions();
            });

            // Function to add a new option row
            function addOptionRow() {
                let html = $('#optionRowTemplate').html();
                html = html.replaceAll('__INDEX__', index);
                $('#optionsContainer').append(html);
                index++;
                applyStepRestrictions();
            }

            // Add initial row on load
            addOptionRow();

            // Click listener for add button
            $('#addOptionBtn').on('click', function() {
                addOptionRow();
            });

            // Click listener for delete button inside row
            $(document).on('click', '.remove-row-btn', function() {
                if ($('.option-row').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        text: 'You must have at least one option!',
                        timer: 2000,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false
                    });
                }
            });

            // Target type select change listener
            $(document).on('change', '.target-type-select', function() {
                let row = $(this).closest('tr');
                let targetType = $(this).val();

                row.find('.target-question-container').addClass('d-none').find('select').attr('required', false);
                row.find('.target-result-container').addClass('d-none').find('select').attr('required', false);

                if (targetType === 'next_question') {
                    row.find('.target-question-container').removeClass('d-none').find('select').attr('required', true);
                } else if (targetType === 'result') {
                    row.find('.target-result-container').removeClass('d-none').find('select').attr('required', true);
                }
            });
        });
    </script>
@endpush
