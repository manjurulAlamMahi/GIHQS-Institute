<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeCertificate extends Model
{
    protected $table = 'home_certificates';

    protected $fillable = [
        'home_recognized_pathway_id',
        'short_title',
        'title',
        'icon',
        'tagline',
        'headline',
        'description',
        'audience',
        'tags',
        'button_text',
    ];

    /**
     * Get the Home Recognized Pathway config that owns this certificate.
     */
    public function homeRecognizedPathway()
    {
        return $this->belongsTo(HomeRecognizedPathway::class, 'home_recognized_pathway_id');
    }
}
