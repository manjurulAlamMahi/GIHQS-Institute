<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\PathwayQuestion;
use App\Models\PathwayOption;
use App\Models\PathwayResult;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PathwayQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $questions = PathwayQuestion::with('options')->get();

            return DataTables::of($questions)
                ->addIndexColumn()
                ->addColumn('step', function ($row) {
                    return '<span class="badge bg-primary">Step ' . $row->step_number . '</span>';
                })
                ->addColumn('question', function ($row) {
                    return $row->question_text;
                })
                ->addColumn('options_count', function ($row) {
                    return '<span class="badge bg-info">' . $row->options->count() . ' Options</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="form-check form-switch form-switch-right form-switch-md">
                            <input class="form-check-input status-switch" type="checkbox" data-id="' . $row->id . '" data-type="pathway-question" ' . $checked . '>
                        </div>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.pathway-questions.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>';
                    $action .= '<form action="' . route('admin.pathway-questions.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger delete-button">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>';
                    return $action;
                })
                ->rawColumns(['step', 'options_count', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.pathways.questions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $questions = PathwayQuestion::where('status', 1)->orderBy('step_number')->get();
        $results = PathwayResult::where('status', 1)->orderBy('title')->get();

        return view('backend.layouts.pathways.questions.create', compact('questions', 'results'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'step_number' => 'required|integer|in:1,2,3',
            'question_text' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'options' => 'required|array|min:1',
            'options.*.option_text' => 'required|string|max:255',
            'options.*.target_type' => 'required|string|in:next_question,result',
            'options.*.next_question_id' => 'nullable|required_if:options.*.target_type,next_question|integer',
            'options.*.result_id' => 'nullable|required_if:options.*.target_type,result|integer',
        ]);

        $question = PathwayQuestion::create([
            'step_number' => $request->step_number,
            'question_text' => $request->question_text,
            'status' => $request->status,
        ]);

        if ($request->has('options')) {
            foreach ($request->options as $index => $opt) {
                PathwayOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['option_text'],
                    'next_question_id' => $opt['target_type'] == 'next_question' ? $opt['next_question_id'] : null,
                    'result_id' => $opt['target_type'] == 'result' ? $opt['result_id'] : null,
                    'order' => $index,
                    'status' => 1
                ]);
            }
        }

        return redirect()->route('admin.pathway-questions.index')->with('success', 'Pathway Question and Options created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $question = PathwayQuestion::with('options')->findOrFail($id);
        
        // Load other questions and results for option targets
        $questions = PathwayQuestion::where('id', '!=', $id)->where('status', 1)->orderBy('step_number')->get();
        $results = PathwayResult::where('status', 1)->orderBy('title')->get();

        return view('backend.layouts.pathways.questions.edit', compact('question', 'questions', 'results'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $question = PathwayQuestion::findOrFail($id);

        $request->validate([
            'step_number' => 'required|integer|in:1,2,3',
            'question_text' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'options' => 'required|array|min:1',
            'options.*.option_text' => 'required|string|max:255',
            'options.*.target_type' => 'required|string|in:next_question,result',
            'options.*.next_question_id' => 'nullable|required_if:options.*.target_type,next_question|integer',
            'options.*.result_id' => 'nullable|required_if:options.*.target_type,result|integer',
        ]);

        $question->update([
            'step_number' => $request->step_number,
            'question_text' => $request->question_text,
            'status' => $request->status,
        ]);

        // Recreate options to keep it clean and sync correctly
        $question->options()->delete();

        if ($request->has('options')) {
            foreach ($request->options as $index => $opt) {
                PathwayOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['option_text'],
                    'next_question_id' => $opt['target_type'] == 'next_question' ? $opt['next_question_id'] : null,
                    'result_id' => $opt['target_type'] == 'result' ? $opt['result_id'] : null,
                    'order' => $index,
                    'status' => 1
                ]);
            }
        }

        return redirect()->route('admin.pathway-questions.index')->with('success', 'Pathway Question and Options updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $question = PathwayQuestion::findOrFail($id);
        $question->delete(); // Casacade delete handles options deletion

        return redirect()->route('admin.pathway-questions.index')->with('success', 'Pathway Question deleted successfully.');
    }
}
