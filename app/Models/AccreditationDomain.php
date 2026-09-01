<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationDomain extends Model
{
    use HasFactory;

    protected $table = 'accreditation_domains';

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
        return $this->hasMany(AccreditationDomainFeature::class, 'accreditation_domain_id');
    }
}
