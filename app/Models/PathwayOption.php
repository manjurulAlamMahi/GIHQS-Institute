<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathwayOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_text',
        'next_question_id',
        'result_id',
        'order',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function question()
    {
        return $this->belongsTo(PathwayQuestion::class, 'question_id');
    }

    public function nextQuestion()
    {
        return $this->belongsTo(PathwayQuestion::class, 'next_question_id');
    }

    public function result()
    {
        return $this->belongsTo(PathwayResult::class, 'result_id');
    }
}
