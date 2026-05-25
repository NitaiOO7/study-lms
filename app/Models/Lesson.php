<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'video_url',
        'video_path',
        'pdf_path',
        'annotated_pdf_path',
        'sort_order',
        'is_free'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function progressForStudent()
    {
        return $this->hasOne(LessonProgress::class);
    }
}
