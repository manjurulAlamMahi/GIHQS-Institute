<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryService extends Model
{
    protected $table = 'advisory_services';

    protected $fillable = [
        'title1',
        'title2',
        'description',
    ];

    public function features()
    {
        return $this->hasMany(AdvisoryServiceFeature::class, 'advisory_service_id');
    }
}
