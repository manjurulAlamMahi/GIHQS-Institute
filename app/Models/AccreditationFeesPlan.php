<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationFeesPlan extends Model
{
    use HasFactory;

    protected $table = 'accreditation_fees_plans';

    protected $fillable = [
        'accreditation_fee_id',
        'title',
        'price',
        'description',
    ];

    public function features()
    {
        return $this->hasMany(AccreditationFeesPlanFeature::class, 'accreditation_fees_plan_id');
    }

    public function fee()
    {
        return $this->belongsTo(AccreditationFee::class, 'accreditation_fee_id');
    }
}
