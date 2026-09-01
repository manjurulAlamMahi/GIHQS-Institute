<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPageFaq extends Model
{
    protected $fillable = [
        'about_page_id',
        'faq_title',
        'faq_short_description',
    ];

    /**
     * Get the about page that owns the FAQ.
     */
    public function aboutPage()
    {
        return $this->belongsTo(AboutPage::class, 'about_page_id');
    }
}
