<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationEligibilitySnapshot extends Model
{
    use HasFactory;

    protected $table = 'accreditation_eligibility_snapshot';

    protected $fillable = [
        'title',
        'description',
    ];

    public function features()
    {
        return $this->hasMany(AccreditationEligibilitySnapshotFeature::class, 'accreditation_eligibility_snapshot_id');
    }
}
