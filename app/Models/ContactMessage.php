<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'organization',
        'service_of_interest',
        'message',
        'status',
        'reference_number',
    ];

    protected $appends = ['name', 'reference_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($model) {
            $model->reference_number = 'REF-CON-' . str_pad($model->id, 5, '0', STR_PAD_LEFT);
            $model->saveQuietly();
        });
    }

    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getReferenceNumberAttribute($value)
    {
        return $value ?: 'REF-CON-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function replies()
    {
        return $this->hasMany(ContactMessageReply::class);
    }
}
