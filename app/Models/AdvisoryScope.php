<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryScope extends Model
{
    protected $table = 'advisory_scopes';

    protected $fillable = [
        'title1',
        'title2',
        'description',
    ];

    public function features()
    {
        return $this->hasMany(AdvisoryScopeFeature::class, 'advisory_scope_id');
    }
}
