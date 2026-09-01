<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Helpers\MiaHelper;
use App\Http\Controllers\Controller;
use App\Models\AboutContact;
use Illuminate\Http\Request;

class AboutContactController extends Controller
{
    /**
     * Show the form for editing the About Contact page.
     * Always edits the first (only) record — creates one if it doesn't exist.
     */
    public function edit()
    {
        $aboutContact = AboutContact::firstOrCreate(
            [],
            ['title' => 'About Contact']
        );

        return view('backend.layouts.about_contact.edit', compact('aboutContact'));
    }

    /**
     * Update the About Contact page.
     */
    public function update(Request $request, $id)
    {
        $aboutContact = AboutContact::findOrFail($id);

        $request->validate([
            'title'           => 'required|string|max:255',
            'phone'           => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable|string',
            'working_hours'   => 'nullable|string',
            'mission'         => 'nullable|string',
            'content_file'    => 'nullable|file|mimes:html,txt|max:10240',
            'injected_status' => 'required|in:0,1',
        ]);

        // Handle content file upload
        $contentFilePath = $aboutContact->content_file;

        if ($request->remove_content_file == 1) {
            MiaHelper::deleteFile($aboutContact->content_file);
            $contentFilePath = null;
        } elseif ($request->hasFile('content_file')) {
            $contentFilePath = MiaHelper::updateFile(
                $aboutContact->content_file,
                $request->file('content_file'),
                'about_contact'
            );
        }

        $aboutContact->title = $request->title;
        $aboutContact->phone = $request->phone;
        $aboutContact->email = $request->email;
        $aboutContact->address = $request->address;
        $aboutContact->working_hours = $request->working_hours;
        $aboutContact->mission = $request->mission;
        $aboutContact->content_file = $contentFilePath;
        $aboutContact->injected_status = $request->injected_status;
        $aboutContact->save();

        return redirect()->route('admin.about-contact.edit')
            ->with('success', 'About Contact updated successfully.');
    }
}
