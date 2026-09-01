<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryScopeFeature extends Model
{
    protected $table = 'advisory_scope_features';

    protected $fillable = [
        'advisory_scope_id',
        'icon',
        'title',
        'description',
    ];

    public function scope()
    {
        return $this->belongsTo(AdvisoryScope::class, 'advisory_scope_id');
    }
}
