<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id', 'question_text', 'question_image', 'type',
        'marks', 'negative_marks', 'explanation', 'sort_order'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class)->orderBy('sort_order');
    }

    public function correctOption()
    {
        return $this->hasOne(Option::class)->where('is_correct', true);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
