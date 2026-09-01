<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathwayResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'badges',
        'info_box_text',
        'primary_button_text',
        'primary_button_url',
        'secondary_button_text',
        'secondary_button_url',
        'status'
    ];

    protected $casts = [
        'badges' => 'array',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
