<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AiChatSession extends Model
{
    protected $fillable = ['user_id', 'role', 'session_token', 'title'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->session_token)) {
                $model->session_token = Str::random(64);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiChatMessage::class, 'session_id');
    }

    public function getLastMessageAttribute(): ?AiChatMessage
    {
        return $this->messages()->latest('created_at')->first();
    }

    /**
     * Build the message history array for the Groq API
     */
    public function getApiHistory(int $limit = 20): array
    {
        return $this->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->latest('created_at')
            ->take($limit)
            ->get()
            ->reverse()
            ->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])
            ->values()
            ->toArray();
    }
}
