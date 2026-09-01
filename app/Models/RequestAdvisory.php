<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestAdvisory extends Model
{
    use HasFactory;

    protected $table = 'request_advisories';

    protected $fillable = [
        'title1',
        'title2',
        'tagline',
        'description',
    ];
}
