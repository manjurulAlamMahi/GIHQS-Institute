<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryFocusFeature extends Model
{
    protected $table = 'advisory_focus_features';

    protected $fillable = [
        'advisory_focus_id',
        'description',
    ];

    public function focus()
    {
        return $this->belongsTo(AdvisoryFocus::class, 'advisory_focus_id');
    }
}
