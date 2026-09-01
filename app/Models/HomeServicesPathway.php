<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeServicesPathway extends Model
{
    protected $table = 'home_services_pathways';

    protected $fillable = [
        'home_gihq_id',
        'serial',
        'target_audience',
        'title',
        'description',
        'link_text',
    ];

    /**
     * Get the Home GIHQ config that owns this item.
     */
    public function homeGihq()
    {
        return $this->belongsTo(HomeGihq::class, 'home_gihq_id');
    }
}
