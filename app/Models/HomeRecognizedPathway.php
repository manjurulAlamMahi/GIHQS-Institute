<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeRecognizedPathway extends Model
{
    protected $table = 'home_recognized_pathways';

    protected $fillable = [
        'title1',
        'title2',
        'tagline',
        'description',
        'content_file',
        'injected_status',
    ];

    /**
     * Get the certificates associated with this page.
     */
    public function certificates()
    {
        return $this->hasMany(HomeCertificate::class, 'home_recognized_pathway_id');
    }
}
