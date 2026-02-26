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
}
