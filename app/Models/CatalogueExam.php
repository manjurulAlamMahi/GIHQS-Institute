<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogueExam extends Model
{
    protected $table = 'catalogue_exams';

    protected $fillable = [
        'catalogue_id',
        'exam_id',
        'exam_title',
        'exam_link',
        'pass_mark',
        'is_premium',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'pass_mark' => 'float',
        'exam_id' => 'integer',
    ];

    /**
     * Get the catalogue item that owns this exam.
     */
    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id');
    }

    /**
     * Get the local exam associated.
     */
    public function localExam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
