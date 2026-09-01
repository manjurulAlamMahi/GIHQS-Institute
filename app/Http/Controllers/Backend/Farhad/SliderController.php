<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Helpers\MiaHelper;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sliders = Slider::latest()->get();

            return DataTables::of($sliders)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset($row->image) . '" alt="Slider" width="100" height="60" style="object-fit: cover;">';
                    }
                    return '<span class="text-muted">No Image</span>';
                })
                ->addColumn('position', function ($row) {
                    return '<span class="badge bg-info">' . ucfirst(str_replace('_', ' ', $row->page_position)) . '</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="form-check form-switch form-switch-right form-switch-md">
                            <input class="form-check-input status-switch" type="checkbox" data-id="' . $row->id . '" data-type="slider" ' . $checked . '>
                        </div>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.sliders.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>';
                    $action .= '<form action="' . route('admin.sliders.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger delete-button">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>';
                    return $action;
                })
                ->rawColumns(['image', 'position', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.sliders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
            'page_position' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        $slider = new Slider();

        if ($request->hasFile('image')) {
            $slider->image = MiaHelper::uploadFile($request->file('image'), 'sliders');
        }

        $slider->page_position = $request->page_position;
        $slider->status = $request->status;
        $slider->save();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $slider = Slider::findOrFail($id);
        return view('backend.layouts.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
            'page_position' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        if ($request->hasFile('image')) {
            $slider->image = MiaHelper::updateFile($slider->image, $request->file('image'), 'sliders');
        }

        $slider->page_position = $request->page_position;
        $slider->status = $request->status;
        $slider->save();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);

        if ($slider->image) {
            MiaHelper::deleteFile($slider->image);
        }

        $slider->delete();

        return back()->with('success', 'Slider deleted successfully');
    }
}
