<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryFocus extends Model
{
    protected $table = 'advisory_focuses';

    protected $fillable = [
        'title',
        'description',
    ];

    public function features()
    {
        return $this->hasMany(AdvisoryFocusFeature::class, 'advisory_focus_id');
    }
}
