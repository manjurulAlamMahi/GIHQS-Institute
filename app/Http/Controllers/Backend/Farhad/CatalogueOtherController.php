<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\Catalogue;
use App\Models\CatalogueResource;
use App\Models\CatalogueExam;
use App\Models\CatalogueLiveLink;
use App\Models\CatalogueVideo;
use App\Models\CatalogueVideoLink;
use App\Models\Exam;
use App\Helpers\MiaHelper;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CatalogueOtherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Catalogue::where('service_type', '!=', 'Certification')
                ->withCount(['resources', 'exams', 'liveLinks', 'videos', 'videoLinks'])
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
                ->addColumn('exams_count', function ($row) {
                    return '<span class="badge bg-warning text-dark">' . $row->exams_count . '</span>';
                })
                ->addColumn('resources_count', function ($row) {
                    return '<span class="badge bg-success">' . $row->resources_count . '</span>';
                })
                ->addColumn('live_links_count', function ($row) {
                    return '<span class="badge bg-info">' . $row->live_links_count . '</span>';
                })
                ->addColumn('videos_count', function ($row) {
                    return '<span class="badge bg-primary">' . $row->videos_count . '</span>';
                })
                ->addColumn('video_links_count', function ($row) {
                    return '<span class="badge bg-secondary">' . $row->video_links_count . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $hasRelations = ($row->resources_count > 0 || $row->exams_count > 0 || $row->live_links_count > 0 || $row->videos_count > 0 || $row->video_links_count > 0);
                    if ($hasRelations) {
                        $action .= '<a href="' . route('admin.catalogue-others.edit', $row->id) . '"
                                        class="btn btn-sm btn-primary me-1">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </a>';
                        $action .= '<form action="' . route('admin.catalogue-others.destroy', $row->id) . '"
                                        method="POST" style="display:inline-block;" class="delete-form">';
                        $action .= csrf_field() . method_field('DELETE');
                        $action .= '<button type="button" class="btn btn-sm btn-danger delete-button">
                                        <i class="fa-regular fa-trash-can"></i> Delete
                                    </button>
                                </form>';
                    } else {
                        $action .= '<a href="' . route('admin.catalogue-others.create', ['catalogue_id' => $row->id]) . '"
                                        class="btn btn-sm btn-success me-1">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </a>';
                    }
                    return $action;
                })
                ->rawColumns(['title', 'exams_count', 'resources_count', 'live_links_count', 'videos_count', 'video_links_count', 'action'])
                ->make(true);
        }

        return view('backend.layouts.catalogue_others.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $catalogues = Catalogue::where('service_type', '!=', 'Certification')
            ->orderBy('title')
            ->get();
        $publishedExams = Exam::where('status', 'published')
            ->orderBy('name')
            ->get();
        $selectedCatalogueId = $request->query('catalogue_id');

        return view('backend.layouts.catalogue_others.create', compact('catalogues', 'publishedExams', 'selectedCatalogueId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        info('Catalogue Other store request data:', $request->all());

        $request->validate([
            'catalogue_id' => 'required|exists:catalogues,id',
            
            'exams' => 'nullable|array',
            'exams.*.exam_id' => 'required|exists:exams,id',
            'exams.*.pass_mark' => 'nullable|numeric|min:0|max:100',
            
            'resources' => 'nullable|array',
            'resources.*.resource_title' => 'required|string|max:255',
            'resources.*.resource_file' => 'required|file|max:20480',
            
            'live_links' => 'nullable|array',
            'live_links.*.link_title' => 'nullable|string|max:255',
            'live_links.*.link_url' => 'required|url|max:2048',
            
            'videos' => 'nullable|array',
            'videos.*.video_title' => 'nullable|string|max:255',
            'videos.*.video_file' => 'required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:102400',
            'videos.*.thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            
            'video_links' => 'nullable|array',
            'video_links.*.video_link_title' => 'nullable|string|max:255',
            'video_links.*.video_link_url' => 'required|url|max:2048',
        ]);

        $catalogue = Catalogue::findOrFail($request->catalogue_id);

        // Check if any configuration already exists
        $hasAny = $catalogue->exams()->exists() ||
                  $catalogue->resources()->exists() ||
                  $catalogue->liveLinks()->exists() ||
                  $catalogue->videos()->exists() ||
                  $catalogue->videoLinks()->exists();

        if ($hasAny) {
            return redirect()->back()->withInput()->with('error', 'This catalogue already has details configured. Please edit them instead.');
        }

        // 1. Save Exams
        if ($request->has('exams')) {
            foreach ($request->exams as $examData) {
                if (empty($examData['exam_id'])) continue;
                
                $localExam = Exam::find($examData['exam_id']);
                if ($localExam) {
                    $catalogue->exams()->create([
                        'exam_id' => $localExam->id,
                        'exam_title' => $localExam->name,
                        'pass_mark' => $examData['pass_mark'] ?? null,
                        'is_premium' => 0,
                    ]);
                }
            }
        }

        // 2. Save Resources
        if ($request->has('resources')) {
            foreach ($request->resources as $index => $resourceData) {
                if (empty($resourceData['resource_title'])) continue;

                $filePath = null;
                $fileInput = $request->file("resources.{$index}.resource_file");
                if ($fileInput) {
                    $filePath = MiaHelper::uploadFile($fileInput, 'catalogue-resources');
                }

                $catalogue->resources()->create([
                    'resource_title' => $resourceData['resource_title'],
                    'resource_file' => $filePath,
                    'is_premium' => 0,
                ]);
            }
        }

        // 3. Save Live Links
        if ($request->has('live_links')) {
            foreach ($request->live_links as $item) {
                if (empty($item['link_url'])) continue;
                $catalogue->liveLinks()->create([
                    'link_title' => $item['link_title'] ?? null,
                    'link_url' => $item['link_url'],
                ]);
            }
        }

        // 4. Save Videos (Files)
        if ($request->has('videos')) {
            foreach ($request->videos as $index => $item) {
                $fileInput = $request->file("videos.{$index}.video_file");
                if ($fileInput) {
                    $filePath = MiaHelper::uploadFile($fileInput, 'catalogue-videos');

                    $thumbnailPath = null;
                    $thumbnailInput = $request->file("videos.{$index}.thumbnail");
                    if ($thumbnailInput) {
                        $thumbnailPath = MiaHelper::uploadFile($thumbnailInput, 'catalogue-video-thumbnails');
                    }

                    $catalogue->videos()->create([
                        'video_title' => $item['video_title'] ?? null,
                        'video_file' => $filePath,
                        'thumbnail' => $thumbnailPath,
                    ]);
                }
            }
        }

        // 5. Save Video Links
        if ($request->has('video_links')) {
            foreach ($request->video_links as $item) {
                if (empty($item['video_link_url'])) continue;
                $catalogue->videoLinks()->create([
                    'video_link_title' => $item['video_link_title'] ?? null,
                    'video_link_url' => $item['video_link_url'],
                ]);
            }
        }

        return redirect()->route('admin.catalogue-others.index')
            ->with('success', 'Catalogue details saved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $catalogue = Catalogue::with(['resources', 'exams.localExam', 'liveLinks', 'videos', 'videoLinks'])->findOrFail($id);
        $publishedExams = Exam::where('status', 'published')
            ->orderBy('name')
            ->get();

        return view('backend.layouts.catalogue_others.edit', compact('catalogue', 'publishedExams'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        info('Catalogue Other update request data:', $request->all());

        $request->validate([
            'exams' => 'nullable|array',
            'exams.*.exam_id' => 'required|exists:exams,id',
            'exams.*.pass_mark' => 'nullable|numeric|min:0|max:100',
            
            'resources' => 'nullable|array',
            'resources.*.resource_title' => 'required|string|max:255',
            'resources.*.resource_file' => 'nullable|file|max:20480',
            
            'live_links' => 'nullable|array',
            'live_links.*.link_title' => 'nullable|string|max:255',
            'live_links.*.link_url' => 'required|url|max:2048',
            
            'videos' => 'nullable|array',
            'videos.*.video_title' => 'nullable|string|max:255',
            'videos.*.video_file' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:102400',
            'videos.*.thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            
            'video_links' => 'nullable|array',
            'video_links.*.video_link_title' => 'nullable|string|max:255',
            'video_links.*.video_link_url' => 'required|url|max:2048',
        ]);

        $catalogue = Catalogue::findOrFail($id);

        // 1. Handle Exams
        $submittedExamIds = [];
        if ($request->has('exams')) {
            foreach ($request->exams as $examData) {
                if (empty($examData['exam_id'])) continue;
                
                $examRecordId = $examData['id'] ?? null;
                $exam = $examRecordId ? CatalogueExam::find($examRecordId) : null;
                $localExam = Exam::find($examData['exam_id']);

                if ($localExam) {
                    if ($exam) {
                        $exam->update([
                            'exam_id' => $localExam->id,
                            'exam_title' => $localExam->name,
                            'pass_mark' => $examData['pass_mark'] ?? null,
                        ]);
                        $submittedExamIds[] = $exam->id;
                    } else {
                        $newExam = $catalogue->exams()->create([
                            'exam_id' => $localExam->id,
                            'exam_title' => $localExam->name,
                            'pass_mark' => $examData['pass_mark'] ?? null,
                            'is_premium' => 0,
                        ]);
                        $submittedExamIds[] = $newExam->id;
                    }
                }
            }
        }
        $catalogue->exams()->whereNotIn('id', $submittedExamIds)->delete();

        // 2. Handle Resources
        $submittedResourceIds = [];
        if ($request->has('resources')) {
            foreach ($request->resources as $index => $resourceData) {
                if (empty($resourceData['resource_title'])) continue;

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
                    ]);
                    $submittedResourceIds[] = $resource->id;
                } else {
                    $newResource = $catalogue->resources()->create([
                        'resource_title' => $resourceData['resource_title'],
                        'resource_file' => $filePath,
                        'is_premium' => 0,
                    ]);
                    $submittedResourceIds[] = $newResource->id;
                }
            }
        }
        $catalogue->resources()->whereNotIn('id', $submittedResourceIds)->get()->each(function ($resource) {
            MiaHelper::deleteFile($resource->resource_file);
            $resource->delete();
        });

        // 3. Handle Live Links
        $submittedLinkIds = [];
        if ($request->has('live_links')) {
            foreach ($request->live_links as $item) {
                if (empty($item['link_url'])) continue;
                
                $linkId = $item['id'] ?? null;
                $link = $linkId ? CatalogueLiveLink::find($linkId) : null;

                if ($link) {
                    $link->update([
                        'link_title' => $item['link_title'] ?? null,
                        'link_url' => $item['link_url'],
                    ]);
                    $submittedLinkIds[] = $link->id;
                } else {
                    $newLink = $catalogue->liveLinks()->create([
                        'link_title' => $item['link_title'] ?? null,
                        'link_url' => $item['link_url'],
                    ]);
                    $submittedLinkIds[] = $newLink->id;
                }
            }
        }
        $catalogue->liveLinks()->whereNotIn('id', $submittedLinkIds)->delete();

        // 4. Handle Videos
        $submittedVideoIds = [];
        if ($request->has('videos')) {
            foreach ($request->videos as $index => $item) {
                $videoId = $item['id'] ?? null;
                $video = $videoId ? CatalogueVideo::find($videoId) : null;
                $filePath = $video ? $video->video_file : null;
                $thumbnailPath = $video ? $video->thumbnail : null;

                if (isset($item['remove_video_file']) && $item['remove_video_file'] == 1) {
                    if ($video) {
                        MiaHelper::deleteFile($video->video_file);
                    }
                    $filePath = null;
                }

                if (isset($item['remove_thumbnail']) && $item['remove_thumbnail'] == 1) {
                    if ($video && $video->thumbnail) {
                        MiaHelper::deleteFile($video->thumbnail);
                    }
                    $thumbnailPath = null;
                }

                $fileInput = $request->file("videos.{$index}.video_file");
                if ($fileInput) {
                    if ($video && $video->video_file) {
                        $filePath = MiaHelper::updateFile($video->video_file, $fileInput, 'catalogue-videos');
                    } else {
                        $filePath = MiaHelper::uploadFile($fileInput, 'catalogue-videos');
                    }
                }

                $thumbnailInput = $request->file("videos.{$index}.thumbnail");
                if ($thumbnailInput) {
                    if ($video && $video->thumbnail) {
                        $thumbnailPath = MiaHelper::updateFile($video->thumbnail, $thumbnailInput, 'catalogue-video-thumbnails');
                    } else {
                        $thumbnailPath = MiaHelper::uploadFile($thumbnailInput, 'catalogue-video-thumbnails');
                    }
                }

                if ($filePath) {
                    if ($video) {
                        $video->update([
                            'video_title' => $item['video_title'] ?? null,
                            'video_file' => $filePath,
                            'thumbnail' => $thumbnailPath,
                        ]);
                        $submittedVideoIds[] = $video->id;
                    } else {
                        $newVideo = $catalogue->videos()->create([
                            'video_title' => $item['video_title'] ?? null,
                            'video_file' => $filePath,
                            'thumbnail' => $thumbnailPath,
                        ]);
                        $submittedVideoIds[] = $newVideo->id;
                    }
                }
            }
        }
        $catalogue->videos()->whereNotIn('id', $submittedVideoIds)->get()->each(function ($video) {
            MiaHelper::deleteFile($video->video_file);
            if ($video->thumbnail) {
                MiaHelper::deleteFile($video->thumbnail);
            }
            $video->delete();
        });

        // 5. Handle Video Links
        $submittedVideoLinkIds = [];
        if ($request->has('video_links')) {
            foreach ($request->video_links as $item) {
                if (empty($item['video_link_url'])) continue;
                
                $linkId = $item['id'] ?? null;
                $vLink = $linkId ? CatalogueVideoLink::find($linkId) : null;

                if ($vLink) {
                    $vLink->update([
                        'video_link_title' => $item['video_link_title'] ?? null,
                        'video_link_url' => $item['video_link_url'],
                    ]);
                    $submittedVideoLinkIds[] = $vLink->id;
                } else {
                    $newVLink = $catalogue->videoLinks()->create([
                        'video_link_title' => $item['video_link_title'] ?? null,
                        'video_link_url' => $item['video_link_url'],
                    ]);
                    $submittedVideoLinkIds[] = $newVLink->id;
                }
            }
        }
        $catalogue->videoLinks()->whereNotIn('id', $submittedVideoLinkIds)->delete();

        return redirect()->route('admin.catalogue-others.index')
            ->with('success', 'Catalogue details updated successfully.');
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

        $catalogue->liveLinks()->delete();

        $catalogue->videos->each(function ($video) {
            MiaHelper::deleteFile($video->video_file);
            if ($video->thumbnail) {
                MiaHelper::deleteFile($video->thumbnail);
            }
            $video->delete();
        });

        $catalogue->videoLinks()->delete();

        return redirect()->route('admin.catalogue-others.index')
            ->with('success', 'Catalogue other details deleted successfully.');
    }
}
