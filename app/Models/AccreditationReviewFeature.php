<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccreditationReviewFeature extends Model
{
    protected $table = 'accreditation_review_features';

    protected $fillable = [
        'accreditation_review_id',
        'description',
    ];

    /**
     * Get the accreditation review that owns the feature.
     */
    public function accreditationReview()
    {
        return $this->belongsTo(AccreditationReview::class, 'accreditation_review_id');
    }
}
