<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationProcess extends Model
{
    use HasFactory;

    protected $table = 'accreditation_processes';

    protected $fillable = [
        'title1',
        'title2',
        'description',
    ];

    /**
     * Get features for this section.
     */
    public function features()
    {
        return $this->hasMany(AccreditationProcessFeature::class, 'accreditation_process_id');
    }
}
