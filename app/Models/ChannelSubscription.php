<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelSubscription extends Model
{
    protected $fillable = [
        'channel_id', 'plan_id', 'status', 'amount_paid', 'currency', 'starts_at', 'expires_at', 'gateway', 'payment_id'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
