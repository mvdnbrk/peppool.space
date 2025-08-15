<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'tx_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'tx_id',
        'block_height',
        'size',
        'fee',
        'version',
        'locktime',
        'is_coinbase',
    ];

    // Accessor for backward compatibility
    public function getTxidAttribute(): string
    {
        return $this->tx_id;
    }

    protected $casts = [
        'is_coinbase' => 'boolean',
        'fee' => 'decimal:8',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_height', 'height');
    }

    public function inputs(): HasMany
    {
        return $this->hasMany(TransactionInput::class);
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(TransactionOutput::class);
    }

    public function opReturnOutputs(): HasMany
    {
        return $this->hasMany(TransactionOutput::class)->whereNotNull('op_return_data');
    }

    public function getInputValueAttribute(): float
    {
        return $this->inputs->sum('amount') ?? 0;
    }

    public function getOutputValueAttribute(): float
    {
        return $this->outputs->sum('amount') ?? 0;
    }

    public function getCalculatedFeeAttribute(): float
    {
        if ($this->is_coinbase) {
            return 0;
        }

        return max(0, $this->input_value - $this->output_value);
    }

    public function getConfirmationsAttribute(): int
    {
        // Get current blockchain height from cache or RPC
        $currentHeight = cache()->remember('current_block_height', 60, function () {
            try {
                return app(\App\Services\PepecoinRpcService::class)->getBlockCount();
            } catch (\Exception $e) {
                // Fallback to highest block in database if RPC fails
                return \App\Models\Block::max('height') ?? 0;
            }
        });

        return max(0, $currentHeight - $this->block_height + 1);
    }

    public function getBlockHashAttribute(): ?string
    {
        return $this->block?->hash;
    }

    public function getCreatedAtAttribute(): ?\Carbon\Carbon
    {
        return $this->block?->created_at;
    }

    public function getTimestampAttribute(): ?\Carbon\Carbon
    {
        return $this->block?->created_at;
    }
}
