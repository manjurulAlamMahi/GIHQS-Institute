<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateSetting extends Model
{
    protected $table = 'certificate_settings';

    protected $fillable = [
        'certificate_template',
        'chairman_name',
        'chairman_signature',
        'executive_director_name',
        'executive_director_signature',
    ];
}
