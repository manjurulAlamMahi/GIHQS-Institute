<?php

namespace App\Http\Controllers\Backend\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class ProfileSettingController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('backend.layouts.settings.profile_settings', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Prevent unauthorized updates
        if ($user->id != $id) {
            abort(403, 'Unauthorized action.');
        }

        // Validation
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5048',
        ]);

        // Update basic info
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->country = $request->country;
        $user->username = $request->username;
        $user->email = $request->email;

        // -------------------------
        // Avatar update / keep / remove
        // -------------------------
        $avatarUrl = $user->avatar; // default: keep old avatar

        if ($request->hasFile('avatar')) {
            // Delete old if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            // Upload new
            $avatar     = $request->file('avatar');
            $avatarName = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            $directory  = 'uploads/users/';

            if (!file_exists(public_path($directory))) {
                mkdir(public_path($directory), 0755, true);
            }

            // Resize + save
            $resizedAvatar = Image::make($avatar)->resize(300, 300);
            $resizedAvatar->save(public_path($directory . $avatarName));

            $avatarUrl = $directory . $avatarName;
        }

        // If explicitly removed via Dropify
        if ($request->remove_avatar == 1) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
            $avatarUrl = null;
        }

        $user->avatar = $avatarUrl;

        $user->save();

        return redirect()
            ->route('admin.profile-settings.edit')
            ->with(['success' => 'Profile updated successfully!', 'active_tab' => 'profile']);
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.'])->withInput()->with('active_tab', 'password');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()
            ->route('admin.profile-settings.edit')
            ->with(['success' => 'Password changed successfully!', 'active_tab' => 'password']);
    }
}
