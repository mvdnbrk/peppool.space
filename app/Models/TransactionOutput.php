<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionOutput extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tx_id',
        'output_index',
        'address',
        'amount',
        'script_pub_key',
        'script_type',
        'op_return_data',
        'op_return_decoded',
        'op_return_protocol',
        'is_spent',
        'spent_by_tx_id',
        'spent_by_input_index',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:8',
            'is_spent' => 'boolean',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'tx_id', 'tx_id');
    }

    public function spentByTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'spent_by_tx_id', 'tx_id');
    }

    public function spentByInput(): HasOne
    {
        return $this->hasOne(TransactionInput::class, 'prev_tx_id', 'tx_id')
            ->where('prev_vout', $this->output_index);
    }

    public function getIsOpReturnAttribute(): bool
    {
        return $this->script_type === 'nulldata' && ! empty($this->op_return_data);
    }

    public function getHasReadableDataAttribute(): bool
    {
        return ! empty($this->op_return_decoded);
    }

    public function getProtocolDisplayNameAttribute(): ?string
    {
        return match ($this->op_return_protocol) {
            'omni' => 'Omni Layer',
            'counterparty' => 'Counterparty',
            'simple_ledger' => 'Simple Ledger Protocol',
            'open_assets' => 'Open Assets',
            'storj' => 'Storj',
            'colored_coins' => 'Colored Coins',
            'text' => 'Text Message',
            'url' => 'URL',
            'email' => 'Email',
            'uuid' => 'UUID',
            'unknown' => 'Unknown Protocol',
            default => null,
        };
    }
}
