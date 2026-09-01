<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutInstituteFaq extends Model
{
    protected $table = 'about_institute_faqs';

    protected $fillable = [
        'about_institute_id',
        'faq_title',
        'faq_short_description',
    ];

    /**
     * Get the about institute that owns the FAQ.
     */
    public function aboutInstitute()
    {
        return $this->belongsTo(AboutInstitute::class, 'about_institute_id');
    }
}
