<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiKnowledgeChunk extends Model
{
    protected $fillable = [
        'source_type',
        'source_path',
        'chunk_index',
        'content',
        'keywords',
        'checksum',
        'indexed_at',
    ];

    protected $casts = [
        'keywords'   => 'array',
        'indexed_at' => 'datetime',
    ];

    /**
     * Find existing chunk by path + index
     */
    public static function findChunk(string $path, int $index): ?self
    {
        return static::where('source_path', $path)
                     ->where('chunk_index', $index)
                     ->first();
    }
}
