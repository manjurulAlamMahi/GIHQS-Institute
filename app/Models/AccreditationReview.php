<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccreditationReview extends Model
{
    protected $table = 'accreditation_reviews';

    protected $fillable = [
        'title1',
        'title2',
        'tagline',
        'short_description',

        'purpose_tagline',
        'purpose_title',
        'purpose_short_description',

        'review_title',

        'panel_title',
        'panel_short_description',

        'appointment_title',
        'appointment_short_description',

        'conflict_title',
        'conflict_short_description',

        'expression_title',
        'expression_description',

        'content_file',
        'injected_status',
    ];

    /**
     * Get the evaluation responsibilities/features.
     */
    public function features()
    {
        return $this->hasMany(AccreditationReviewFeature::class, 'accreditation_review_id');
    }
}
