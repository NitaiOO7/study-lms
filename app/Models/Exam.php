<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'description', 'is_active'];

    public function category()
    {
        return $this->belongsTo(ExamCategory::class, 'category_id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
