<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeNextStep extends Model
{
    protected $table = 'home_next_steps';

    protected $fillable = [
        'home_gihq_id',
        'title1',
        'title2',
        'tagline',
        'certificate_btn_text',
        'learning_btn_text',
        'advisory_btn_text',
        'member_btn_text',
    ];

    /**
     * Get the Home GIHQ config that owns this section.
     */
    public function homeGihq()
    {
        return $this->belongsTo(HomeGihq::class, 'home_gihq_id');
    }
}
