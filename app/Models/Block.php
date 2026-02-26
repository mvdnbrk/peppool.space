<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Block extends Model
{
    /** @use HasFactory<\Database\Factories\BlockFactory> */
    use HasFactory;

    protected $primaryKey = 'height';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'height',
        'pool_id',
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

    public function pool(): BelongsTo
    {
        return $this->belongsTo(Pool::class);
    }

    public static function getLatestBlocks(int $limit = 10): Collection
    {
        return static::orderBy('height', 'desc')
            ->select(['height', 'hash', 'created_at', 'tx_count', 'size', 'pool_id'])
            ->with('pool')
            ->take($limit)
            ->get()
            ->map(fn ($block): array => [
                'height' => $block->height,
                'hash' => $block->hash,
                'time' => $block->created_at->timestamp,
                'tx_count' => $block->tx_count,
                'size' => $block->size,
                'pool' => $block->pool ? [
                    'name' => $block->pool->name,
                    'slug' => $block->pool->slug,
                ] : null,
            ]);
    }
}
