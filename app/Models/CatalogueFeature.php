<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogueFeature extends Model
{
    protected $table = 'catalogue_features';

    protected $fillable = [
        'catalogue_id',
        'description',
    ];

    /**
     * Get the catalog item that owns the feature.
     */
    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }
}
