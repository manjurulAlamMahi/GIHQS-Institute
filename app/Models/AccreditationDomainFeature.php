<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationDomainFeature extends Model
{
    use HasFactory;

    protected $table = 'accreditation_domain_features';

    protected $fillable = [
        'accreditation_domain_id',
        'domain_serial',
        'title',
        'description',
    ];

    public function domain()
    {
        return $this->belongsTo(AccreditationDomain::class, 'accreditation_domain_id');
    }
}
