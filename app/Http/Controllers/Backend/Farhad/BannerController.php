<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Helpers\MiaHelper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $banners = Banner::latest()->get();

            return DataTables::of($banners)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset($row->image) . '" alt="Banner" width="100" height="60" style="object-fit: cover;">';
                    }
                    return '<span class="text-muted">No Image</span>';
                })
                ->addColumn('title', function ($row) {
                    return ($row->banner_title ?? 'N/A');
                })
                ->addColumn('position', function ($row) {
                    return '<span class="badge bg-info">' . ucfirst(str_replace('_', ' ', $row->page_position)) . '</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="form-check form-switch form-switch-right form-switch-md">
                            <input class="form-check-input status-switch" type="checkbox" data-id="' . $row->id . '" data-type="banner" ' . $checked . '>
                        </div>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.banners.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>';
                    $action .= '<form action="' . route('admin.banners.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger delete-button">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>';
                    return $action;
                })
                ->rawColumns(['image', 'title', 'position', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.banners.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
            'banner_title' => 'nullable|string|max:255',
            'banner_description' => 'nullable|string',
            // 'page_position' => 'required|string',
            'page_position' => [
                'required',
                'string',
                Rule::unique('banners', 'page_position'),
            ],
            'status' => 'required|in:0,1',
        ]);

        $banner = new Banner();

        if ($request->hasFile('image')) {
            $banner->image = MiaHelper::uploadFile($request->file('image'), 'banners');
        }

        $banner->banner_title = $request->banner_title;
        $banner->banner_description = $request->banner_description;
        $banner->page_position = $request->page_position;
        $banner->status = $request->status;
        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('backend.layouts.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'image' => [
                $banner->image ? 'nullable' : 'required', // required only if no existing image
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:10240'
            ],
            'banner_title' => 'nullable|string|max:255',
            'banner_description' => 'nullable|string',
            // 'page_position' => 'required|string',
            'page_position' => [
                'required',
                'string',
                Rule::unique('banners', 'page_position')->ignore($banner->id),
            ],
            'status' => 'required|in:0,1',
        ]);

        if ($request->hasFile('image')) {
            $banner->image = MiaHelper::updateFile($banner->image, $request->file('image'), 'banners');
        }

        $banner->banner_title = $request->banner_title;
        $banner->banner_description = $request->banner_description;
        $banner->page_position = $request->page_position;
        $banner->status = $request->status;
        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image) {
            MiaHelper::deleteFile($banner->image);
        }

        $banner->delete();

        return back()->with('success', 'Banner deleted successfully');
    }
}
