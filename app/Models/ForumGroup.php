<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon', 'subject_id', 'is_universal', 'is_active'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUniversal($query)
    {
        return $query->where('is_universal', true);
    }
}
