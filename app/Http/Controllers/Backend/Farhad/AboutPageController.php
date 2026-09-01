<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Helpers\MiaHelper;
use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Models\AboutPageFaq;
use Illuminate\Http\Request;

class AboutPageController extends Controller
{
    /**
     * Show the form for editing the about page.
     * Always edits the first (only) record — creates one if it doesn't exist.
     */
    public function edit()
    {
        $aboutPage = AboutPage::with('faqs')->firstOrCreate(
            [],
            ['title1' => 'About Us']
        );

        return view('backend.layouts.about_page.edit', compact('aboutPage'));
    }

    /**
     * Update the about page.
     */
    public function update(Request $request, $id)
    {
        $aboutPage = AboutPage::findOrFail($id);

        $request->validate([
            'title1'       => 'required|string|max:255',
            'title2'       => 'nullable|string|max:255',
            'tag_line'     => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'faqs'         => 'nullable|array',
            'faqs.*.faq_title'             => 'required|string|max:255',
            'faqs.*.faq_short_description' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $aboutPage->image = MiaHelper::updateFile($aboutPage->image, $request->file('image'), 'about_page');
        }

        $aboutPage->title1      = $request->title1;
        $aboutPage->title2      = $request->title2;
        $aboutPage->tag_line    = $request->tag_line;
        $aboutPage->description = $request->description;
        $aboutPage->save();

        // Handle FAQs repeater
        $submittedFaqIds = [];

        if ($request->has('faqs')) {
            foreach ($request->faqs as $faqData) {
                if (empty($faqData['faq_title'])) {
                    continue;
                }

                $faqId  = $faqData['id'] ?? null;
                $faq    = null;

                if ($faqId) {
                    $faq = AboutPageFaq::find($faqId);
                }

                if ($faq) {
                    $faq->update([
                        'faq_title'             => $faqData['faq_title'],
                        'faq_short_description' => $faqData['faq_short_description'] ?? null,
                    ]);
                    $submittedFaqIds[] = $faq->id;
                } else {
                    $newFaq = $aboutPage->faqs()->create([
                        'faq_title'             => $faqData['faq_title'],
                        'faq_short_description' => $faqData['faq_short_description'] ?? null,
                    ]);
                    $submittedFaqIds[] = $newFaq->id;
                }
            }
        }

        // Delete FAQs removed from the repeater
        $aboutPage->faqs()->whereNotIn('id', $submittedFaqIds)->delete();

        return redirect()->route('admin.about-institute.edit')
            ->with('success', 'About page updated successfully.');
    }
}
