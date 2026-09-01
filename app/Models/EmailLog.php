<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $table = 'email_logs';

    protected $fillable = [
        'user_id',
        'recipient_email',
        'recipient_role',
        'subject',
        'stage',
        'model_type',
        'model_id',
        'body_snippet',
    ];

    /**
     * Get the user associated with this log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent model (polymorphic relation).
     */
    public function loggable()
    {
        return $this->morphTo(null, 'model_type', 'model_id');
    }
}
