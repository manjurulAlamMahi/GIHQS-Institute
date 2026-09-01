<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\PathwayResult;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PathwayResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $results = PathwayResult::latest()->get();

            return DataTables::of($results)
                ->addIndexColumn()
                ->addColumn('title', function ($row) {
                    return $row->title;
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="form-check form-switch form-switch-right form-switch-md">
                            <input class="form-check-input status-switch" type="checkbox" data-id="' . $row->id . '" data-type="pathway-result" ' . $checked . '>
                        </div>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.pathway-results.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>';
                    $action .= '<form action="' . route('admin.pathway-results.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger delete-button">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>';
                    return $action;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.pathways.results.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.pathways.results.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'badges' => 'nullable|string', // comma separated in form, will convert to array
            'info_box_text' => 'nullable|string',
            'primary_button_text' => 'required|string|max:255',
            'primary_button_url' => 'nullable|string|max:255',
            'secondary_button_text' => 'required|string|max:255',
            'secondary_button_url' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        // Process badges comma separated string to array
        $badgesArray = null;
        if ($request->filled('badges')) {
            $badgesArray = array_filter(array_map('trim', explode(',', $request->badges)));
        }

        PathwayResult::create([
            'title' => $request->title,
            'description' => $request->description,
            'badges' => $badgesArray,
            'info_box_text' => $request->info_box_text,
            'primary_button_text' => $request->primary_button_text,
            'primary_button_url' => $request->primary_button_url,
            'secondary_button_text' => $request->secondary_button_text,
            'secondary_button_url' => $request->secondary_button_url,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.pathway-results.index')->with('success', 'Pathway Result created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $result = PathwayResult::findOrFail($id);
        
        // Convert badges array to comma separated string for form input
        $badgesString = $result->badges ? implode(', ', $result->badges) : '';

        return view('backend.layouts.pathways.results.edit', compact('result', 'badgesString'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $result = PathwayResult::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'badges' => 'nullable|string',
            'info_box_text' => 'nullable|string',
            'primary_button_text' => 'required|string|max:255',
            'primary_button_url' => 'nullable|string|max:255',
            'secondary_button_text' => 'required|string|max:255',
            'secondary_button_url' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        $badgesArray = null;
        if ($request->filled('badges')) {
            $badgesArray = array_filter(array_map('trim', explode(',', $request->badges)));
        }

        $result->update([
            'title' => $request->title,
            'description' => $request->description,
            'badges' => $badgesArray,
            'info_box_text' => $request->info_box_text,
            'primary_button_text' => $request->primary_button_text,
            'primary_button_url' => $request->primary_button_url,
            'secondary_button_text' => $request->secondary_button_text,
            'secondary_button_url' => $request->secondary_button_url,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.pathway-results.index')->with('success', 'Pathway Result updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = PathwayResult::findOrFail($id);
        $result->delete();

        return redirect()->route('admin.pathway-results.index')->with('success', 'Pathway Result deleted successfully.');
    }
}
