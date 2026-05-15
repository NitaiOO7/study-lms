<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSeries extends Model
{
    use HasFactory;

    protected $table = 'test_series';

    protected $fillable = [
        'course_id', 'channel_id', 'branch_id', 'title', 'slug', 'description',
        'is_demo', 'is_published', 'total_marks', 'passing_marks', 'sort_order'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeDemo($query)
    {
        return $query->where('is_demo', true);
    }
}
