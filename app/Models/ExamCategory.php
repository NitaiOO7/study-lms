<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'is_active'];

    public function exams()
    {
        return $this->hasMany(Exam::class, 'category_id');
    }
}
