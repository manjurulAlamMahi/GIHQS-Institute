<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryHeader extends Model
{
    protected $table = 'advisory_headers';

    protected $fillable = [
        'title1',
        'title2',
        'tagline',
        'description',
        'content_file',
        'injected_status',
    ];
}
