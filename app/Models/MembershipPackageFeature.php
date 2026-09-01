<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPackageFeature extends Model
{
    protected $fillable = [
        'membership_package_id',
        'description',
        'badge',
        'note',
    ];

    /**
     * Get the membership package that owns the feature.
     */
    public function membershipPackage()
    {
        return $this->belongsTo(MembershipPackage::class, 'membership_package_id');
    }
}
