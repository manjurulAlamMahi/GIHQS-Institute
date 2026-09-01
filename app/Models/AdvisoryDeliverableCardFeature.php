<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryDeliverableCardFeature extends Model
{
    protected $table = 'advisory_deliverable_card_features';

    protected $fillable = [
        'advisory_deliverable_card_id',
        'name',
    ];

    public function deliverableCard()
    {
        return $this->belongsTo(AdvisoryDeliverableCard::class, 'advisory_deliverable_card_id');
    }
}
