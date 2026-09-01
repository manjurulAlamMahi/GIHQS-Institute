<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationKeyfact extends Model
{
    use HasFactory;

    protected $table = 'accreditation_keyfacts';

    protected $fillable = [
        'accreditation_header_id',
        'title',
        'subtitle',
    ];

    /**
     * Get the header that owns the key fact.
     */
    public function header()
    {
        return $this->belongsTo(AccreditationHeader::class, 'accreditation_header_id');
    }
}
