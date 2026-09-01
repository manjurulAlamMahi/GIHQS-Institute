<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutInstitute extends Model
{
    protected $table = 'about_institutes';

    protected $fillable = [
        'title1',
        'title2',
        'tag_line',
        'description',
        'image',
        'content_file',
        'injected_status',
    ];

    /**
     * Get the FAQs for the about institute page.
     */
    public function faqs()
    {
        return $this->hasMany(AboutInstituteFaq::class, 'about_institute_id');
    }
}
