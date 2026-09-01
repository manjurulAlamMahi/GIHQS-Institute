<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipPackage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'title',
        'short_description',
        'price',
        'discount_percentage',
        'validity_days',
        'exam_attempt_limit',
        'status',
    ];

    protected $casts = [
    ];

    /**
     * Get the features for the membership package.
     */
    public function features()
    {
        return $this->hasMany(MembershipPackageFeature::class, 'membership_package_id');
    }
}
