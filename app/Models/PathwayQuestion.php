<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathwayQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'step_number',
        'question_text',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($question) {
            if ($question->step_number == 1 && $question->status == 1) {
                $query = static::where('step_number', 1);
                if ($question->id) {
                    $query->where('id', '!=', $question->id);
                }
                $query->update(['status' => 0]);
            }
        });
    }

    public function options()
    {
        return $this->hasMany(PathwayOption::class, 'question_id')->orderBy('order', 'asc');
    }
}
