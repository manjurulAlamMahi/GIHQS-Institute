<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogueVideoLink extends Model
{
    protected $table = 'catalogue_video_links';

    protected $fillable = [
        'catalogue_id',
        'video_link_title',
        'video_link_url',
    ];

    /**
     * Get the catalogue item that owns this video link.
     */
    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }
}
