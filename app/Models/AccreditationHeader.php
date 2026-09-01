<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationHeader extends Model
{
    use HasFactory;

    protected $table = 'accreditation_headers';

    protected $fillable = [
        'title1',
        'title2',
        'tagline',
        'description',
        'note',
        'apply_btn_text',
        'download_btn_text',
        'download_file',
        'content_file',
        'injected_status',
    ];

    /**
     * Get the tags for the accreditation header.
     */
    public function tags()
    {
        return $this->hasMany(AccreditationTag::class, 'accreditation_header_id');
    }

    /**
     * Get the key facts for the accreditation header.
     */
    public function keyfacts()
    {
        return $this->hasMany(AccreditationKeyfact::class, 'accreditation_header_id');
    }
}
