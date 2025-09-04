<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;

final class UnspentOutputData extends Data
{
    public function __construct(
        public string $txid = '',
        public int $vout = 0,
        public ?string $address = null,
        public ?string $scriptPubKey = null,
        public float $amount = 0.0,
        public int $confirmations = 0,
        public bool $spendable = false,
    ) {}
}
