<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    protected $fillable = [
        'title1',
        'title2',
        'tag_line',
        'description',
        'image',
    ];

    /**
     * Get the FAQs for the about page.
     */
    public function faqs()
    {
        return $this->hasMany(AboutPageFaq::class, 'about_page_id');
    }
}
