<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogueLiveLink extends Model
{
    protected $table = 'catalogue_live_links';

    protected $fillable = [
        'catalogue_id',
        'link_title',
        'link_url',
    ];

    /**
     * Get the catalogue item that owns this live link.
     */
    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }
}
