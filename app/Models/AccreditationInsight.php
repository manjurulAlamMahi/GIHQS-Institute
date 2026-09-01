<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationInsight extends Model
{
    use HasFactory;

    protected $table = 'accreditation_insights';

    protected $fillable = [
        'title1',
        'title2',
        'description',
    ];

    /**
     * Get features for this section.
     */
    public function features()
    {
        return $this->hasMany(AccreditationInsightFeature::class, 'accreditation_insights_id');
    }
}
