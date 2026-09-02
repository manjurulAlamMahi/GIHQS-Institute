<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateSetting extends Model
{
    /** Used when no title has been configured. */
    public const DEFAULT_CHAIRMAN_TITLE           = 'Chairman of the Board';
    public const DEFAULT_EXECUTIVE_DIRECTOR_TITLE = 'Executive Director';

    protected $fillable = [
        'certificate_template',
        'chairman_name',
        'chairman_title',
        'chairman_signature',
        'show_chairman',
        'executive_director_name',
        'executive_director_title',
        'executive_director_signature',
        'show_executive_director',
    ];

    protected $casts = [
        'show_chairman'           => 'boolean',
        'show_executive_director' => 'boolean',
    ];

    protected $attributes = [
        'show_chairman'           => true,
        'show_executive_director' => true,
    ];

    public function chairmanTitle(): string
    {
        return filled($this->chairman_title)
            ? $this->chairman_title
            : self::DEFAULT_CHAIRMAN_TITLE;
    }

    public function executiveDirectorTitle(): string
    {
        return filled($this->executive_director_title)
            ? $this->executive_director_title
            : self::DEFAULT_EXECUTIVE_DIRECTOR_TITLE;
    }
}
