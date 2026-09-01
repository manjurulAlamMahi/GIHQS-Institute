<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserExamOverride extends Model
{
    use HasFactory;

    protected $table = 'user_exam_overrides';

    protected $fillable = [
        'user_id',
        'catalogue_exam_id',
        'max_attempts',
        'retake_eligible_date',
        'ignore_cooldown',
    ];

    protected $casts = [
        'ignore_cooldown' => 'boolean',
        'max_attempts' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function catalogueExam()
    {
        return $this->belongsTo(CatalogueExam::class, 'catalogue_exam_id');
    }
}
