<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisionMissionValue extends Model
{
    protected $table = 'vision_mission_values';

    protected $fillable = [
        // General Section
        'tagline',
        'title1',
        'title2',
        'short_description',

        // Vision Section
        'vision_tagline',
        'vision_title',
        'vision_short_description',

        // Mission Section
        'mission_tagline',
        'mission_title',
        'mission_short_description',

        // Value Section
        'value_tagline',
        'value_title',
        'value_title2',
        'value_short_description',

        // Global Perspective Section
        'global_perspective_tagline',
        'global_perspective_title',
        'global_perspective_short_description',

        // Integrity Section
        'integrity_tagline',
        'integrity_title',
        'integrity_short_description',

        // Human Centered Section
        'human_centered_tagline',
        'human_centered_title',
        'human_centered_short_description',

        // Quality & Excellence Section
        'quality_excellence_tagline',
        'quality_excellence_title',
        'quality_excellence_short_description',

        // Safety Leadership Section
        'safety_leadership_tagline',
        'safety_leadership_title',
        'safety_leadership_short_description',

        'content_file',
        'injected_status',
    ];
}
