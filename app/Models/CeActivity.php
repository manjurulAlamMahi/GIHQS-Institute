<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CeActivity extends Model
{
    use HasFactory;

    protected $table = 'ce_activities';

    protected $fillable = [
        'user_id',
        'catalogue_id',
        'domain',
        'activity_type',
        'activity_title',
        'provider',
        'completion_date',
        'credits_earned',
        'evidence_file',
        'description',
        'admin_notes',
        'status',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'credits_earned' => 'float',
    ];

    /**
     * Get the user that owns the CE activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the certification catalogue associated with the CE activity.
     */
    public function certification()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }
}
