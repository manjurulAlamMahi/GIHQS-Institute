<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationEligibility extends Model
{
    use HasFactory;

    protected $table = 'accreditation_eligibility';

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
        return $this->hasMany(AccreditationEligibilityFeature::class, 'accreditation_eligibility_id');
    }
}
