<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;

final class RawVoutData extends Data
{
    public function __construct(
        public float $value = 0.0,
        public int $n = 0,
        public ?ScriptPubKeyData $scriptPubKey = null,
    ) {}
}
