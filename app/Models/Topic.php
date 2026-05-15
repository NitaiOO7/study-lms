<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['branch_id', 'name', 'slug'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
