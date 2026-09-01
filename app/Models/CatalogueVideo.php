<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogueVideo extends Model
{
    protected $table = 'catalogue_videos';

    protected $fillable = [
        'catalogue_id',
        'video_title',
        'video_file',
        'thumbnail',
    ];

    /**
     * Get the catalogue item that owns this video.
     */
    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }
}
