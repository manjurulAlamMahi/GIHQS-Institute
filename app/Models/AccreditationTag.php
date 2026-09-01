<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationTag extends Model
{
    use HasFactory;

    protected $table = 'accreditation_tags';

    protected $fillable = [
        'accreditation_header_id',
        'tagname',
    ];

    /**
     * Get the header that owns the tag.
     */
    public function header()
    {
        return $this->belongsTo(AccreditationHeader::class, 'accreditation_header_id');
    }
}
