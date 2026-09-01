<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Models\Catalogue;
use App\Models\CatalogueFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\MiaHelper;

class CatalogueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Catalogue::withCount('features')->latest()->get();

            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('title', function ($row) {
                    return '<strong>' . e($row->title) . '</strong>';
                })
                ->addColumn('service_type', function ($row) {
                    $badges = [
                        'Certification' => 'bg-warning text-dark',
                        'Course'        => 'bg-success',
                        'Webinar'       => 'bg-danger',
                        'Module'        => 'bg-primary',
                        'Toolkit'       => 'bg-info',
                        'Workshop'      => 'bg-secondary'
                    ];
                    $badgeClass = $badges[$row->service_type] ?? 'bg-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . e($row->service_type) . '</span>';
                })
                ->addColumn('price_regular', function ($row) {
                    return '$' . number_format($row->price_regular, 2);
                })
                ->addColumn('price_final', function ($row) {
                    return '$' . number_format($row->price_final, 2);
                })
                ->addColumn('catalogue_type', function ($row) {
                    $badges = [
                        'paid'         => 'bg-danger text-white',
                        'free'         => 'bg-success text-white',
                        'members only' => 'bg-info text-dark',
                    ];
                    $badgeClass = $badges[$row->catalogue_type] ?? 'bg-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . e(ucfirst($row->catalogue_type)) . '</span>';
                })
                ->addColumn('features_count', function ($row) {
                    return '<span class="badge bg-info">' . $row->features_count . '</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="form-check form-switch form-switch-right form-switch-md">
                                <input class="form-check-input status-switch" type="checkbox"
                                    data-id="' . $row->id . '" data-type="catalogue" ' . $checked . '>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.catalogues.edit', $row->id) . '"
                                    class="btn btn-sm btn-primary me-1">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>';
                    $action .= '<a href="' . route('admin.catalogue-html-resources.index', $row->id) . '"
                                    class="btn btn-sm btn-info me-1" title="HTML documents">
                                    <i class="fa-solid fa-file-code"></i>
                                </a>';
                    $action .= '<form action="' . route('admin.catalogues.destroy', $row->id) . '"
                                    method="POST" style="display:inline-block;">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger delete-button">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>';
                    return $action;
                })
                ->rawColumns(['title', 'service_type', 'catalogue_type', 'features_count', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.catalogues.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.catalogues.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (in_array($request->catalogue_type, ['free', 'members only'])) {
            $request->merge([
                'price_regular'      => 0.00,
                'price_final'        => 0.00,
                'discount_value'     => 0.00,
                'is_discount_active' => 0,
            ]);
        } else {
            $request->merge([
                'is_discount_active' => $request->is_discount_active == '1' ? 1 : 0,
                'discount_value'     => $request->discount_value ?? 0.00,
            ]);
        }

        if ($request->service_type !== 'Certification') {
            $request->merge([
                'ce_credit_total_required' => 0.00,
            ]);
        }

        if (!in_array($request->service_type, ['Webinar', 'Workshop'])) {
            $request->merge([
                'fixed_date' => null,
                'start_time' => null,
                'end_time' => null,
            ]);
        }

        $request->validate([
            'title'             => 'required|string|max:255',
            'short_title'       => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'price_regular'     => 'required|numeric|min:0',
            'price_final'       => 'required|numeric|min:0',
            'catalogue_type'    => 'required|string|in:paid,free,members only',
            'discount_type'     => 'nullable|string|in:percentage,fixed',
            'discount_value'    => 'nullable|numeric|min:0',
            'is_discount_active'=> 'nullable|in:0,1',
            'price_member'      => 'nullable|numeric|min:0',
            'service_type'      => 'required|string|in:Certification,Course,Webinar,Module,Toolkit,Workshop',
            'fixed_date'        => 'required_if:service_type,Webinar,Workshop|nullable|date',
            'start_time'        => 'required_if:service_type,Webinar,Workshop|nullable',
            'end_time'          => 'required_if:service_type,Webinar,Workshop|nullable',
            'details_file'      => 'nullable|file|mimes:html,txt|max:10240',
            'story_guide_file'  => 'nullable|file|mimes:html,txt|max:10240',
            'module_file'       => 'nullable|file|mimes:html,txt|max:10240',
            'certification_seal'=> 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'overview_video'    => 'nullable|file|mimes:mp4,mov,avi,wmv,webm,ogg|max:102400',
            'credential_statement' => 'nullable|string',
            'status'            => 'required|in:0,1',
            'healthcare_quality_improvement' => 'nullable|boolean',
            'patient_safety_risk_management' => 'nullable|boolean',
            'credit_earn'       => 'nullable|numeric|min:0',
            'ce_credit_total_required' => 'nullable|numeric|min:0',
            'validity_years'    => 'nullable|integer|min:1',
            'features'          => 'nullable|array',
            'features.*.description' => 'required|string',
        ]);

        $detailsFilePath = null;
        if ($request->hasFile('details_file')) {
            $detailsFilePath = MiaHelper::uploadFile($request->file('details_file'), 'development-catalogues');
        }

        $storyGuideFilePath = null;
        if ($request->hasFile('story_guide_file')) {
            $storyGuideFilePath = MiaHelper::uploadFile($request->file('story_guide_file'), 'development-catalogues');
        }

        $moduleFilePath = null;
        if ($request->hasFile('module_file')) {
            $moduleFilePath = MiaHelper::uploadFile($request->file('module_file'), 'development-catalogues');
        }

        $certificationSealPath = null;
        if ($request->hasFile('certification_seal')) {
            $certificationSealPath = MiaHelper::uploadFile($request->file('certification_seal'), 'development-catalogues');
        }

        $overviewVideoPath = null;
        if ($request->hasFile('overview_video')) {
            $overviewVideoPath = MiaHelper::uploadFile($request->file('overview_video'), 'development-catalogues');
        }

        $item = Catalogue::create([
            'title'             => $request->title,
            'short_title'       => $request->short_title,
            'short_description' => $request->short_description,
            'price_regular'     => $request->price_regular,
            'price_final'       => $request->price_final,
            'catalogue_type'    => $request->catalogue_type,
            'discount_type'     => $request->discount_type ?? 'percentage',
            'discount_value'    => $request->discount_value,
            'is_discount_active'=> $request->is_discount_active,
            'price_member'      => $request->price_member ?? 0.00,
            'service_type'      => $request->service_type,
            'fixed_date'        => $request->fixed_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'details_file'      => $detailsFilePath,
            'story_guide_file'  => $storyGuideFilePath,
            'module_file'       => $moduleFilePath,
            'is_feature'        => $request->has('is_feature'),
            'is_trending'       => $request->has('is_trending'),
            'is_popular'        => $request->has('is_popular'),
            'healthcare_quality_improvement' => $request->has('healthcare_quality_improvement'),
            'patient_safety_risk_management' => $request->has('patient_safety_risk_management'),
            'status'            => $request->status,
            'credit_earn'       => $request->credit_earn ?? 0.00,
            'ce_credit_total_required' => $request->ce_credit_total_required ?? 0.00,
            'validity_years'    => $request->validity_years ?? 1,
            'certification_seal'=> $certificationSealPath,
            'credential_statement' => $request->credential_statement,
            'overview_video'    => $overviewVideoPath,
        ]);

        if ($request->has('features')) {
            foreach ($request->features as $featureData) {
                if (!empty($featureData['description'])) {
                    $item->features()->create([
                        'description' => $featureData['description'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.catalogues.index')
            ->with('success', 'Catalogue item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = Catalogue::with('features')->findOrFail($id);
        return view('backend.layouts.catalogues.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = Catalogue::findOrFail($id);

        if (in_array($request->catalogue_type, ['free', 'members only'])) {
            $request->merge([
                'price_regular'      => 0.00,
                'price_final'        => 0.00,
                'discount_value'     => 0.00,
                'is_discount_active' => 0,
            ]);
        } else {
            $request->merge([
                'is_discount_active' => $request->is_discount_active == '1' ? 1 : 0,
                'discount_value'     => $request->discount_value ?? 0.00,
            ]);
        }

        if ($request->service_type !== 'Certification') {
            $request->merge([
                'ce_credit_total_required' => 0.00,
            ]);
        }

        if (!in_array($request->service_type, ['Webinar', 'Workshop'])) {
            $request->merge([
                'fixed_date' => null,
                'start_time' => null,
                'end_time' => null,
            ]);
        }

        $request->validate([
            'title'             => 'required|string|max:255',
            'short_title'       => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'price_regular'     => 'required|numeric|min:0',
            'price_final'       => 'required|numeric|min:0',
            'catalogue_type'    => 'required|string|in:paid,free,members only',
            'discount_type'     => 'nullable|string|in:percentage,fixed',
            'discount_value'    => 'nullable|numeric|min:0',
            'is_discount_active'=> 'nullable|in:0,1',
            'price_member'      => 'nullable|numeric|min:0',
            'service_type'      => 'required|string|in:Certification,Course,Webinar,Module,Toolkit,Workshop',
            'fixed_date'        => 'required_if:service_type,Webinar,Workshop|nullable|date',
            'start_time'        => 'required_if:service_type,Webinar,Workshop|nullable',
            'end_time'          => 'required_if:service_type,Webinar,Workshop|nullable',
            'details_file'      => 'nullable|file|mimes:html,txt|max:10240',
            'story_guide_file'  => 'nullable|file|mimes:html,txt|max:10240',
            'module_file'       => 'nullable|file|mimes:html,txt|max:10240',
            'certification_seal'=> 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'overview_video'    => 'nullable|file|mimes:mp4,mov,avi,wmv,webm,ogg|max:102400',
            'credential_statement' => 'nullable|string',
            'status'            => 'required|in:0,1',
            'healthcare_quality_improvement' => 'nullable|boolean',
            'patient_safety_risk_management' => 'nullable|boolean',
            'credit_earn'       => 'nullable|numeric|min:0',
            'ce_credit_total_required' => 'nullable|numeric|min:0',
            'validity_years'    => 'nullable|integer|min:1',
            'features'          => 'nullable|array',
            'features.*.description' => 'nullable|string',
        ]);

        $detailsFilePath = $item->details_file;

        if ($request->remove_details_file == 1) {
            MiaHelper::deleteFile($item->details_file);
            $detailsFilePath = null;
        } elseif ($request->hasFile('details_file')) {
            $detailsFilePath = MiaHelper::updateFile($item->details_file, $request->file('details_file'), 'development-catalogues');
        }

        $storyGuideFilePath = $item->story_guide_file;

        if ($request->remove_story_guide_file == 1) {
            MiaHelper::deleteFile($item->story_guide_file);
            $storyGuideFilePath = null;
        } elseif ($request->hasFile('story_guide_file')) {
            $storyGuideFilePath = MiaHelper::updateFile($item->story_guide_file, $request->file('story_guide_file'), 'development-catalogues');
        }

        $moduleFilePath = $item->module_file;

        if ($request->remove_module_file == 1) {
            MiaHelper::deleteFile($item->module_file);
            $moduleFilePath = null;
        } elseif ($request->hasFile('module_file')) {
            $moduleFilePath = MiaHelper::updateFile($item->module_file, $request->file('module_file'), 'development-catalogues');
        }

        $certificationSealPath = $item->certification_seal;

        if ($request->remove_certification_seal == 1) {
            MiaHelper::deleteFile($item->certification_seal);
            $certificationSealPath = null;
        } elseif ($request->hasFile('certification_seal')) {
            $certificationSealPath = MiaHelper::updateFile($item->certification_seal, $request->file('certification_seal'), 'development-catalogues');
        }

        $overviewVideoPath = $item->overview_video;

        if ($request->remove_overview_video == 1) {
            MiaHelper::deleteFile($item->overview_video);
            $overviewVideoPath = null;
        } elseif ($request->hasFile('overview_video')) {
            $overviewVideoPath = MiaHelper::updateFile($item->overview_video, $request->file('overview_video'), 'development-catalogues');
        }

        $item->update([
            'title'             => $request->title,
            'short_title'       => $request->short_title,
            'short_description' => $request->short_description,
            'price_regular'     => $request->price_regular,
            'price_final'       => $request->price_final,
            'catalogue_type'    => $request->catalogue_type,
            'discount_type'     => $request->discount_type ?? 'percentage',
            'discount_value'    => $request->discount_value,
            'is_discount_active'=> $request->is_discount_active,
            'price_member'      => $request->price_member ?? 0.00,
            'service_type'      => $request->service_type,
            'fixed_date'        => $request->fixed_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'details_file'      => $detailsFilePath,
            'story_guide_file'  => $storyGuideFilePath,
            'module_file'       => $moduleFilePath,
            'is_feature'        => $request->has('is_feature'),
            'is_trending'       => $request->has('is_trending'),
            'is_popular'        => $request->has('is_popular'),
            'healthcare_quality_improvement' => $request->has('healthcare_quality_improvement'),
            'patient_safety_risk_management' => $request->has('patient_safety_risk_management'),
            'status'            => $request->status,
            'credit_earn'       => $request->credit_earn ?? 0.00,
            'ce_credit_total_required' => $request->ce_credit_total_required ?? 0.00,
            'validity_years'    => $request->validity_years ?? 1,
            'certification_seal'=> $certificationSealPath,
            'credential_statement' => $request->credential_statement,
            'overview_video'    => $overviewVideoPath,
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
                    $feature = CatalogueFeature::find($featureId);
                }

                if ($feature) {
                    $feature->update([
                        'description' => $featureData['description'],
                    ]);
                    $submittedFeatureIds[] = $feature->id;
                } else {
                    $newFeature = $item->features()->create([
                        'description' => $featureData['description'],
                    ]);
                    $submittedFeatureIds[] = $newFeature->id;
                }
            }
        }

        // Delete features removed from the repeater
        $item->features()->whereNotIn('id', $submittedFeatureIds)->delete();

        return redirect()->route('admin.catalogues.index')
            ->with('success', 'Catalogue item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = Catalogue::findOrFail($id);
        MiaHelper::deleteFile($item->details_file);
        MiaHelper::deleteFile($item->story_guide_file);
        MiaHelper::deleteFile($item->module_file);
        MiaHelper::deleteFile($item->overview_video);
        $item->delete();

        return redirect()->route('admin.catalogues.index')
            ->with('success', 'Catalogue item deleted successfully.');
    }
}
