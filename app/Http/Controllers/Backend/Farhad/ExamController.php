<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::withCount('questions')
            ->with('questions.options')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('backend.layouts.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('backend.layouts.exams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'question_count'                  => 'required|integer|min:1|max:200',
            'status'                          => 'required|in:published,draft',
            'questions'                       => 'required|array|min:1',
            'questions.*.text'                => 'required|string|max:1000',
            'questions.*.options'             => 'required|array|min:2|max:4',
            'questions.*.options.*.text'      => 'required|string|max:500',
            'questions.*.correct_option'      => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $exam = Exam::create([
                'name'   => $validated['name'],
                'status' => $validated['status'],
            ]);

            foreach ($validated['questions'] as $qIndex => $questionData) {
                $question = $exam->questions()->create([
                    'question_text' => $questionData['text'],
                    'sort_order'    => $qIndex,
                ]);

                $correctIndex = (int) $questionData['correct_option'];

                foreach ($questionData['options'] as $oIndex => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct'  => ($oIndex === $correctIndex),
                        'sort_order'  => $oIndex,
                    ]);
                }
            }
        });

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam created successfully!');
    }

    public function edit($id)
    {
        $exam = Exam::with('questions.options')->findOrFail($id);

        return view('backend.layouts.exams.edit', compact('exam'));
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'question_count'                  => 'required|integer|min:1|max:200',
            'status'                          => 'required|in:published,draft',
            'questions'                       => 'required|array|min:1',
            'questions.*.text'                => 'required|string|max:1000',
            'questions.*.options'             => 'required|array|min:2|max:4',
            'questions.*.options.*.text'      => 'required|string|max:500',
            'questions.*.correct_option'      => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $exam) {
            $exam->update([
                'name'   => $validated['name'],
                'status' => $validated['status'],
            ]);

            // Cascade delete will automatically remove options via DB foreign key constrained onDelete('cascade')
            $exam->questions()->delete();

            foreach ($validated['questions'] as $qIndex => $questionData) {
                $question = $exam->questions()->create([
                    'question_text' => $questionData['text'],
                    'sort_order'    => $qIndex,
                ]);

                $correctIndex = (int) $questionData['correct_option'];

                foreach ($questionData['options'] as $oIndex => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct'  => ($oIndex === $correctIndex),
                        'sort_order'  => $oIndex,
                    ]);
                }
            }
        });

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam updated successfully!');
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->update([
            'status' => $exam->isPublished() ? 'draft' : 'published',
        ]);

        return redirect()->back()
            ->with('success', 'Exam status toggled successfully.');
    }
}
