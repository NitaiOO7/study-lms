<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id', 'subject_id', 'branch_id', 'title', 'slug', 'description',
        'thumbnail', 'price', 'duration_days', 'level', 'is_free', 'is_published'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function testSeries()
    {
        return $this->hasMany(TestSeries::class);
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'course_id', 'student_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
