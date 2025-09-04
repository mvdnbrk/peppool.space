<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;

final class RawVinData extends Data
{
    public function __construct(
        public ?string $txid = null,
        public ?int $vout = null,
        public ?ScriptSigData $scriptSig = null,
        public ?int $sequence = null,
        public ?string $coinbase = null,
    ) {}
}
