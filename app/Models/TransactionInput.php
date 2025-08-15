<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionInput extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tx_id',
        'input_index',
        'prev_tx_id',
        'prev_vout',
        'address',
        'amount',
        'script_sig',
        'sequence',
        'coinbase_data',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'sequence' => 'integer',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'tx_id', 'tx_id');
    }

    public function previousOutput(): BelongsTo
    {
        return $this->belongsTo(TransactionOutput::class, 'prev_txid', 'txid')
            ->where('output_index', $this->prev_vout);
    }

    public function getIsCoinbaseAttribute(): bool
    {
        return ! empty($this->coinbase_data);
    }
}
