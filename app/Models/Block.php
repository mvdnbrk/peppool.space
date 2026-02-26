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
}
