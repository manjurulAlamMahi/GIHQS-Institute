<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class ManagerController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role == 'manager') {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have access to this module.');
        }

        if ($request->ajax()) {
            $users = User::where('role', 'manager')
                ->latest()
                ->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if ($row->avatar) {
                        return '<img src="' . asset($row->avatar) . '" width="40" height="40" class="rounded-circle shadow-sm border"/>';
                    }
                    return '<span class="badge bg-light text-muted border">No Image</span>';
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '<button type="button" class="btn btn-sm btn-primary edit-button me-1"
                        data-id="' . $row->id . '"
                        data-first_name="' . $row->first_name . '"
                        data-last_name="' . $row->last_name . '"
                        data-country="' . $row->country . '"
                        data-username="' . $row->username . '"
                        data-email="' . $row->email . '"
                        title="Edit">
                        <i class="ri-pencil-line"></i>
                    </button>';

                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger delete-button" data-id="' . $row->id . '" title="Delete">
                        <i class="ri-delete-bin-line"></i>
                    </button>';

                    return $editBtn . $deleteBtn;
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('backend.layouts.settings.manager_settings');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role == 'manager') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'country' => $request->country,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'manager', // Manager role
            'status' => 1, // Active by default
        ]);

        return response()->json(['success' => true, 'message' => 'Admin user created successfully!']);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role == 'manager') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'current_password' => $request->filled('password') ? 'required' : 'nullable',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // If password is being changed, verify current admin's password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, auth()->user()->password)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['current_password' => ['The provided current password does not match our records.']]
                ], 422);
            }
            $user->password = Hash::make($request->password);
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->country = $request->country;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Manager updated successfully!']);
    }

    public function destroy($id)
    {
        if (auth()->user()->role == 'manager') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Admin user deleted successfully!']);
    }
}
