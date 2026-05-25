<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function isVideo(): bool
    {
        if ($this->type !== 'video') {
            return false;
        }

        $path = strtolower((string) ($this->file_path ?: $this->external_url));

        return $this->file_path
            || str_contains($path, '.mp4')
            || str_contains($path, '.webm')
            || str_contains($path, 'youtube.com')
            || str_contains($path, 'youtu.be')
            || str_contains($path, 'vimeo.com');
    }

    public function playbackUrl(): ?string
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return $this->external_url;
    }
}
