<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id', 'course_id', 'subject_id', 'title', 'description',
        'type', 'file_path', 'external_url', 'file_size', 'is_free', 'sort_order'
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
