<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Models\MembershipPackage;
use App\Models\MembershipPackageFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MembershipPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $packages = MembershipPackage::withCount('features')->latest()->get();

            return DataTables::of($packages)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<strong>' . e($row->name) . '</strong>';
                })
                ->addColumn('title', function ($row) {
                    return e($row->title);
                })
                ->addColumn('price', function ($row) {
                    return '$' . number_format($row->price, 2);
                })
                ->addColumn('discount_percentage', function ($row) {
                    return number_format($row->discount_percentage, 2) . '%';
                })
                ->addColumn('exam_attempt_limit', function ($row) {
                    return $row->exam_attempt_limit . ' attempts';
                })
                ->addColumn('features_count', function ($row) {
                    return '<span class="badge bg-info">' . $row->features_count . '</span>';
                })

                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="form-check form-switch form-switch-right form-switch-md">
                                <input class="form-check-input status-switch" type="checkbox"
                                    data-id="' . $row->id . '" data-type="membership-package" ' . $checked . '>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.membership-packages.clone', $row->id) . '"
                                    class="btn btn-sm btn-info text-white me-1" title="Clone Package">
                                    <i class="fa-regular fa-copy"></i>
                                </a>';
                    $action .= '<a href="' . route('admin.membership-packages.edit', $row->id) . '"
                                    class="btn btn-sm btn-primary">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>';
                    return $action;
                })
                ->rawColumns(['name', 'features_count', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.membership_packages.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.membership_packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'validity_days'     => 'nullable|integer|min:0',
            'exam_attempt_limit'  => 'required|integer|min:0',
            'status'            => 'required|in:0,1',
            'features'          => 'nullable|array',
            'features.*.description' => 'required|string',
            'features.*.badge' => 'nullable|string',
            'features.*.note' => 'nullable|string',
        ]);

        $package = MembershipPackage::create([
            'name'              => $request->name,
            'title'             => $request->title,
            'short_description' => $request->short_description,
            'price'             => $request->price,
            'discount_percentage' => $request->discount_percentage,
            'validity_days'     => $request->validity_days,
            'exam_attempt_limit'  => $request->exam_attempt_limit,
            'status'            => $request->status,
        ]);

        if ($request->has('features')) {
            foreach ($request->features as $featureData) {
                if (!empty($featureData['description'])) {
                    $package->features()->create([
                        'description' => $featureData['description'],
                        'badge'       => $featureData['badge'] ?? null,
                        'note'        => $featureData['note'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.membership-packages.index')
            ->with('success', 'Membership package created successfully.');
    }

    /**
     * Clone the specified resource.
     */
    public function clone($id)
    {
        $package = MembershipPackage::with('features')->findOrFail($id);
        return view('backend.layouts.membership_packages.clone', compact('package'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $package = MembershipPackage::with('features')->findOrFail($id);
        return view('backend.layouts.membership_packages.edit', compact('package'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $package = MembershipPackage::findOrFail($id);

        $request->validate([
            'name'              => 'required|string|max:255',
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'validity_days'     => 'nullable|integer|min:0',
            'exam_attempt_limit'  => 'required|integer|min:0',
            'status'            => 'required|in:0,1',
            'features'          => 'nullable|array',
            'features.*.description' => 'nullable|string',
            'features.*.badge'       => 'nullable|string',
            'features.*.note'        => 'nullable|string',
        ]);

        $package->update([
            'name'              => $request->name,
            'title'             => $request->title,
            'short_description' => $request->short_description,
            'price'             => $request->price,
            'discount_percentage' => $request->discount_percentage,
            'validity_days'     => $request->validity_days,
            'exam_attempt_limit'  => $request->exam_attempt_limit,
            'status'            => $request->status,
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
                    $feature = MembershipPackageFeature::find($featureId);
                }

                if ($feature) {
                    $feature->update([
                        'description' => $featureData['description'],
                        'badge'       => $featureData['badge'] ?? null,
                        'note'        => $featureData['note'] ?? null,
                    ]);
                    $submittedFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $package->features()->create([
                        'description' => $featureData['description'],
                        'badge'       => $featureData['badge'] ?? null,
                        'note'        => $featureData['note'] ?? null,
                    ]);
                    $submittedFeatureIds[] = $newFeature->id;
                }
            }
        }

        // Delete features removed from the repeater
        $package->features()->whereNotIn('id', $submittedFeatureIds)->delete();

        return redirect()->route('admin.membership-packages.index')
            ->with('success', 'Membership package updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $package = MembershipPackage::findOrFail($id);

        $protectedNames = ['standard', 'premium'];
        $protectedIds   = [1, 2];
        if (in_array(strtolower($package->name), $protectedNames) || in_array($package->id, $protectedIds)) {
            return back()->with('error', 'Default system membership packages (Standard and Premium) cannot be deleted.');
        }

        $package->delete();

        return back()->with('success', 'Membership package deleted successfully.');
    }
}
