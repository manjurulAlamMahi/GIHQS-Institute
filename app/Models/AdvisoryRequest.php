<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdvisoryRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'advisory_requests';

    protected $fillable = [
        'user_id',
        'organization_name',
        'full_name',
        'work_email',
        'phone_number',
        'country',
        'organization_type',
        'service_of_interest',
        'desired_timeline',
        'description_of_needs',
        'status',
        'admin_notes',
        'reference_number',
        'payment_amount',
        'payment_currency',
        'payment_description',
        'payment_status',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_payment_link',
        'payment_sent_at',
        'payment_date',
        'validity_days',
        'expires_at',
    ];

    protected $casts = [
        'payment_amount'  => 'float',
        'validity_days'   => 'integer',
        'payment_sent_at' => 'datetime',
        'payment_date'    => 'datetime',
        'expires_at'      => 'datetime',
    ];

    protected $appends = ['reference_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($model) {
            $model->reference_number = 'REF-ADV-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->saveQuietly();
        });
    }

    public function getReferenceNumberAttribute($value)
    {
        return $value ?: 'REF-ADV-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}
