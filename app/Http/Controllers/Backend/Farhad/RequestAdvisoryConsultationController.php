<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Http\Controllers\Controller;
use App\Models\RequestAdvisory;
use Illuminate\Http\Request;

class RequestAdvisoryConsultationController extends Controller
{
    /**
     * Show the form for editing the Request Advisory Consultation config.
     */
    public function edit()
    {
        $consultation = RequestAdvisory::firstOrCreate(
            [],
            [
                'title1' => 'Request Advisory',
                'title2' => 'Consultation',
                'tagline' => 'GIHQS CONSULTATION',
                'description' => 'Fill out the form below to request a strategic consultation session with the GIHQS Advisory Panel.',
            ]
        );

        return view('backend.layouts.request_advisory.edit', compact('consultation'));
    }

    /**
     * Update the Request Advisory Consultation config.
     */
    public function update(Request $request, $id)
    {
        $consultation = RequestAdvisory::findOrFail($id);

        $request->validate([
            'title1'      => 'required|string|max:255',
            'title2'      => 'nullable|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $consultation->update([
            'title1'      => $request->title1,
            'title2'      => $request->title2,
            'tagline'     => $request->tagline,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Request Advisory Consultation updated successfully.');
    }
}
