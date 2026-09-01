<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationProcessFeature extends Model
{
    use HasFactory;

    protected $table = 'accreditation_process_features';

    protected $fillable = [
        'accreditation_process_id',
        'serial',
        'title',
        'subtitle',
        'description',
    ];

    public function process()
    {
        return $this->belongsTo(AccreditationProcess::class, 'accreditation_process_id');
    }
}
