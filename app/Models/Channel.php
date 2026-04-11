<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_id', 'name', 'slug', 'description', 'logo', 'banner', 'is_active', 'is_verified'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function testSeries()
    {
        return $this->hasMany(TestSeries::class);
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
