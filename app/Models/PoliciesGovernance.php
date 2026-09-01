<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliciesGovernance extends Model
{
    protected $table = 'policies_governances';

    protected $fillable = [
        'title1',
        'title2',
        'tagline',
        'description',

        'inst_title',
        'inst_tag',
        'inst_description',

        'cert_title',
        'cert_tag',
        'cert_description',

        'acc_title',
        'acc_tag',
        'acc_description',

        'commitment_title1',
        'commitment_title2',
        'commitment_description',

        'content_file',
        'injected_status',
    ];

    /**
     * Get all documents associated with this page config.
     */
    public function documents()
    {
        return $this->hasMany(PoliciesGovernanceDocument::class, 'policies_governance_id');
    }

    /**
     * Helper relation for institutional policies.
     */
    public function institutionalDocuments()
    {
        return $this->hasMany(PoliciesGovernanceDocument::class, 'policies_governance_id')
            ->where('type', 'inst');
    }

    /**
     * Helper relation for certification policies.
     */
    public function certificationDocuments()
    {
        return $this->hasMany(PoliciesGovernanceDocument::class, 'policies_governance_id')
            ->where('type', 'cert');
    }

    /**
     * Helper relation for accreditation policies.
     */
    public function accreditationDocuments()
    {
        return $this->hasMany(PoliciesGovernanceDocument::class, 'policies_governance_id')
            ->where('type', 'acc');
    }
}
