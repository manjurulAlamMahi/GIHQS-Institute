@extends('backend.app')

@push('styles')
<style>
    .step-wrapper {
        max-width: 1000px;
        margin: 0 auto;
    }

    #step2 { display: none; }

    /* Premium Progress Stepper */
    .stepper-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        position: relative;
        background: #fff;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--vz-border-color, #e9ebec);
    }
    .stepper-line {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ebec;
        z-index: 1;
        transform: translateY(-50%);
        margin: 0 80px;
    }
    .stepper-line-progress {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #1a3c34, #0ab39c);
        transition: width 0.4s ease;
    }
    .stepper-item {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        background: #fff;
        padding: 0 10px;
    }
    .stepper-bubble {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #e9ebec;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--vz-muted, #7f8c8d);
        transition: all 0.3s ease;
    }
    .stepper-item.active .stepper-bubble {
        border-color: #1a3c34;
        background: #1a3c34;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(26, 60, 52, 0.15);
    }
    .stepper-item.completed .stepper-bubble {
        border-color: #0ab39c;
        background: #0ab39c;
        color: #fff;
    }
    .stepper-label {
        font-size: 13px;
        font-weight: 600;
        margin-top: 8px;
        color: var(--vz-muted);
        transition: color 0.3s ease;
    }
    .stepper-item.active .stepper-label {
        color: #1a3c34;
    }
    .stepper-item.completed .stepper-label {
        color: #0ab39c;
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

    /* Excel Dropzone Styling */
    .excel-dropzone {
        border: 2px dashed rgba(26, 60, 52, 0.2);
        background: rgba(26, 60, 52, 0.015);
        border-radius: 12px;
        padding: 32px 24px;
        text-align: center;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease-in-out;
    }
    .excel-dropzone:hover {
        border-color: #0ab39c;
        background: rgba(10, 179, 156, 0.025);
    }
    .excel-dropzone.dragover {
        border-color: #0ab39c;
        background: rgba(10, 179, 156, 0.06);
        transform: scale(1.005);
    }
    .excel-dropzone.success-state {
        border-color: #0ab39c;
        border-style: solid;
        background: rgba(10, 179, 156, 0.02);
    }
    .excel-dropzone.error-state {
        border-color: var(--vz-danger, #f06548);
        border-style: solid;
        background: rgba(240, 101, 72, 0.02);
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0 text-dark fw-bold">Create Exam Set</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="step-wrapper">
    <!-- Visual Progress Stepper -->
    <div class="stepper-container">
        <div class="stepper-line">
            <div class="stepper-line-progress" id="stepperProgress"></div>
        </div>
        <div class="stepper-item active" id="stepItem1">
            <div class="stepper-bubble">1</div>
            <div class="stepper-label">Exam Settings</div>
        </div>
        <div class="stepper-item" id="stepItem2">
            <div class="stepper-bubble">2</div>
            <div class="stepper-label">Questions Details</div>
        </div>
    </div>

    <form action="{{ route('admin.exams.store') }}" method="POST" id="mainForm">
        @csrf

        <!-- =================== STEP 1 =================== -->
        <div id="step1" class="card fade-in shadow-sm border-0">
            <div class="card-header border-bottom border-light">
                <h5 class="card-title mb-1 fw-bold text-dark">Step 1: Exam Configuration</h5>
                <p class="text-muted mb-0 fs-13">Configure the initial properties of the question exam set.</p>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark fs-13" for="examName">Exam Name <span class="text-danger">*</span></label>
                    <input type="text" id="examName" name="name" class="form-control"
                           placeholder="e.g. Science and Math Quiz - Set A"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <span class="text-danger mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark fs-13" for="questionCount">Initial Question Count <span class="text-danger">*</span></label>
                    <input type="number" id="questionCount" name="question_count" class="form-control"
                           min="1" max="200" placeholder="e.g. 5"
                           value="{{ old('question_count') }}" required
                           oninput="updatePreview(this.value)">
                    <div class="text-success mt-2 fs-13" id="qCountPreview"></div>
                    @error('question_count')
                        <span class="text-danger mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark fs-13" for="statusSelect">Publish Status <span class="text-danger">*</span></label>
                    <select id="statusSelect" name="status" class="form-select" required>
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>📝 Draft</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>✅ Published</option>
                    </select>
                </div>

                <hr class="my-4" style="border-top: 1px dashed var(--vz-border-color, #e9ebec);">

                <!-- Excel / CSV Bulk Upload Dropzone -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark fs-13 d-flex align-items-center justify-content-between">
                        <span>📂 Bulk Upload Questions (Optional)</span>
                        <a href="#" onclick="downloadSampleTemplate(event)" class="text-primary fs-12 fw-semibold d-flex align-items-center gap-1">
                            <i class="ri-download-cloud-2-line fs-14"></i> Download Excel Template
                        </a>
                    </label>
                    
                    <div id="excelDropzone" class="excel-dropzone">
                        <input type="file" id="excelFile" accept=".xlsx, .xls, .csv" class="d-none">
                        
                        <div id="dropzoneContent">
                            <i class="ri-file-excel-2-line text-success display-5 mb-2 d-inline-block"></i>
                            <h6 class="fw-bold text-dark mb-1">Drag and drop your Excel or CSV file here</h6>
                            <p class="text-muted fs-12 mb-0">or click to browse from your device (.xlsx, .xls, .csv)</p>
                        </div>
                        
                        <div id="dropzoneLoading" style="display: none;">
                            <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Processing file...</h6>
                            <p class="text-muted fs-12 mb-0">Analyzing sheet layout and rows</p>
                        </div>
                        
                        <div id="dropzoneSuccess" style="display: none;">
                            <i class="ri-checkbox-circle-line text-success display-5 mb-2 d-inline-block"></i>
                            <h6 class="fw-bold text-success mb-1">Questions parsed successfully!</h6>
                            <p class="text-muted fs-12 mb-0" id="successDetails">0 questions loaded.</p>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="resetDropzone(event)" style="position: relative; z-index: 20;">Upload different file</button>
                        </div>
                        
                        <div id="dropzoneError" style="display: none;">
                            <i class="ri-error-warning-line text-danger display-5 mb-2 d-inline-block"></i>
                            <h6 class="fw-bold text-danger mb-1" id="errorMsg">Failed to read file</h6>
                            <p class="text-muted fs-12 mb-0" id="errorDetails">Please make sure the structure is correct.</p>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="resetDropzone(event)" style="position: relative; z-index: 20;">Try again</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between p-3 border-top border-light">
                <a href="{{ route('admin.exams.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="button" id="btnStep1Next" class="btn btn-primary px-4 fw-semibold" disabled onclick="goToStep2()">
                    Next: Write Questions <i class="ri-arrow-right-line align-bottom ms-1"></i>
                </button>
            </div>
        </div>
        <!-- =================== END STEP 1 =================== -->

        <!-- =================== STEP 2 =================== -->
        <div id="step2" class="fade-in">
            <!-- Sticky header -->
            <div class="sticky-header d-flex align-items-center justify-content-between">
                <div class="flex-grow-1 overflow-hidden me-3">
                    <span class="text-muted fs-11 text-uppercase fw-bold">Currently Editing</span>
                    <h5 class="sticky-set-name text-dark fw-bold text-truncate mb-0" id="stickyName">—</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info-subtitle text-info fs-12 px-2.5 py-1.5" id="stickyCount">— questions</span>
                    <span class="badge" id="stickyStatus">—</span>
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
                <div class="card-body d-flex gap-2 justify-content-between p-3">
                    <button type="button" class="btn btn-light px-4" onclick="goToStep1()">Back to Settings</button>
                    <div>
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-light px-4 me-1">Cancel</a>
                        <button type="submit" class="btn btn-success px-4" onclick="return validateForm()">
                            <i class="ri-save-line align-bottom me-1"></i> Save Exam Set
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- =================== END STEP 2 =================== -->
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    // State
    const questions = []; // array of { text, options: [{text}], correctOption }

    // Dropzone management
    function showDropzoneState(state) {
        const dz = document.getElementById('excelDropzone');
        if (!dz) return;
        const content = document.getElementById('dropzoneContent');
        const loading = document.getElementById('dropzoneLoading');
        const success = document.getElementById('dropzoneSuccess');
        const error = document.getElementById('dropzoneError');
        
        content.style.display = 'none';
        loading.style.display = 'none';
        success.style.display = 'none';
        error.style.display = 'none';
        
        dz.classList.remove('success-state', 'error-state');
        
        if (state === 'default') {
            content.style.display = 'block';
        } else if (state === 'loading') {
            loading.style.display = 'block';
        } else if (state === 'success') {
            success.style.display = 'block';
            dz.classList.add('success-state');
        } else if (state === 'error') {
            error.style.display = 'block';
            dz.classList.add('error-state');
        }
    }

    function resetDropzone(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const fileInput = document.getElementById('excelFile');
        if (fileInput) fileInput.value = '';
        
        showDropzoneState('default');
        
        // Reset count and questions
        questions.length = 0;
        document.getElementById('questionCount').value = '';
        updatePreview('');
        renderAllQuestions();
        checkStep1();
    }

    // Drag and Drop drag events
    document.addEventListener('DOMContentLoaded', function() {
        const dz = document.getElementById('excelDropzone');
        const fileInput = document.getElementById('excelFile');
        
        if (dz && fileInput) {
            // Prevent default drag behaviors on window to prevent browser from opening the file
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                window.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            // Highlight drop zone when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dz.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dz.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dz.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dz.classList.remove('dragover');
                }, false);
            });

            // Handle dropped files on the dropzone
            dz.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length > 0) {
                    fileInput.files = files; // Sync files to file input
                    handleExcelFile(files[0]);
                }
            }, false);

            // Handle clicking the dropzone
            dz.addEventListener('click', (e) => {
                // Do not trigger file browse if clicking a button (reset or try again)
                if (e.target.tagName.toLowerCase() === 'button') {
                    return;
                }
                fileInput.click();
            });

            // Handle file input changes (when browse is clicked)
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    handleExcelFile(file);
                }
            });
        }
    });

    // Download dynamic Excel Template
    function downloadSampleTemplate(event) {
        event.preventDefault();
        const data = [
            ["Question Text", "Option A", "Option B", "Option C", "Option D", "Correct Option"],
            ["What is the capital of Bangladesh?", "Dhaka", "Chittagong", "Sylhet", "Khulna", "A"],
            ["Which planet is known as the Red Planet?", "Earth", "Mars", "Jupiter", "Saturn", "B"],
            ["Is the Earth flat?", "Yes", "No", "", "", "B"]
        ];
        const ws = XLSX.utils.aoa_to_sheet(data);
        
        ws['!cols'] = [
            { wch: 45 }, // Question Text
            { wch: 15 }, // Option A
            { wch: 15 }, // Option B
            { wch: 15 }, // Option C
            { wch: 15 }, // Option D
            { wch: 15 }  // Correct Option
        ];
        
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Questions Template");
        XLSX.writeFile(wb, "exam_questions_template.xlsx");
    }

    // Parse Excel File
    function handleExcelFile(file) {
        if (!file) return;
        
        showDropzoneState('loading');
        
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                
                if (!workbook.SheetNames || workbook.SheetNames.length === 0) {
                    throw new Error("The Excel file doesn't contain any sheets.");
                }
                
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                
                if (rows.length < 2) {
                    throw new Error("Excel must contain a header row and at least one question row.");
                }
                
                // Clean headers
                const headers = rows[0].map(h => String(h || '').trim().toLowerCase());
                
                const qIdx = headers.indexOf('question text');
                const optAIdx = headers.indexOf('option a');
                const optBIdx = headers.indexOf('option b');
                const optCIdx = headers.indexOf('option c');
                const optDIdx = headers.indexOf('option d');
                const correctIdx = headers.indexOf('correct option');
                
                if (qIdx === -1 || optAIdx === -1 || optBIdx === -1 || correctIdx === -1) {
                    throw new Error("Missing required columns. Please use columns: 'Question Text', 'Option A', 'Option B', 'Option C', 'Option D', 'Correct Option'.");
                }
                
                const tempQuestions = [];
                
                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    if (!row || row.length === 0) continue;
                    
                    const qText = String(row[qIdx] || '').trim();
                    if (!qText) continue; // Skip empty question rows
                    
                    const optA = String(row[optAIdx] || '').trim();
                    const optB = String(row[optBIdx] || '').trim();
                    const optC = optCIdx !== -1 ? String(row[optCIdx] || '').trim() : '';
                    const optD = optDIdx !== -1 ? String(row[optDIdx] || '').trim() : '';
                    
                    const rawOptions = [optA, optB, optC, optD];
                    const cleanOptions = rawOptions
                        .map(o => ({ text: o }))
                        .filter(o => o.text !== '');
                    
                    if (cleanOptions.length < 2) {
                        throw new Error(`Row ${i + 1}: Questions must have at least 2 options (Option A and Option B).`);
                    }
                    
                    // Parse correct option index
                    const correctVal = String(row[correctIdx] || '').trim().toUpperCase();
                    let correctOptIndex = -1;
                    
                    if (correctVal === 'A' || correctVal === '1' || correctVal === optA.toUpperCase()) {
                        correctOptIndex = 0;
                    } else if (correctVal === 'B' || correctVal === '2' || correctVal === optB.toUpperCase()) {
                        correctOptIndex = 1;
                    } else if (correctVal === 'C' || correctVal === '3' || (optC && correctVal === optC.toUpperCase())) {
                        correctOptIndex = 2;
                    } else if (correctVal === 'D' || correctVal === '4' || (optD && correctVal === optD.toUpperCase())) {
                        correctOptIndex = 3;
                    } else {
                        // Check numeric fallback
                        const valInt = parseInt(correctVal);
                        if (!isNaN(valInt) && valInt >= 1 && valInt <= cleanOptions.length) {
                            correctOptIndex = valInt - 1;
                        }
                    }
                    
                    if (correctOptIndex === -1 || correctOptIndex >= cleanOptions.length) {
                        throw new Error(`Row ${i + 1}: Correct option must be A, B, C, D (or match the text of one of the options). Found: "${correctVal}"`);
                    }
                    
                    tempQuestions.push({
                        text: qText,
                        options: cleanOptions,
                        correctOption: correctOptIndex
                    });
                }
                
                if (tempQuestions.length === 0) {
                    throw new Error("No valid question rows found in the sheet.");
                }
                
                if (tempQuestions.length > 200) {
                    throw new Error("Max 200 questions allowed per exam.");
                }
                
                // Clear state and write new questions
                questions.length = 0;
                tempQuestions.forEach(q => questions.push(q));
                
                // Sync UI
                document.getElementById('questionCount').value = questions.length;
                updatePreview(questions.length);
                
                // Re-render
                renderAllQuestions();
                
                // Update dropzone success UI
                document.getElementById('successDetails').textContent = `${questions.length} questions loaded.`;
                showDropzoneState('success');
                
                checkStep1();
                
            } catch (err) {
                console.error(err);
                document.getElementById('errorMsg').textContent = "Upload Failed";
                document.getElementById('errorDetails').textContent = err.message || "Invalid file structure.";
                showDropzoneState('error');
                
                // Reset questions state
                questions.length = 0;
                document.getElementById('questionCount').value = '';
                updatePreview('');
                renderAllQuestions();
                checkStep1();
            }
        };
        
        reader.onerror = function() {
            document.getElementById('errorMsg').textContent = "File Error";
            document.getElementById('errorDetails').textContent = "Failed to read the file.";
            showDropzoneState('error');
            checkStep1();
        };
        
        reader.readAsArrayBuffer(file);
    }

    // Repopulate from old() on validation failure
    function loadFromOldData(oldQs) {
        oldQs.forEach(qData => {
            const rawOpts = Object.values(qData.options || {});
            const opts    = rawOpts.map(o => ({ text: o.text || '' }));
            questions.push({
                text:          qData.text || '',
                options:       opts.length >= 2 ? opts : [{ text: '' }, { text: '' }],
                correctOption: (qData.correct_option !== undefined && qData.correct_option !== '')
                                   ? parseInt(qData.correct_option) : -1,
            });
        });
        renderAllQuestions();
    }

    // Step 1 helpers
    function updatePreview(val) {
        const n = parseInt(val);
        const el = document.getElementById('qCountPreview');
        if (n > 0 && n <= 200) {
            el.textContent = '✓ ' + n + ' question block(s) will be created';
        } else {
            el.textContent = '';
        }
        checkStep1();
    }

    function checkStep1() {
        const name  = document.getElementById('examName').value.trim();
        const count = parseInt(document.getElementById('questionCount').value);
        const btn   = document.getElementById('btnStep1Next');
        btn.disabled = !(name && count >= 1 && count <= 200);
    }

    document.getElementById('examName').addEventListener('input', checkStep1);
    document.getElementById('questionCount').addEventListener('input', checkStep1);

    function goToStep2() {
        const name   = document.getElementById('examName').value.trim();
        const count  = parseInt(document.getElementById('questionCount').value);
        const status = document.getElementById('statusSelect').value;

        // Visual Stepper updates
        document.getElementById('stepItem1').classList.remove('active');
        document.getElementById('stepItem1').classList.add('completed');
        document.getElementById('stepItem2').classList.add('active');
        document.getElementById('stepperProgress').style.width = '100%';

        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';

        // Update sticky header
        document.getElementById('stickyName').textContent  = name;
        document.getElementById('stickyCount').textContent = count + ' Questions';
        const sb = document.getElementById('stickyStatus');
        sb.textContent  = status === 'published' ? 'Published' : 'Draft';
        sb.className    = 'badge ' + (status === 'published' ? 'bg-success-subtitle text-success' : 'bg-warning-subtitle text-warning');

        if (questions.length === 0) {
            generateQuestions(count);
        }
    }

    function goToStep1() {
        // Visual Stepper updates
        document.getElementById('stepItem2').classList.remove('active');
        document.getElementById('stepItem1').classList.remove('completed');
        document.getElementById('stepItem1').classList.add('active');
        document.getElementById('stepperProgress').style.width = '0%';

        document.getElementById('step2').style.display = 'none';
        document.getElementById('step1').style.display = 'block';
    }

    // Generate initial question blocks
    function generateQuestions(n) {
        for (let i = 0; i < n; i++) {
            questions.push({ text: '', options: [{ text: '' }, { text: '' }], correctOption: -1 });
        }
        renderAllQuestions();
    }

    // Add More Question
    function addNewQuestion() {
        const qIndex = questions.length;
        questions.push({ text: '', options: [{ text: '' }, { text: '' }], correctOption: -1 });

        // Update count field in step 1 input to sync with validation
        const countInput = document.getElementById('questionCount');
        if (countInput) {
            countInput.value = questions.length;
            updatePreview(questions.length);
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
                const countInput = document.getElementById('questionCount');
                if (countInput) {
                    countInput.value = questions.length;
                    updatePreview(questions.length);
                }
                
                // Update sticky header count
                const stickyCount = document.getElementById('stickyCount');
                if (stickyCount) {
                    stickyCount.textContent = questions.length + ' Questions';
                }
                
                // Re-render
                renderAllQuestions();
                checkStep1();
            }
        });
    }

    function renderAllQuestions() {
        const container = document.getElementById('questionsContainer');
        container.innerHTML = '';
        questions.forEach((_, i) => renderQuestion(i));
    }

    function renderQuestion(i) {
        const q      = questions[i];
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

        // correct_option hidden input
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

    // Option management
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

    // Mark as correct
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
        const old = document.getElementById('q-block-' + qIndex);
        if (old) old.remove();
        // Re-append at right position
        const container = document.getElementById('questionsContainer');
        const blocks    = container.querySelectorAll('.question-block');
        const refBlock  = blocks[qIndex] || null;

        const block = document.createElement('div');
        block.className = 'card mb-4 question-block';
        block.id        = 'q-block-' + qIndex;

        const q       = questions[qIndex];
        const letters = ['A','B','C','D'];

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

    // Form validation
    function validateForm() {
        let valid = true;
        let firstErrorBlock = null;

        questions.forEach((q, i) => {
            const block    = document.getElementById('q-block-' + i);
            const errorEl  = document.getElementById('q-error-' + i);
            const textarea = block ? block.querySelector('textarea') : null;
            const textVal  = textarea ? textarea.value.trim() : q.text.trim();

            const hasText    = textVal.length > 0;
            const hasCorrect = q.correctOption >= 0;
            const allOptsFilled = q.options.every(o => o.text.trim().length > 0);

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

    // Utility
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Init preview if old value exists
    const initCount = document.getElementById('questionCount').value;
    if (initCount) updatePreview(initCount);

    // Auto-advance to step 2 on validation failure
    @if(old('questions'))
    (function() {
        const oldQs = @json(old('questions', []));
        if (!oldQs || !oldQs.length) return;
        const name   = document.getElementById('examName').value.trim();
        const status = document.getElementById('statusSelect').value;
        
        // Progress bar updates
        document.getElementById('stepItem1').classList.remove('active');
        document.getElementById('stepItem1').classList.add('completed');
        document.getElementById('stepItem2').classList.add('active');
        document.getElementById('stepperProgress').style.width = '100%';

        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
        document.getElementById('stickyName').textContent  = name;
        document.getElementById('stickyCount').textContent = oldQs.length + ' Questions';
        const sb = document.getElementById('stickyStatus');
        sb.textContent = status === 'published' ? 'Published' : 'Draft';
        sb.className   = 'badge ' + (status === 'published' ? 'bg-success-subtitle text-success' : 'bg-warning-subtitle text-warning');
        loadFromOldData(oldQs);
    })();
    @endif
</script>
@endpush
