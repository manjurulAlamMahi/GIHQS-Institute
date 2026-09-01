<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationEligibilitySnapshotFeature extends Model
{
    use HasFactory;

    protected $table = 'accreditation_eligibility_snapshot_features';

    protected $fillable = [
        'accreditation_eligibility_snapshot_id',
        'keypoints',
        'details',
    ];

    public function snapshot()
    {
        return $this->belongsTo(AccreditationEligibilitySnapshot::class, 'accreditation_eligibility_snapshot_id');
    }
}
