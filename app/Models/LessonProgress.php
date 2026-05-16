<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'lesson_id',
        'current_time',
        'duration',
        'watched_seconds',
        'watched_percentage',
        'max_watched_time',
        'is_completed',
        'completed_at',
        'last_watched_at',
    ];

    protected function casts(): array
    {
        return [
            'current_time' => 'float',
            'duration' => 'float',
            'watched_seconds' => 'float',
            'watched_percentage' => 'float',
            'max_watched_time' => 'float',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'last_watched_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
