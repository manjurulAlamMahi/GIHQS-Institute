<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategicAdvisoryFeature extends Model
{
    protected $table = 'strategic_advisory_features';

    protected $fillable = [
        'strategic_advisory_id',
        'description',
    ];

    /**
     * Get the strategic advisory that owns the feature.
     */
    public function strategicAdvisory()
    {
        return $this->belongsTo(StrategicAdvisory::class, 'strategic_advisory_id');
    }
}
