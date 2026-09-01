<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmailLogController extends Controller
{
    /**
     * Display a listing of email logs.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = EmailLog::with('user')->latest('id');

            return DataTables::of($logs)
                ->addIndexColumn()
                ->addColumn('recipient', function ($row) {
                    return e($row->recipient_email) . ' <span class="badge bg-light text-dark">' . ucfirst(e($row->recipient_role)) . '</span>';
                })
                ->addColumn('subject', function ($row) {
                    return '<strong>' . e($row->subject) . '</strong>';
                })
                ->editColumn('stage', function ($row) {
                    return '<span class="badge bg-info">' . str_replace('_', ' ', ucfirst(e($row->stage))) . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns(['recipient', 'subject', 'stage'])
                ->make(true);
        }

        return view('backend.layouts.email_logs.index');
    }
}
