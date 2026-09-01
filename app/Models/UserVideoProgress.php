<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVideoProgress extends Model
{
    protected $table = 'user_video_progress';

    protected $fillable = [
        'user_id',
        'video_id',
        'video_link_id',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(CatalogueVideo::class, 'video_id');
    }

    public function videoLink()
    {
        return $this->belongsTo(CatalogueVideoLink::class, 'video_link_id');
    }
}
