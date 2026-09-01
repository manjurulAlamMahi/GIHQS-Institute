<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeProfessionalPathway extends Model
{
    protected $table = 'home_professional_pathways';

    protected $fillable = [
        'home_gihq_id',
        'serial',
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
