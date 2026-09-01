<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeGihq extends Model
{
    protected $table = 'home_gihqs';

    protected $fillable = [
        'title1',
        'title2',
        'tagline',
        'description',
        'certificate_btn_text',
        'learning_btn_text',
        'advisory_btn_text',
        'member_btn_text',

        'professional_ecosystem_title',
        'learning_tagline',
        'learning_title',
        'learning_details',
        'certificate_tagline',
        'certificate_title',
        'certificate_details',
        'lead_tagline',
        'lead_title',
        'lead_details',

        'content_file',
        'injected_status',
    ];

    /**
     * Get the Services & Pathways items for the home page.
     */
    public function servicesPathways()
    {
        return $this->hasMany(HomeServicesPathway::class, 'home_gihq_id');
    }

    /**
     * Get the professional pathways items for the home page.
     */
    public function professionalPathways()
    {
        return $this->hasMany(HomeProfessionalPathway::class, 'home_gihq_id');
    }

    /**
     * Get the choose your next step config for the home page.
     */
    public function nextStep()
    {
        return $this->hasOne(HomeNextStep::class, 'home_gihq_id');
    }
}
