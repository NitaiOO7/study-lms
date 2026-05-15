<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBundle extends Model
{
    protected $fillable = [
        'creator_id', 'title', 'slug', 'description', 
        'original_price', 'bundle_price', 'is_published'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'bundle_items', 'bundle_id', 'course_id');
    }

    public function collaborations()
    {
        return $this->hasMany(BundleCollaboration::class, 'bundle_id');
    }
}
