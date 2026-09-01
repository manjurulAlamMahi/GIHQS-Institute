<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationFee extends Model
{
    use HasFactory;

    protected $table = 'accreditation_fees';

    protected $fillable = [
        'title1',
        'title2',
        'description',
    ];

    public function plans()
    {
        return $this->hasMany(AccreditationFeesPlan::class, 'accreditation_fee_id');
    }
}
