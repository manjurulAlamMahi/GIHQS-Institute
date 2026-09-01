@extends('backend.app')

@push('styles')
<style>
    .step-wrapper {
        max-width: 1000px;
        margin: 0 auto;
    }

    .sticky-header {
        position: sticky;
        top: 70px;
        z-index: 99;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(26, 60, 52, 0.1);
        border-radius: 12px;
        padding: 18px 24px;
        margin-bottom: 25px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    /* Animation effects */
    .fade-in {
        animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .question-block {
        transition: all 0.25s ease;
        border: 1px solid var(--vz-border-color, #e9ebec);
        box-shadow: 0 4px 12px rgba(0,0,0,0.01);
        border-radius: 12px;
        overflow: hidden;
    }
    .question-block:hover {
        border-color: rgba(26, 60, 52, 0.15);
        box-shadow: 0 8px 18px rgba(26, 60, 52, 0.04);
    }
    .question-block.has-error {
        border-color: var(--vz-danger, #f06548);
        box-shadow: 0 4px 12px rgba(240, 101, 72, 0.08);
    }

    .q-header-premium {
        background: linear-gradient(135deg, rgba(26, 60, 52, 0.04) 0%, rgba(10, 179, 156, 0.02) 100%);
        border-bottom: 1px solid rgba(26, 60, 52, 0.06);
    }

    .q-number-badge {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1a3c34, #0ab39c);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        box-shadow: 0 2px 6px rgba(26, 60, 52, 0.2);
    }

    .q-error {
        display: none;
        font-size: 13px;
        padding: 8px 12px;
        border-radius: 6px;
        background: rgba(240, 101, 72, 0.08);
    }
    .q-error.show {
        display: block;
    }

    /* Choices / Option styling */
    .option-row {
        display: flex;
        align-items: center;
        gap: 14px;
        background: #fdfdfd;
        border: 1.5px solid var(--vz-border-color, #e9ebec);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }
    .option-row:hover {
        border-color: rgba(26, 60, 52, 0.15);
        background: #fcfcfc;
    }
    .option-row.is-correct {
        border-color: #0ab39c;
        background: rgba(10, 179, 156, 0.03);
        box-shadow: 0 2px 8px rgba(10, 179, 156, 0.05);
    }

    .option-radio {
        width: 20px;
        height: 20px;
        cursor: pointer;
        border-color: #ced4da;
    }
    .option-radio:checked {
        background-color: #0ab39c;
        border-color: #0ab39c;
    }

    .option-letter {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        background: #f3f6f9;
        border: 1px solid #e9ebec;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        color: #7f8c8d;
        transition: all 0.2s ease;
    }
    .option-row.is-correct .option-letter {
        background: #0ab39c;
        color: #fff;
        border-color: #0ab39c;
    }

    .option-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        font-size: 14px;
        color: var(--vz-body-color);
        padding: 0;
    }

    .btn-remove-option {
        background: none;
        border: none;
        color: var(--vz-danger, #f06548);
        cursor: pointer;
        font-size: 18px;
        padding: 4px;
        opacity: 0.6;
        transition: all 0.15s;
    }
    .btn-remove-option:hover {
        opacity: 1;
        transform: scale(1.1);
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0 text-dark fw-bold">Edit Exam Set</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="step-wrapper fade-in">
    <form action="{{ route('admin.exams.update', $exam) }}" method="POST" id="mainForm">
        @csrf
        @method('PUT')

        <!-- Set Info Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header border-bottom border-light">
                <h5 class="card-title mb-1 fw-bold text-dark">Modify Exam Configuration</h5>
                <p class="text-muted mb-0 fs-13">Change base exam properties and update question records.</p>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark fs-13" for="examName">Exam Name <span class="text-danger">*</span></label>
                    <input type="text" id="examName" name="name" class="form-control"
                           placeholder="e.g. Science and Math Quiz - Set A"
                           value="{{ old('name', $exam->name) }}" required>
                    @error('name')
                        <span class="text-danger mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark fs-13" for="statusSelect">Publish Status <span class="text-danger">*</span></label>
                    <select id="statusSelect" name="status" class="form-select" required>
                        <option value="draft" {{ old('status', $exam->status) === 'draft' ? 'selected' : '' }}>📝 Draft</option>
                        <option value="published" {{ old('status', $exam->status) === 'published' ? 'selected' : '' }}>✅ Published</option>
                    </select>
                </div>

                <!-- hidden question_count input to pass validation -->
                <input type="hidden" name="question_count" id="questionCountHidden"
                       value="{{ old('question_count', old('questions') ? count(old('questions')) : $exam->questions->count()) }}">
            </div>
        </div>

        <!-- Sticky header -->
        <div class="sticky-header d-flex align-items-center justify-content-between">
            <div class="flex-grow-1 overflow-hidden me-3">
                <span class="text-muted fs-11 text-uppercase fw-bold">Active Set</span>
                <h5 class="sticky-set-name text-dark fw-bold text-truncate mb-0" id="stickyName">{{ $exam->name }}</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-info-subtitle text-info fs-12 px-2.5 py-1.5" id="stickyCount">{{ $exam->questions->count() }} Questions</span>
                <span class="badge {{ $exam->isPublished() ? 'bg-success-subtitle text-success' : 'bg-warning-subtitle text-warning' }}" id="stickyStatus">
                    {{ $exam->isPublished() ? 'Published' : 'Draft' }}
                </span>
            </div>
        </div>

        <!-- Questions container -->
        <div id="questionsContainer"></div>

        <!-- Add More Question Button -->
        <button type="button" class="btn btn-outline-primary w-100 mb-4 p-3 fs-15 fw-bold" onclick="addNewQuestion()">
            <i class="ri-add-line align-bottom me-1"></i> Add Another Question
        </button>

        <!-- Form Footer -->
        <div class="card mb-5 border-0 shadow-sm">
            <div class="card-body d-flex gap-2 justify-content-end p-3">
                <a href="{{ route('admin.exams.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-success px-4" onclick="return validateForm()">
                    <i class="ri-save-line align-bottom me-1"></i> Update Exam Set
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    @php
        // When validation fails, prefer old('questions'); otherwise use DB data
        if (old('questions')) {
            $editData = collect(old('questions'))->map(fn($q) => [
                'text'          => $q['text'] ?? '',
                'correctOption' => isset($q['correct_option']) && $q['correct_option'] !== ''
                                       ? (int) $q['correct_option'] : -1,
                'options'       => collect($q['options'] ?? [])
                                       ->map(fn($o) => ['text' => $o['text'] ?? ''])
                                       ->values()->toArray(),
            ])->values()->toArray();
        } else {
            $editData = $exam->questions->map(function ($q) {
                $correctIdx = $q->options->search(fn($o) => $o->is_correct);
                return [
                    'text'          => $q->question_text,
                    'correctOption' => $correctIdx !== false ? $correctIdx : -1,
                    'options'       => $q->options->map(fn($o) => ['text' => $o->option_text])->values()->toArray(),
                ];
            })->values()->toArray();
        }
    @endphp
    const existingData = @json($editData);

    const questions = [];

    function loadExistingData(data) {
        data.forEach(qData => {
            questions.push({
                text:          qData.text,
                options:       qData.options.map(o => ({ text: o.text })),
                correctOption: qData.correctOption !== false ? qData.correctOption : -1,
            });
        });
        renderAllQuestions();
        document.getElementById('questionCountHidden').value = questions.length;
    }

    // Add More Question
    function addNewQuestion() {
        const qIndex = questions.length;
        questions.push({ text: '', options: [{ text: '' }, { text: '' }], correctOption: -1 });

        // Update hidden input to pass validation
        const countInput = document.getElementById('questionCountHidden');
        if (countInput) {
            countInput.value = questions.length;
        }

        // Update sticky header count
        const stickyCount = document.getElementById('stickyCount');
        if (stickyCount) {
            stickyCount.textContent = questions.length + ' Questions';
        }

        // Render newly added question
        renderQuestion(qIndex);
        
        // Scroll to the new question
        setTimeout(() => {
            const el = document.getElementById('q-block-' + qIndex);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    function deleteQuestion(index) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this question? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove question from the array
                questions.splice(index, 1);
                
                // Sync the count fields
                const countInput = document.getElementById('questionCountHidden');
                if (countInput) {
                    countInput.value = questions.length;
                }
                
                // Update sticky header count
                const stickyCount = document.getElementById('stickyCount');
                if (stickyCount) {
                    stickyCount.textContent = questions.length + ' Questions';
                }
                
                // Re-render
                renderAllQuestions();
            }
        });
    }

    function renderAllQuestions() {
        const container = document.getElementById('questionsContainer');
        container.innerHTML = '';
        questions.forEach((_, i) => renderQuestion(i));
    }

    function renderQuestion(i) {
        const q       = questions[i];
        const letters = ['A','B','C','D'];
        const container = document.getElementById('questionsContainer');

        const block = document.createElement('div');
        block.className = 'card mb-4 question-block fade-in';
        block.id        = 'q-block-' + i;

        let optionsHtml = q.options.map((opt, j) => `
            <div class="option-row ${q.correctOption === j ? 'is-correct' : ''}" id="opt-row-${i}-${j}">
                <input type="radio" class="option-radio form-check-input" name="questions[${i}][correct_option_radio]"
                       ${q.correctOption === j ? 'checked' : ''}
                       onchange="setCorrect(${i}, ${j})">
                <div class="option-letter">${letters[j] || (j+1)}</div>
                <input type="text" class="option-input"
                       name="questions[${i}][options][${j}][text]"
                       placeholder="Enter Option ${letters[j] || (j+1)} text..."
                       value="${escapeHtml(opt.text)}"
                       oninput="questions[${i}].options[${j}].text = this.value">
                ${q.options.length > 2
                    ? `<button type="button" class="btn-remove-option" onclick="removeOption(${i}, ${j})" title="Remove Option"><i class="ri-close-line"></i></button>`
                    : '<span style="width:30px"></span>'}
            </div>
        `).join('');

        const correctHidden = `<input type="hidden" name="questions[${i}][correct_option]" id="hidden-correct-${i}" value="${q.correctOption >= 0 ? q.correctOption : ''}">`;

        block.innerHTML = `
            <div class="card-header q-header-premium d-flex align-items-center gap-3 p-3">
                <div class="q-number-badge">${i + 1}</div>
                <h6 class="card-title mb-0 flex-grow-1 text-dark fw-bold">Question #${i + 1}</h6>
                ${questions.length > 1 ? `
                <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1 py-1 px-2.5 fw-semibold" onclick="deleteQuestion(${i})">
                    <i class="ri-delete-bin-line fs-14"></i> Delete Question
                </button>
                ` : ''}
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Question Context/Text</label>
                    <textarea class="form-control" rows="3"
                              name="questions[${i}][text]"
                              placeholder="Type the exam question here..."
                              oninput="questions[${i}].text = this.value"
                              style="resize:vertical">${escapeHtml(q.text)}</textarea>
                </div>
                <div class="q-error text-danger mb-3" id="q-error-${i}"><i class="ri-error-warning-line me-1"></i> Please provide question text, fill all options, and mark the correct answer.</div>
                
                <label class="form-label fw-semibold text-dark d-block">Answer Choices (Choose the correct option below)</label>
                <div class="options-list" id="opts-${i}">
                    ${optionsHtml}
                </div>
                ${correctHidden}
                
                <button type="button" class="btn btn-link btn-sm mt-3 text-primary p-0 d-flex align-items-center gap-1 fw-semibold"
                        id="btn-add-opt-${i}"
                        ${q.options.length >= 4 ? 'style="display:none;"' : ''}
                        onclick="addOption(${i})">
                    <i class="ri-add-circle-fill fs-18"></i> Add Option Choice
                </button>
            </div>
        `;

        container.appendChild(block);
    }

    function addOption(qIndex) {
        const q = questions[qIndex];
        if (q.options.length >= 4) return;
        q.options.push({ text: '' });
        refreshQuestion(qIndex);
    }

    function removeOption(qIndex, optIndex) {
        const q = questions[qIndex];
        if (q.options.length <= 2) return;
        q.options.splice(optIndex, 1);
        if (q.correctOption === optIndex) {
            q.correctOption = -1;
        } else if (q.correctOption > optIndex) {
            q.correctOption--;
        }
        refreshQuestion(qIndex);
    }

    function setCorrect(qIndex, optIndex) {
        questions[qIndex].correctOption = optIndex;
        const hid = document.getElementById('hidden-correct-' + qIndex);
        if (hid) hid.value = optIndex;
        const opts = document.getElementById('opts-' + qIndex);
        if (opts) {
            opts.querySelectorAll('.option-row').forEach((row, j) => {
                row.classList.toggle('is-correct', j === optIndex);
            });
        }
    }

    function refreshQuestion(qIndex) {
        const oldBlock = document.getElementById('q-block-' + qIndex);
        const container = document.getElementById('questionsContainer');
        const blocks    = Array.from(container.querySelectorAll('.question-block'));
        const refBlock  = blocks[qIndex + 1] || null;

        if (oldBlock) oldBlock.remove();

        const q       = questions[qIndex];
        const letters = ['A','B','C','D'];

        const block = document.createElement('div');
        block.className = 'card mb-4 question-block';
        block.id        = 'q-block-' + qIndex;

        let optionsHtml = q.options.map((opt, j) => `
            <div class="option-row ${q.correctOption === j ? 'is-correct' : ''}" id="opt-row-${qIndex}-${j}">
                <input type="radio" class="option-radio form-check-input" name="questions[${qIndex}][correct_option_radio]"
                       ${q.correctOption === j ? 'checked' : ''}
                       onchange="setCorrect(${qIndex}, ${j})">
                <div class="option-letter">${letters[j] || (j+1)}</div>
                <input type="text" class="option-input"
                       name="questions[${qIndex}][options][${j}][text]"
                       placeholder="Enter Option ${letters[j] || (j+1)} text..."
                       value="${escapeHtml(opt.text)}"
                       oninput="questions[${qIndex}].options[${j}].text = this.value">
                ${q.options.length > 2
                    ? `<button type="button" class="btn-remove-option" onclick="removeOption(${qIndex}, ${j})" title="Remove Option"><i class="ri-close-line"></i></button>`
                    : '<span style="width:30px"></span>'}
            </div>
        `).join('');

        const correctHidden = `<input type="hidden" name="questions[${qIndex}][correct_option]" id="hidden-correct-${qIndex}" value="${q.correctOption >= 0 ? q.correctOption : ''}">`;

        block.innerHTML = `
            <div class="card-header q-header-premium d-flex align-items-center gap-3 p-3">
                <div class="q-number-badge">${qIndex + 1}</div>
                <h6 class="card-title mb-0 flex-grow-1 text-dark fw-bold">Question #${qIndex + 1}</h6>
                ${questions.length > 1 ? `
                <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1 py-1 px-2.5 fw-semibold" onclick="deleteQuestion(${qIndex})">
                    <i class="ri-delete-bin-line fs-14"></i> Delete Question
                </button>
                ` : ''}
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Question Context/Text</label>
                    <textarea class="form-control" rows="3"
                              name="questions[${qIndex}][text]"
                              placeholder="Type the exam question here..."
                              oninput="questions[${qIndex}].text = this.value"
                              style="resize:vertical">${escapeHtml(q.text)}</textarea>
                </div>
                <div class="q-error text-danger mb-3" id="q-error-${qIndex}"><i class="ri-error-warning-line me-1"></i> Please provide question text, fill all options, and mark the correct answer.</div>
                
                <label class="form-label fw-semibold text-dark d-block">Answer Choices (Choose the correct option below)</label>
                <div class="options-list" id="opts-${qIndex}">
                    ${optionsHtml}
                </div>
                ${correctHidden}
                
                <button type="button" class="btn btn-link btn-sm mt-3 text-primary p-0 d-flex align-items-center gap-1 fw-semibold"
                        id="btn-add-opt-${qIndex}"
                        ${q.options.length >= 4 ? 'style="display:none;"' : ''}
                        onclick="addOption(${qIndex})">
                    <i class="ri-add-circle-fill fs-18"></i> Add Option Choice
                </button>
            </div>
        `;

        container.insertBefore(block, refBlock);
    }

    function validateForm() {
        let valid = true;
        let firstErrorBlock = null;

        questions.forEach((q, i) => {
            const block    = document.getElementById('q-block-' + i);
            const errorEl  = document.getElementById('q-error-' + i);
            const textarea = block ? block.querySelector('textarea') : null;
            const textVal  = textarea ? textarea.value.trim() : q.text.trim();

            const hasText        = textVal.length > 0;
            const hasCorrect     = q.correctOption >= 0;
            const allOptsFilled  = q.options.every(o => o.text.trim().length > 0);

            if (!hasText || !hasCorrect || !allOptsFilled) {
                valid = false;
                if (block)  block.classList.add('has-error');
                if (errorEl) errorEl.classList.add('show');
                if (!firstErrorBlock) firstErrorBlock = block;
            } else {
                if (block)  block.classList.remove('has-error');
                if (errorEl) errorEl.classList.remove('show');
            }
        });

        if (!valid && firstErrorBlock) {
            firstErrorBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        return valid;
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Sync header on name change
    document.getElementById('examName').addEventListener('input', function() {
        document.getElementById('stickyName').textContent = this.value.trim() || '—';
    });

    // Sync header status on select change
    document.getElementById('statusSelect').addEventListener('change', function() {
        const val = this.value;
        const sb = document.getElementById('stickyStatus');
        sb.textContent = val === 'published' ? 'Published' : 'Draft';
        sb.className = 'badge ' + (val === 'published' ? 'bg-success-subtitle text-success' : 'bg-warning-subtitle text-warning');
    });

    // Load existing data on page load
    loadExistingData(existingData);
</script>
@endpush
