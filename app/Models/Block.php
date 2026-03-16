<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'inscription_count',
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

    /**
     * Estimate Scrypt hashrate based on difficulty and time window.
     * Formula: (AvgDifficulty * 2^32 * BlockCount) / TimeInSeconds
     */
    public static function estimateHashrate(float $avgDifficulty, int $blockCount, int $seconds): float
    {
        if ($seconds <= 0 || $blockCount <= 0) {
            return 0.0;
        }

        return ($avgDifficulty * 4294967296.0 * $blockCount) / $seconds;
    }
}
