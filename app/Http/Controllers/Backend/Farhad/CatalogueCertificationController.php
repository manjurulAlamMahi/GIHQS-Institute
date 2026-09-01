<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\Catalogue;
use App\Models\CatalogueResource;
use App\Models\CatalogueExam;
use App\Helpers\MiaHelper;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CatalogueCertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Catalogue::where('service_type', 'Certification')
                ->withCount(['resources', 'exams'])
                ->latest()
                ->get();

            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('title', function ($row) {
                    return '<strong>' . e($row->title) . '</strong>';
                })
                ->addColumn('short_title', function ($row) {
                    return e($row->short_title ?? '-');
                })
                ->addColumn('resources_count', function ($row) {
                    return '<span class="badge bg-success">' . $row->resources_count . '</span>';
                })
                ->addColumn('exams_count', function ($row) {
                    return '<span class="badge bg-warning text-dark">' . $row->exams_count . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    if ($row->resources_count > 0 || $row->exams_count > 0) {
                        $action .= '<a href="' . route('admin.catalogue-certifications.edit', $row->id) . '"
                                        class="btn btn-sm btn-primary me-1">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </a>';
                        $action .= '<form action="' . route('admin.catalogue-certifications.destroy', $row->id) . '"
                                        method="POST" style="display:inline-block;" class="delete-form">';
                        $action .= csrf_field() . method_field('DELETE');
                        $action .= '<button type="button" class="btn btn-sm btn-danger delete-button">
                                        <i class="fa-regular fa-trash-can"></i> Delete
                                    </button>
                                </form>';
                    } else {
                        $action .= '<a href="' . route('admin.catalogue-certifications.create', ['catalogue_id' => $row->id]) . '"
                                        class="btn btn-sm btn-success me-1">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </a>';
                    }
                    return $action;
                })
                ->rawColumns(['title', 'resources_count', 'exams_count', 'action'])
                ->make(true);
        }

        return view('backend.layouts.catalogue_certifications.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $catalogues = Catalogue::where('service_type', 'Certification')
            ->orderBy('title')
            ->get();
        $selectedCatalogueId = $request->query('catalogue_id');

        return view('backend.layouts.catalogue_certifications.create', compact('catalogues', 'selectedCatalogueId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        info('Certification store request data:', $request->all());

        // Normalize is_premium checkboxes from jquery.repeater
        if ($request->has('exams')) {
            $exams = $request->input('exams');
            foreach ($exams as $key => $val) {
                if (isset($val['is_premium'])) {
                    if (is_array($val['is_premium'])) {
                        $exams[$key]['is_premium'] = in_array('1', $val['is_premium']) || in_array(1, $val['is_premium']);
                    } else {
                        $exams[$key]['is_premium'] = (bool)$val['is_premium'];
                    }
                } else {
                    $exams[$key]['is_premium'] = false;
                }
            }
            $request->merge(['exams' => $exams]);
        }

        if ($request->has('resources')) {
            $resources = $request->input('resources');
            foreach ($resources as $key => $val) {
                if (isset($val['is_premium'])) {
                    if (is_array($val['is_premium'])) {
                        $resources[$key]['is_premium'] = in_array('1', $val['is_premium']) || in_array(1, $val['is_premium']);
                    } else {
                        $resources[$key]['is_premium'] = (bool)$val['is_premium'];
                    }
                } else {
                    $resources[$key]['is_premium'] = false;
                }
            }
            $request->merge(['resources' => $resources]);
        }

        $request->validate([
            'catalogue_id' => 'required|exists:catalogues,id',
            'resources' => 'nullable|array',
            'resources.*.resource_title' => 'required|string|max:255',
            'resources.*.resource_file' => 'required|file|max:20480',
            'resources.*.is_premium' => 'nullable|boolean',
            'exams' => 'nullable|array',
            'exams.*.exam_title' => 'required|string|max:255',
            'exams.*.exam_link' => 'nullable|string|max:2048',
            'exams.*.pass_mark' => 'nullable|numeric|min:0|max:100',
            'exams.*.is_premium' => 'nullable|boolean',
        ]);

        $catalogue = Catalogue::findOrFail($request->catalogue_id);

        if ($catalogue->resources()->exists() || $catalogue->exams()->exists()) {
            return redirect()->back()->withInput()->with('error', 'This certification already has resources or exams configured. Please edit them instead.');
        }

        // Save Resources if resources setup is enabled
        if ($request->has('resources_enabled') && $request->has('resources')) {
            foreach ($request->resources as $index => $resourceData) {
                if (empty($resourceData['resource_title'])) {
                    continue;
                }

                $filePath = null;
                $fileInput = $request->file("resources.{$index}.resource_file");
                if ($fileInput) {
                    $filePath = MiaHelper::uploadFile($fileInput, 'catalogue-resources');
                }

                $catalogue->resources()->create([
                    'resource_title' => $resourceData['resource_title'],
                    'resource_file' => $filePath,
                    'is_premium' => !empty($resourceData['is_premium']) ? 1 : 0,
                ]);
            }
        }

        // Save Exams
        if ($request->has('exams')) {
            foreach ($request->exams as $index => $examData) {
                if (empty($examData['exam_title'])) {
                    continue;
                }

                $catalogue->exams()->create([
                    'exam_title' => $examData['exam_title'],
                    'exam_link' => $examData['exam_link'] ?? null,
                    'pass_mark' => $examData['pass_mark'] ?? null,
                    'is_premium' => !empty($examData['is_premium']) ? 1 : 0,
                ]);
            }
        }

        return redirect()->route('admin.catalogue-certifications.index')
            ->with('success', 'Catalogue Certification details saved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $catalogue = Catalogue::with(['resources', 'exams'])->findOrFail($id);
        return view('backend.layouts.catalogue_certifications.edit', compact('catalogue'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        info('Certification update request data:', $request->all());

        // Normalize is_premium checkboxes from jquery.repeater
        if ($request->has('exams')) {
            $exams = $request->input('exams');
            foreach ($exams as $key => $val) {
                if (isset($val['is_premium'])) {
                    if (is_array($val['is_premium'])) {
                        $exams[$key]['is_premium'] = in_array('1', $val['is_premium']) || in_array(1, $val['is_premium']);
                    } else {
                        $exams[$key]['is_premium'] = (bool)$val['is_premium'];
                    }
                } else {
                    $exams[$key]['is_premium'] = false;
                }
            }
            $request->merge(['exams' => $exams]);
        }

        if ($request->has('resources')) {
            $resources = $request->input('resources');
            foreach ($resources as $key => $val) {
                if (isset($val['is_premium'])) {
                    if (is_array($val['is_premium'])) {
                        $resources[$key]['is_premium'] = in_array('1', $val['is_premium']) || in_array(1, $val['is_premium']);
                    } else {
                        $resources[$key]['is_premium'] = (bool)$val['is_premium'];
                    }
                } else {
                    $resources[$key]['is_premium'] = false;
                }
            }
            $request->merge(['resources' => $resources]);
        }

        $catalogue = Catalogue::findOrFail($id);

        $request->validate([
            'resources' => 'nullable|array',
            'resources.*.resource_title' => 'required|string|max:255',
            'resources.*.resource_file' => 'nullable|file|max:20480',
            'resources.*.is_premium' => 'nullable|boolean',
            'exams' => 'nullable|array',
            'exams.*.exam_title' => 'required|string|max:255',
            'exams.*.exam_link' => 'nullable|string|max:2048',
            'exams.*.pass_mark' => 'nullable|numeric|min:0|max:100',
            'exams.*.is_premium' => 'nullable|boolean',
        ]);

        // Handle Resources (Only if resources setup was active/enabled in request)
        if ($request->has('resources_enabled')) {
            $submittedResourceIds = [];
            if ($request->has('resources')) {
                foreach ($request->resources as $index => $resourceData) {
                    if (empty($resourceData['resource_title'])) {
                        continue;
                    }

                    $resourceId = $resourceData['id'] ?? null;
                    $resource = $resourceId ? CatalogueResource::find($resourceId) : null;
                    $filePath = $resource ? $resource->resource_file : null;

                    if (isset($resourceData['remove_resource_file']) && $resourceData['remove_resource_file'] == 1) {
                        if ($resource) {
                            MiaHelper::deleteFile($resource->resource_file);
                        }
                        $filePath = null;
                    }

                    $fileInput = $request->file("resources.{$index}.resource_file");
                    if ($fileInput) {
                        if ($resource && $resource->resource_file) {
                            $filePath = MiaHelper::updateFile($resource->resource_file, $fileInput, 'catalogue-resources');
                        } else {
                            $filePath = MiaHelper::uploadFile($fileInput, 'catalogue-resources');
                        }
                    }

                    if ($resource) {
                        $resource->update([
                            'resource_title' => $resourceData['resource_title'],
                            'resource_file' => $filePath,
                            'is_premium' => !empty($resourceData['is_premium']) ? 1 : 0,
                        ]);
                        $submittedResourceIds[] = $resource->id;
                    } else {
                        $newResource = $catalogue->resources()->create([
                            'resource_title' => $resourceData['resource_title'],
                            'resource_file' => $filePath,
                            'is_premium' => !empty($resourceData['is_premium']) ? 1 : 0,
                        ]);
                        $submittedResourceIds[] = $newResource->id;
                    }
                }
            }

            // Delete removed resources
            $catalogue->resources()->whereNotIn('id', $submittedResourceIds)->get()->each(function ($resource) {
                MiaHelper::deleteFile($resource->resource_file);
                $resource->delete();
            });
        }

        // Handle Exams
        $submittedExamIds = [];
        if ($request->has('exams')) {
            foreach ($request->exams as $index => $examData) {
                if (empty($examData['exam_title'])) {
                    continue;
                }

                $examId = $examData['id'] ?? null;
                $exam = $examId ? CatalogueExam::find($examId) : null;

                if ($exam) {
                    $exam->update([
                        'exam_title' => $examData['exam_title'],
                        'exam_link' => $examData['exam_link'] ?? null,
                        'pass_mark' => $examData['pass_mark'] ?? null,
                        'is_premium' => !empty($examData['is_premium']) ? 1 : 0,
                    ]);
                    $submittedExamIds[] = $exam->id;
                } else {
                    $newExam = $catalogue->exams()->create([
                        'exam_title' => $examData['exam_title'],
                        'exam_link' => $examData['exam_link'] ?? null,
                        'pass_mark' => $examData['pass_mark'] ?? null,
                        'is_premium' => !empty($examData['is_premium']) ? 1 : 0,
                    ]);
                    $submittedExamIds[] = $newExam->id;
                }
            }
        }

        // Delete removed exams
        $catalogue->exams()->whereNotIn('id', $submittedExamIds)->get()->each(function ($exam) {
            $exam->delete();
        });

        return redirect()->route('admin.catalogue-certifications.index')
            ->with('success', 'Catalogue Certifications updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $catalogue = Catalogue::findOrFail($id);

        $catalogue->resources->each(function ($resource) {
            MiaHelper::deleteFile($resource->resource_file);
            $resource->delete();
        });

        $catalogue->exams->each(function ($exam) {
            $exam->delete();
        });

        return redirect()->route('admin.catalogue-certifications.index')
            ->with('success', 'Catalogue Certification details deleted successfully.');
    }
}
