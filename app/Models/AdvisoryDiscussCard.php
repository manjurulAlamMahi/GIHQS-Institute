<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryDiscussCard extends Model
{
    protected $table = 'advisory_discuss_cards';

    protected $fillable = [
        'title1',
        'title2',
        'description',
        'button_text',
    ];
}
