<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'option_text', 'option_image', 'is_correct', 'sort_order'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
