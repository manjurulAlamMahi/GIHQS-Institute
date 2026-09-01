<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use App\Mail\ContactMessageReplyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $contactMessages = ContactMessage::query()->latest('id');

            return DataTables::of($contactMessages)
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
                ->addColumn('organization', function ($row) {
                    return $row->organization ? e($row->organization) : 'N/A';
                })
                ->addColumn('service_of_interest', function ($row) {
                    return $row->service_of_interest ? e($row->service_of_interest) : 'N/A';
                })
                ->addColumn('message', function ($row) {
                    return str(e($row->message))->limit(50);
                })
                ->editColumn('status', function ($row) {
                    $status = $row->status ? trim($row->status) : 'pending';
                    $badgeClass = 'bg-secondary';

                    $lowerStatus = strtolower($status);
                    if ($lowerStatus == 'pending') $badgeClass = 'bg-danger';
                    if ($lowerStatus == 'replied') $badgeClass = 'bg-warning';
                    if ($lowerStatus == 'canceled') $badgeClass = 'bg-dark';
                    if ($lowerStatus == 'completed') $badgeClass = 'bg-success';

                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    $action .= '<a href="' . route('admin.contact-messages.show', $row->id) . '" class="btn btn-sm btn-info me-1" title="View Message">
                                <i class="fa-regular fa-eye"></i>
                            </a>';
                    $action .= '<form action="' . route('admin.contact-messages.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger delete-button" title="Delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>';
                    return $action;
                })
                ->rawColumns(['name', 'email', 'phone', 'organization', 'service_of_interest', 'message', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.contact_messages.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contactMessage = ContactMessage::with('replies')->findOrFail($id);
        return view('backend.layouts.contact_messages.show', compact('contactMessage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $contactMessage = ContactMessage::with('replies')->findOrFail($id);
        return view('backend.layouts.contact_messages.edit', compact('contactMessage'));
    }

    /**
     * Send email reply to the contact message and log it.
     */
    public function reply(Request $request, $id)
    {
        $contactMessage = ContactMessage::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'reply_message' => 'required|string',
            'status' => 'nullable|in:pending,replied,canceled,completed',
        ]);

        try {
            Mail::to($contactMessage->email)
                ->send(new ContactMessageReplyMail($contactMessage, $validated['subject'], $validated['reply_message']));
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::error('Contact message reply mail failed.', [
                'contact_message_id' => $contactMessage->id,
                'recipient_email' => $contactMessage->email,
                'subject' => $validated['subject'],
                'exception_class' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);

            return back()->withInput()->with(
                'error',
                'Mail address is not correct or not reachable, so email could not be sent.'
            );
        }

        $updatedStatus = $validated['status'] ?? 'replied';

        $contactMessage->update([
            'status' => $updatedStatus,
        ]);

        ContactMessageReply::create([
            'contact_message_id' => $contactMessage->id,
            'subject' => $validated['subject'],
            'message' => $validated['reply_message'],
            'status' => $updatedStatus,
        ]);

        return redirect()->route('admin.contact-messages.show', $contactMessage->id)
            ->with('success', 'Reply sent successfully and status updated.');
    }

    /**
     * Update status of the contact message.
     */
    public function updateStatus(Request $request, $id)
    {
        $contactMessage = ContactMessage::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,replied,canceled,completed',
        ]);

        $contactMessage->status = $request->status;
        $contactMessage->save();

        return back()->with('success', 'Status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contactMessage = ContactMessage::findOrFail($id);
        $contactMessage->delete();
        return redirect()->back()->with('success', 'Contact message deleted successfully');
    }
}
