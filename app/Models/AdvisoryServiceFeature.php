<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryServiceFeature extends Model
{
    protected $table = 'advisory_service_features';

    protected $fillable = [
        'advisory_service_id',
        'serial_number',
        'tagline',
        'title',
        'description',
    ];

    public function service()
    {
        return $this->belongsTo(AdvisoryService::class, 'advisory_service_id');
    }
}
