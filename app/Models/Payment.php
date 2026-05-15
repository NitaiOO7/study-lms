<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'payable_type', 'payable_id', 'amount', 'currency', 'status', 'gateway', 'gateway_payment_id', 'payload'
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }
}
