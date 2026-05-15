<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['exam_id', 'name', 'slug', 'is_active'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}
