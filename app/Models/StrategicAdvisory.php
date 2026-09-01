<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategicAdvisory extends Model
{
    protected $table = 'strategic_advisories';

    protected $fillable = [
        'title1',
        'title2',
        'tagline',
        'short_description',

        'purpose_tagline',
        'purpose_title',
        'purpose_short_description',

        'advisory_title',

        'panel_title',
        'panel_short_description',

        'appointment_title',
        'appointment_short_description',

        'conflict_title',
        'conflict_short_description',

        'expression_title',
        'expression_description',

        'content_file',
        'injected_status',
    ];

    /**
     * Get the advisory features.
     */
    public function features()
    {
        return $this->hasMany(StrategicAdvisoryFeature::class, 'strategic_advisory_id');
    }
}
