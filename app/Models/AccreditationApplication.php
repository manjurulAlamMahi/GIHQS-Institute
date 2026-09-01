<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccreditationApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accreditation_applications';

    protected $fillable = [
        'user_id',
        // 1. Applicant Information
        'applicant_category',
        'applicant_name',
        'department_division',
        'country',
        'city',
        'website_url',
        'year_established',

        // 2. Program Information
        'program_name',
        'program_type',
        'program_delivery_format',
        'estimated_annual_participants',
        'primary_language_of_instruction',
        'program_launch_date',

        // 3. Primary Contact Information
        'primary_contact_person',
        'contact_title_position',
        'email_address',
        'phone_number',

        // 4. Supporting Attachments
        'program_overview_doc',
        'governance_policy_doc',

        // 5. Additional Information
        'additional_information',

        // Admin Management
        'status',
        'admin_notes',
        'reference_number',
        'verification_code',
        'issued_at',
        'expires_at',
        'certificate_pdf',

        // Payment fields
        'payment_amount',
        'payment_currency',
        'payment_description',
        'payment_status',
        'payment_method',
        'payment_date',
        'validity_days',
        'stripe_session_id',
        'stripe_payment_link',
        'stripe_payment_intent_id',
        'payment_sent_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'payment_amount' => 'float',
        'validity_days' => 'integer',
        'payment_sent_at' => 'datetime',
        'payment_date' => 'datetime',
    ];

    protected $appends = ['reference_number', 'computed_status', 'certificate_pdf_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($model) {
            $model->reference_number = 'REF-ACC-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->verification_code = 'GIHQS-ACC-' . now()->format('Y') . '-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->saveQuietly();
        });
    }

    public function getReferenceNumberAttribute($value)
    {
        return $value ?: 'REF-ACC-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getComputedStatusAttribute()
    {
        $status = strtolower($this->status ?? 'pending');
        if ($status === 'valid' && $this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }
        return $status;
    }

    public function getCertificatePdfUrlAttribute()
    {
        return $this->certificate_pdf ? asset($this->certificate_pdf) : null;
    }
}
