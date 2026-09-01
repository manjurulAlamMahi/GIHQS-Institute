<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogueResource extends Model
{
    protected $table = 'catalogue_resources';

    protected $fillable = [
        'catalogue_id',
        'resource_title',
        'resource_file',
        'is_premium',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
    ];

    /**
     * Get the catalogue item that owns this resource.
     */
    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }
}
