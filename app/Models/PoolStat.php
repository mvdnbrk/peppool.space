<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoolStat extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'hashrate_timestamp',
        'avg_hashrate',
        'pool_id',
        'share',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'hashrate_timestamp' => 'datetime',
            'avg_hashrate' => 'double',
            'share' => 'float',
        ];
    }

    public function pool(): BelongsTo
    {
        return $this->belongsTo(Pool::class);
    }

    /**
     * Get the sum of all pool hashrates for the latest timestamp.
     */
    public static function getLatestNetworkHashrate(): float
    {
        $latestTimestamp = static::where('type', 'daily')->max('hashrate_timestamp');

        if (! $latestTimestamp) {
            return 0.0;
        }

        return (float) static::where('type', 'daily')
            ->where('hashrate_timestamp', $latestTimestamp)
            ->sum('avg_hashrate');
    }
}
