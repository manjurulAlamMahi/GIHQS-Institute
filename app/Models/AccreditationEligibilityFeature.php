<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationEligibilityFeature extends Model
{
    use HasFactory;

    protected $table = 'accreditation_eligibility_features';

    protected $fillable = [
        'accreditation_eligibility_id',
        'title',
        'description',
    ];

    public function eligibility()
    {
        return $this->belongsTo(AccreditationEligibility::class, 'accreditation_eligibility_id');
    }
}
