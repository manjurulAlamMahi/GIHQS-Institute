<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationFeesPlanFeature extends Model
{
    use HasFactory;

    protected $table = 'accreditation_fees_plan_features';

    protected $fillable = [
        'accreditation_fees_plan_id',
        'feature',
    ];

    public function plan()
    {
        return $this->belongsTo(AccreditationFeesPlan::class, 'accreditation_fees_plan_id');
    }
}
