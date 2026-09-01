<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Helpers\MiaHelper;
use App\Http\Controllers\Controller;
use App\Models\AboutInstitute;
use App\Models\AboutInstituteFaq;
use Illuminate\Http\Request;

class AboutInstituteController extends Controller
{
    /**
     * Show the form for editing the About Institute page.
     * Always edits the first (only) record — creates one if it doesn't exist.
     */
    public function edit()
    {
        $aboutInstitute = AboutInstitute::with('faqs')->firstOrCreate(
            [],
            ['title1' => 'About Institute']
        );

        return view('backend.layouts.about_institute.edit', compact('aboutInstitute'));
    }

    /**
     * Update the About Institute page.
     */
    public function update(Request $request, $id)
    {
        $aboutInstitute = AboutInstitute::findOrFail($id);

        $request->validate([
            'title1'       => 'required|string|max:255',
            'title2'       => 'nullable|string|max:255',
            'tag_line'     => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'faqs'         => 'nullable|array',
            'faqs.*.faq_title'             => 'required|string|max:255',
            'faqs.*.faq_short_description' => 'nullable|string',
            'content_file' => 'nullable|file|mimes:html,txt|max:10240',
            'injected_status' => 'required|in:0,1',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $aboutInstitute->image = MiaHelper::updateFile(
                $aboutInstitute->image,
                $request->file('image'),
                'about_institute'
            );
        }

        // Handle content file upload
        $contentFilePath = $aboutInstitute->content_file;

        if ($request->remove_content_file == 1) {
            MiaHelper::deleteFile($aboutInstitute->content_file);
            $contentFilePath = null;
        } elseif ($request->hasFile('content_file')) {
            $contentFilePath = MiaHelper::updateFile(
                $aboutInstitute->content_file,
                $request->file('content_file'),
                'about_institute'
            );
        }

        $aboutInstitute->title1      = $request->title1;
        $aboutInstitute->title2      = $request->title2;
        $aboutInstitute->tag_line    = $request->tag_line;
        $aboutInstitute->description = $request->description;
        $aboutInstitute->content_file = $contentFilePath;
        $aboutInstitute->injected_status = $request->injected_status;
        $aboutInstitute->save();

        // Handle FAQs repeater
        $submittedFaqIds = [];

        if ($request->has('faqs')) {
            foreach ($request->faqs as $faqData) {
                if (empty($faqData['faq_title'])) {
                    continue;
                }

                $faqId = $faqData['id'] ?? null;
                $faq   = null;

                if ($faqId) {
                    $faq = AboutInstituteFaq::find($faqId);
                }

                if ($faq) {
                    $faq->update([
                        'faq_title'             => $faqData['faq_title'],
                        'faq_short_description' => $faqData['faq_short_description'] ?? null,
                    ]);
                    $submittedFaqIds[] = $faq->id;
                } else {
                    $newFaq = $aboutInstitute->faqs()->create([
                        'faq_title'             => $faqData['faq_title'],
                        'faq_short_description' => $faqData['faq_short_description'] ?? null,
                    ]);
                    $submittedFaqIds[] = $newFaq->id;
                }
            }
        }

        // Delete FAQs removed from the repeater
        $aboutInstitute->faqs()->whereNotIn('id', $submittedFaqIds)->delete();

        return redirect()->route('admin.about-institute.edit')
            ->with('success', 'About Institute updated successfully.');
    }
}
