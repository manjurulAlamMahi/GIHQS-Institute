<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CertificationApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        // Applicant Information
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'city',
        'current_job_title',
        'organization',
        'linkedin_profile',

        // Professional Background
        'years_of_experience',
        'primary_area_of_experience',
        'professional_role',
        'resume_cv',

        // Certification Selection
        'catalogue_id',

        // Confirmations
        'confirm_accuracy',
        'agree_policies',

        // Admin
        'status',
        'admin_notes',
        'reference_number',
    ];

    protected $casts = [
        'confirm_accuracy' => 'boolean',
        'agree_policies'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }

    protected $appends = ['name', 'reference_number'];

    protected static function booted()
    {
        static::created(function ($model) {
            $model->reference_number = 'REF-CRT-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->saveQuietly();
        });
    }

    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getReferenceNumberAttribute($value)
    {
        return $value ?: 'REF-CRT-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}
