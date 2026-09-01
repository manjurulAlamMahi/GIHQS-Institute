<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherPage extends Model
{
    protected $table = 'other_pages';

    protected $fillable = [
        'slug',
        'title',
        'content_file',
        'injected_status',
    ];
}
