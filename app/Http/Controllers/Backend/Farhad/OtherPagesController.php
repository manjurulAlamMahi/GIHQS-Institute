<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Helpers\MiaHelper;
use App\Http\Controllers\Controller;
use App\Models\OtherPage;
use Illuminate\Http\Request;

class OtherPagesController extends Controller
{
    /**
     * Show the unified form for editing all other pages.
     */
    public function edit()
    {
        $pages = OtherPage::all()->keyBy('slug');

        // Defensively ensure that all default records exist
        $slugs = ['privacy-policy', 'terms-of-use', 'terms-purchase', 'refund-policy', 'disclaimer'];
        $titles = [
            'privacy-policy' => 'Privacy Policy',
            'terms-of-use' => 'Terms of Use',
            'terms-purchase' => 'Terms & Conditions of Purchase',
            'refund-policy' => 'Refund Policy',
            'disclaimer' => 'Disclaimer'
        ];

        foreach ($slugs as $slug) {
            if (!isset($pages[$slug])) {
                $pages[$slug] = OtherPage::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => $titles[$slug],
                        'content_file' => null,
                        'injected_status' => 1,
                    ]
                );
            }
        }

        return view('backend.layouts.other_pages.edit', compact('pages'));
    }

    /**
     * Update all other pages.
     */
    public function update(Request $request)
    {
        $slugs = ['privacy-policy', 'terms-of-use', 'terms-purchase', 'refund-policy', 'disclaimer'];
        
        $rules = [];
        foreach ($slugs as $slug) {
            $prefix = str_replace('-', '_', $slug);
            $rules["{$prefix}_title"] = 'required|string|max:255';
            $rules["{$prefix}_file"] = 'nullable|file|mimes:html,txt|max:10240';
            $rules["{$prefix}_injected_status"] = 'required|in:0,1';
        }

        $request->validate($rules);

        foreach ($slugs as $slug) {
            $prefix = str_replace('-', '_', $slug);
            $otherPage = OtherPage::where('slug', $slug)->first();

            $contentFilePath = $otherPage->content_file;

            if ($request->input("remove_{$prefix}_file") == 1) {
                MiaHelper::deleteFile($otherPage->content_file);
                $contentFilePath = null;
            } elseif ($request->hasFile("{$prefix}_file")) {
                $contentFilePath = MiaHelper::updateFile(
                    $otherPage->content_file,
                    $request->file("{$prefix}_file"),
                    'other_pages'
                );
            }

            $otherPage->title = $request->input("{$prefix}_title");
            $otherPage->content_file = $contentFilePath;
            $otherPage->injected_status = $request->input("{$prefix}_injected_status");
            $otherPage->save();
        }

        return redirect()->route('admin.other-pages.edit')
            ->with('success', 'Other Pages updated successfully.');
    }
}
