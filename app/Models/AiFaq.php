<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiFaq extends Model
{
    protected $fillable = ['role', 'question', 'answer', 'category', 'keywords', 'is_active'];

    protected $casts = [
        'keywords'  => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('role', $role)->orWhere('role', 'all');
        });
    }
}
