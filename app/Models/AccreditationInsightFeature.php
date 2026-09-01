<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationInsightFeature extends Model
{
    use HasFactory;

    protected $table = 'accreditation_insights_features';

    protected $fillable = [
        'accreditation_insights_id',
        'title',
        'tagline',
        'description',
    ];

    public function insight()
    {
        return $this->belongsTo(AccreditationInsight::class, 'accreditation_insights_id');
    }
}
