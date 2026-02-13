<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Block extends Model
{
    use HasFactory;

    protected $primaryKey = 'height';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'height',
        'hash',
        'tx_count',
        'size',
        'difficulty',
        'nonce',
        'version',
        'merkleroot',
        'chainwork',
        'auxpow',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'auxpow' => 'array',
        ];
    }

    public static function getLatestBlocks(int $limit = 10): Collection
    {
        return static::orderBy('height', 'desc')
            ->select(['height', 'hash', 'created_at', 'tx_count', 'size'])
            ->take($limit)
            ->get()
            ->map(fn ($block): array => [
                'height' => $block->height,
                'hash' => $block->hash,
                'time' => $block->created_at->timestamp,
                'tx_count' => $block->tx_count,
                'size' => $block->size,
            ]);
    }
}
