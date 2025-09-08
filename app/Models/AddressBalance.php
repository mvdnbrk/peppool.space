<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AddressBalance extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'address',
        'balance',
        'total_received',
        'total_sent',
        'tx_count',
        'first_seen',
        'last_activity',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'total_received' => 'decimal:8',
        'total_sent' => 'decimal:8',
        'first_seen' => 'datetime',
        'last_activity' => 'datetime',
    ];

    public function outputs(): HasMany
    {
        return $this->hasMany(TransactionOutput::class, 'address', 'address');
    }

    public function inputs(): HasMany
    {
        return $this->hasMany(TransactionInput::class, 'address', 'address');
    }

    public function unspentOutputs(): HasMany
    {
        return $this->hasMany(TransactionOutput::class, 'address', 'address')
            ->where('is_spent', false);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->last_activity && $this->last_activity->gt(now()->subDays(30));
    }

    public function getRichListRankAttribute(): ?int
    {
        return static::where('balance', '>', $this->balance)->count() + 1;
    }

    public function scopeRichList($query, int $limit = 100)
    {
        return $query->orderByDesc('balance')
            ->where('balance', '>', 0)
            ->limit($limit);
    }

    public function scopeActive($query)
    {
        return $query->where('last_activity', '>', now()->subDays(30));
    }
}
