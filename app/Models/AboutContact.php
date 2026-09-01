<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutContact extends Model
{
    protected $table = 'about_contact';

    protected $fillable = [
        'title',
        'phone',
        'email',
        'address',
        'working_hours',
        'mission',
        'content_file',
        'injected_status',
    ];
}
