<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\CertificationApplication;
use App\Models\UserExamOverride;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Mail\CertificationStatusMail;
use App\Traits\LogsEmails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CertificationApplicationController extends Controller
{
    use LogsEmails;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $applications = CertificationApplication::with('catalogue')->latest('id');

            return DataTables::of($applications)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<strong>' . e($row->name) . '</strong>';
                })
                ->addColumn('email', function ($row) {
                    return e($row->email);
                })
                ->addColumn('phone', function ($row) {
                    return $row->phone ? e($row->phone) : 'N/A';
                })
                ->addColumn('certification', function ($row) {
                    return $row->catalogue ? e($row->catalogue->title) : 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $status = $row->status ? trim($row->status) : 'pending';
                    $badgeClass = 'bg-secondary';

                    $lowerStatus = strtolower($status);
                    if ($lowerStatus == 'pending') $badgeClass = 'bg-danger';
                    if ($lowerStatus == 'accepted') $badgeClass = 'bg-success';
                    if ($lowerStatus == 'canceled') $badgeClass = 'bg-dark';
                    if ($lowerStatus == 'completed') $badgeClass = 'bg-primary';

                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.certification-applications.show', $row->id) . '" class="btn btn-sm btn-info me-1" title="View Application">
                                <i class="fa-regular fa-eye"></i>
                            </a>';
                    $action .= '<form action="' . route('admin.certification-applications.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger delete-button" title="Delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>';

                    return $action;
                })
                ->rawColumns(['name', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.certification_applications.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $application = CertificationApplication::with('catalogue')->findOrFail($id);
        return view('backend.layouts.certification_applications.show', compact('application'));
    }

    /**
     * Update status of the certification application.
     */
    public function updateStatus(Request $request, $id)
    {
        $application = CertificationApplication::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,accepted,canceled,completed',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $application->status;
        $application->status = $request->status;
        $application->admin_notes = $request->admin_notes;
        $application->save();

        if ($oldStatus !== $application->status) {
            if (in_array($application->status, ['accepted', 'canceled'])) {
                $paymentLink = null;
                if ($application->status === 'accepted') {
                    $paymentLink = env('FRONTEND_URL', 'https://gihqs.vercel.app') . '/dashboard/professional-development?catalogue_id=' . $application->catalogue_id;
                }

                try {
                    $mail = new CertificationStatusMail(
                        $application,
                        $application->status,
                        $application->admin_notes,
                        $paymentLink
                    );
                    Mail::to($application->email)->send($mail);

                    // Log email to database
                    $this->logEmail(
                        $application->user_id,
                        $application->email,
                        'user',
                        $mail->envelope()->subject,
                        'certification_' . $application->status,
                        $application
                    );
                } catch (\Throwable $e) {
                    Log::error('Failed to send certification status email: ' . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Application status and notes updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $application = CertificationApplication::findOrFail($id);
        $application->delete();
        return redirect()->route('admin.certification-applications.index')->with('success', 'Certification application deleted successfully');
    }

    /**
     * Store or update an override setting for a user and catalogue exam.
     */
    public function storeOrUpdateOverride(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'application_id' => 'required|integer|exists:certification_applications,id',
            'submit_exam_id' => 'required|integer|exists:catalogue_exams,id',
            'overrides' => 'required|array',
        ]);

        $application = CertificationApplication::findOrFail($request->application_id);

        // Security/validation check: Must be accepted/completed and paid
        $isPaid = Purchase::where('user_id', $application->user_id)
            ->where('catalogue_id', $application->catalogue_id)
            ->where('purchase_type', 'catalogue')
            ->where('payment_status', 'paid')
            ->exists();

        if (!in_array($application->status, ['accepted', 'completed']) || !$isPaid) {
            return back()->with('error', 'This certification application is not approved and paid.');
        }

        $examId = $request->submit_exam_id;
        $overrideData = $request->overrides[$examId] ?? null;

        if ($overrideData) {
            $maxAttempts = ($overrideData['max_attempts'] !== '' && $overrideData['max_attempts'] !== null) 
                ? (int) $overrideData['max_attempts'] 
                : null;
            
            $ignoreCooldown = isset($overrideData['unlock_immediately']) && $overrideData['unlock_immediately'] == '1';
            
            $retakeEligibleDate = null;
            if (!$ignoreCooldown && !empty($overrideData['retake_eligible_date'])) {
                $retakeEligibleDate = $overrideData['retake_eligible_date'];
            }

            // Update or create override
            UserExamOverride::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'catalogue_exam_id' => $examId,
                ],
                [
                    'max_attempts' => $maxAttempts,
                    'retake_eligible_date' => $retakeEligibleDate,
                    'ignore_cooldown' => $ignoreCooldown,
                ]
            );

            return back()->with('success', 'Exam override saved successfully');
        }

        return back()->with('error', 'Failed to save override');
    }

    /**
     * Display a listing of approved and paid certification applications for overrides.
     */
    public function overridesIndex(Request $request)
    {
        if ($request->ajax()) {
            $applications = CertificationApplication::whereIn('status', ['accepted', 'completed'])
                ->whereNotNull('user_id')
                ->whereExists(function ($query) {
                    $query->select(\DB::raw(1))
                        ->from('purchases')
                        ->whereColumn('purchases.user_id', 'certification_applications.user_id')
                        ->whereColumn('purchases.catalogue_id', 'certification_applications.catalogue_id')
                        ->where('purchases.purchase_type', 'catalogue')
                        ->where('purchases.payment_status', 'paid');
                })
                ->with(['catalogue', 'user'])
                ->latest('id');

            return DataTables::of($applications)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<strong>' . e($row->name) . '</strong>';
                })
                ->addColumn('email', function ($row) {
                    return e($row->email);
                })
                ->addColumn('certification', function ($row) {
                    return $row->catalogue ? e($row->catalogue->title) : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.exam-overrides.show', $row->id) . '" class="btn btn-sm btn-success me-1" title="Manage Override">
                                <i class="fa-solid fa-gear me-1"></i> Manage Override
                            </a>';
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }

        return view('backend.layouts.certification_applications.overrides_index');
    }

    /**
     * Display the override settings for a specific approved and paid application.
     */
    public function overridesShow($id)
    {
        $application = CertificationApplication::with(['catalogue.exams', 'user'])->findOrFail($id);

        // Security check: Must be accepted/completed and paid
        $isPaid = Purchase::where('user_id', $application->user_id)
            ->where('catalogue_id', $application->catalogue_id)
            ->where('purchase_type', 'catalogue')
            ->where('payment_status', 'paid')
            ->exists();

        if (!in_array($application->status, ['accepted', 'completed']) || !$isPaid) {
            abort(403, 'This certification application is not approved and paid.');
        }

        return view('backend.layouts.certification_applications.overrides_show', compact('application'));
    }
}
