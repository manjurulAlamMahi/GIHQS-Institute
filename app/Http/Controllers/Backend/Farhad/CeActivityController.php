<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\CeActivity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CeActivityController extends Controller
{
    /**
     * Display a listing of all CE Activities.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $activities = CeActivity::with(['user', 'certification'])->latest('id');

            return DataTables::of($activities)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    return $row->user ? '<strong>' . e($row->user->full_name) . '</strong>' : 'N/A';
                })
                ->addColumn('certification', function ($row) {
                    return $row->certification ? e($row->certification->short_title) : 'N/A';
                })
                ->addColumn('domain', function ($row) {
                    return e($row->domain);
                })
                ->addColumn('activity_type', function ($row) {
                    return e($row->activity_type);
                })
                ->editColumn('completion_date', function ($row) {
                    return $row->completion_date ? $row->completion_date->format('Y-m-d') : 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $status = $row->status ? trim($row->status) : 'pending';
                    $badgeClass = 'bg-secondary';

                    $lowerStatus = strtolower($status);
                    if ($lowerStatus == 'pending') $badgeClass = 'bg-danger';
                    if ($lowerStatus == 'approved') $badgeClass = 'bg-success';
                    if ($lowerStatus == 'rejected') $badgeClass = 'bg-dark';

                    // Human readable display
                    $displayStatus = $lowerStatus == 'pending' ? 'Pending Review' : ucfirst($lowerStatus);

                    return '<span class="badge ' . $badgeClass . '">' . $displayStatus . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.ce-activities.show', $row->id) . '" class="btn btn-sm btn-info me-1" title="View CE Activity">
                                <i class="fa-regular fa-eye"></i>
                            </a>';
                    $action .= '<form action="' . route('admin.ce-activities.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger delete-button" title="Delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>';

                    return $action;
                })
                ->rawColumns(['user_name', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.ce_activities.index');
    }

    /**
     * Display the specified CE Activity details.
     */
    public function show($id)
    {
        $activity = CeActivity::with(['user', 'certification'])->findOrFail($id);
        return view('backend.layouts.ce_activities.show', compact('activity'));
    }

    /**
     * Update status and admin notes of the CE Activity.
     */
    public function updateStatus(Request $request, $id)
    {
        $activity = CeActivity::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $activity->status = $request->status;
        $activity->admin_notes = $request->admin_notes;
        $activity->save();

        return back()->with('success', 'CE Activity status and notes updated successfully');
    }

    /**
     * Remove the specified CE Activity from storage.
     */
    public function destroy($id)
    {
        $activity = CeActivity::findOrFail($id);
        $activity->delete();
        return redirect()->route('admin.ce-activities.index')->with('success', 'CE Activity deleted successfully');
    }
}
