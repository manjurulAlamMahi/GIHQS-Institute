<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class MiaHelper
{

    //------- For Image and File Upload
    public static function uploadFile($file, $directory)
    {
        $directoryPath = 'uploads/' . $directory;
        if (!file_exists(public_path($directoryPath))) {
            mkdir(public_path($directoryPath), 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $directoryPath . '/' . $fileName;
        // $filePathFull = public_path($filePath);

        $file->move(public_path($directoryPath), $fileName);
        return $filePath;
    }

    // Usage Example:
    //  if ($request->hasFile('image')) {
    //         $milestone->image = MiaHelper::uploadFile($request->file('image'), 'milestone-images');
    //     }


    //-------- For File Deletion
    public static function deleteFile($filePath)
    {
        if ($filePath && file_exists(public_path($filePath))) {
            unlink(public_path($filePath));
        }
    }

    // Usage Example:
    // if ($request->file('image')) {
    //         MiaHelper::deleteFile($user->image);
    // }

    //-------- For File Update
    public static function updateFile($oldFilePath, $newFile, $directory)
    {
        // Delete old file
        self::deleteFile($oldFilePath);

        // Upload new file
        return self::uploadFile($newFile, $directory);
    }

    // Usage Example:
    // if ($request->hasFile('banner_image')) {
    //         $history->banner_image = MiaHelper::updateFile($history->banner_image, $request->file('banner_image'), 'history-images');
    //     }


    // For Image Upload with Intervention
    public static function uploadImageResize($file, $directory, $width = null, $height = null)
    {
        $directoryPath = 'uploads/' . $directory;
        if (!file_exists(public_path($directoryPath))) {
            mkdir(public_path($directoryPath), 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $directoryPath . '/' . $fileName;
        // $filePathFull = public_path($filePath);

        $image = Image::make($file);

        // Resize logic
        if ($width && !$height) {
            $image->resize($width, null);
        } elseif ($height && !$width) {
            $image->resize(null, $height);
        } elseif ($width && $height) {
            $image->resize($width, $height);
        }

        $image->save(public_path($filePath));
        return $filePath;
    }


    //-------- For Slug Generation
    public static function generateSlug($modelClass, $name, $field = 'slug', $ignoreId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        $query = $modelClass::where($field, $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;

            $query = $modelClass::where($field, $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }


    // Usage Example - create:
    // $slug = MiaHelper::generateSlug(Category::class, $request->name);

    // Usage Example - update:
    // if ($category->name !== $request->name) {
    //         $category->slug = MiaHelper::generateSlug(Category::class, $request->name, 'slug', $category->id);
    //     }


    //--------  For Text Cleaning
    public static function cleanText($html)
    {
        return trim(
            preg_replace(
                '/\s+/u',
                ' ',
                str_replace(
                    "\u{00A0}",
                    ' ',
                    html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                )
            )
        );
    }

    public static function cleanTextWithLineBreaks($html)
    {
        // 1. Replace block level tags and break tags with newlines, accounting for attributes
        $text = preg_replace('/<(br|p|div|li|\/p|\/div|\/li)(\s[^>]*)?>/i', "\n", $html);
        
        // 2. Strip all remaining HTML tags
        $text = strip_tags($text);
        
        // 3. Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // 4. Replace non-breaking spaces and tabs with regular spaces
        $text = str_replace(["\u{00A0}", "\t"], ' ', $text);
        
        // 5. Replace multiple horizontal spaces with a single space (preserving newlines)
        $text = preg_replace('/[ ]+/u', ' ', $text);
        
        // 6. Clean up leading/trailing spaces on each individual line
        $lines = explode("\n", $text);
        $cleanedLines = [];
        foreach ($lines as $line) {
            $line = trim($line);
            $cleanedLines[] = $line;
        }
        $text = implode("\n", $cleanedLines);
        
        // 7. Collapse multiple consecutive empty lines/newlines (maximum 2 consecutive newlines for clean paragraph spacing)
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        
        // 8. Final trim
        return trim($text);
    }

    public static function htmlToMarkdown($html)
    {
        if (empty($html)) {
            return '';
        }

        // 1. Decode HTML entities
        $markdown = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 2. Convert <br>, <br/>, <br /> to newlines
        $markdown = preg_replace('/<br\s*\/?>/i', "\n", $markdown);

        // 3. Convert paragraphs <p>...</p> to block newlines
        $markdown = preg_replace('/<p(\s[^>]*)?>/i', '', $markdown);
        $markdown = preg_replace('/<\/p>/i', "\n\n", $markdown);

        // 4. Convert bold tags: <strong>...</strong> or <b>...</b> to **...**
        $markdown = preg_replace('/<(strong|b)>([\s\S]*?)<\/\1>/i', '**$2**', $markdown);

        // 5. Convert italic tags: <em>...</em> or <i>...</i> to *...*
        $markdown = preg_replace('/<(em|i)>([\s\S]*?)<\/\1>/i', '*$2*', $markdown);

        // 6. Convert headers: <h1>...</h1> to # ..., <h2>...</h2> to ## ..., etc.
        $markdown = preg_replace_callback('/<h([1-6])(\s[^>]*)?>([\s\S]*?)<\/h\1>/i', function($matches) {
            $level = intval($matches[1]);
            $hashes = str_repeat('#', $level);
            return "\n\n" . $hashes . ' ' . trim($matches[3]) . "\n\n";
        }, $markdown);

        // 7. Convert links: <a href="URL">TEXT</a> or <a href='URL'>TEXT</a> to [TEXT](URL)
        $markdown = preg_replace('/<a\s+[^>]*href=(["\'])(.*?)\1[^>]*>([\s\S]*?)<\/a>/i', '[$3]($2)', $markdown);

        // 8. Convert list items: <li>...</li> to - ...
        $markdown = preg_replace('/<li(\s[^>]*)?>([\s\S]*?)<\/li>/i', "- $2\n", $markdown);
        
        // 9. Remove outer list tags <ul>, </ul>, <ol>, </ol>
        $markdown = preg_replace('/<\/?(ul|ol)(\s[^>]*)?>/i', '', $markdown);

        // 10. Strip any other HTML tags that are left
        $markdown = strip_tags($markdown);

        // 11. Normalize spaces & newlines (but preserve paragraph spacing)
        $lines = explode("\n", $markdown);
        $cleanedLines = [];
        foreach ($lines as $line) {
            $cleanedLines[] = trim($line);
        }
        $markdown = implode("\n", $cleanedLines);

        // Collapse multiple consecutive newlines (max 2 consecutive for clean paragraphs)
        $markdown = preg_replace("/\n{3,}/", "\n\n", $markdown);

        return trim($markdown);
    }

    // Usage Example:
    // $cleanDescription = MiaHelper::cleanText($item->description);
    //or,
    // 'cleanDescription' = MiaHelper::cleanText($item->description);
}
