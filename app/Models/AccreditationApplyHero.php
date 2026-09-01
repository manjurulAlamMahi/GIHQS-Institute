<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationApplyHero extends Model
{
    use HasFactory;

    protected $table = 'accreditation_apply_hero';

    protected $fillable = [
        'title1',
        'title2',
        'tagline',
        'description',
        'note',
    ];
}
