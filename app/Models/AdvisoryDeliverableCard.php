<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryDeliverableCard extends Model
{
    protected $table = 'advisory_deliverable_cards';

    protected $fillable = [
        'title1',
        'title2',
        'description',
    ];

    public function features()
    {
        return $this->hasMany(AdvisoryDeliverableCardFeature::class, 'advisory_deliverable_card_id');
    }
}
