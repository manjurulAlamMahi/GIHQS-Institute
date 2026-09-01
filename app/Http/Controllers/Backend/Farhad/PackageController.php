<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Models\Package;
use App\Models\PackageFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $packages = Package::withCount('features')->latest()->get();

            return DataTables::of($packages)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<strong>' . e($row->name) . '</strong>';
                })
                ->addColumn('features_count', function ($row) {
                    return '<span class="badge bg-info">' . $row->features_count . '</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="form-check form-switch form-switch-right form-switch-md">
                                <input class="form-check-input status-switch" type="checkbox"
                                    data-id="' . $row->id . '" data-type="package" ' . $checked . '>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.packages.edit', $row->id) . '"
                                    class="btn btn-sm btn-primary me-1">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>';
                    $action .= '<form action="' . route('admin.packages.destroy', $row->id) . '"
                                    method="POST" style="display:inline-block;">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger delete-button">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>';
                    return $action;
                })
                ->rawColumns(['name', 'features_count', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.packages.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'status'   => 'required|in:0,1',
            'features' => 'nullable|array',
            'features.*.description' => 'required|string',
        ]);

        $package = Package::create([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        if ($request->has('features')) {
            foreach ($request->features as $featureData) {
                if (!empty($featureData['description'])) {
                    $package->features()->create([
                        'description' => $featureData['description'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        $package->load('features');
        return view('backend.layouts.packages.edit', compact('package'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'status'   => 'required|in:0,1',
            'features' => 'nullable|array',
            'features.*.description' => 'nullable|string',
        ]);

        $package->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        $submittedFeatureIds = [];

        if ($request->has('features')) {
            foreach ($request->features as $featureData) {
                if (empty($featureData['description'])) {
                    continue;
                }

                $featureId = $featureData['id'] ?? null;
                $feature   = null;

                if ($featureId) {
                    $feature = PackageFeature::find($featureId);
                }

                if ($feature) {
                    // Update existing feature
                    $feature->update([
                        'description' => $featureData['description'],
                    ]);
                    $submittedFeatureIds[] = $feature->id;
                } else {
                    // Create new feature
                    $newFeature = $package->features()->create([
                        'description' => $featureData['description'],
                    ]);
                    $submittedFeatureIds[] = $newFeature->id;
                }
            }
        }

        // Delete features removed from the repeater
        $package->features()->whereNotIn('id', $submittedFeatureIds)->delete();

        return back()->with('success', 'Package updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        // Features will cascade-delete via DB constraint
        $package->delete();

        return back()->with('success', 'Package deleted successfully.');
    }
}
