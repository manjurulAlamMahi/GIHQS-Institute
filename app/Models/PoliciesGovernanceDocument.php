<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliciesGovernanceDocument extends Model
{
    protected $table = 'policies_governance_documents';

    protected $fillable = [
        'policies_governance_id',
        'type',
        'title',
        'file',
    ];

    /**
     * Get the policies governance page config that owns the document.
     */
    public function policiesGovernance()
    {
        return $this->belongsTo(PoliciesGovernance::class, 'policies_governance_id');
    }
}
