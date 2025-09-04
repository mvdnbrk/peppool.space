<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;

final class RawVoutData extends Data
{
    public function __construct(
        public float $value,
        public int $n,
        public ?ScriptPubKeyData $scriptPubKey,
    ) {}

    public static function fromRpc(array $payload): self
    {
        $spk = null;
        if (isset($payload['scriptPubKey']) && is_array($payload['scriptPubKey'])) {
            $spk = ScriptPubKeyData::from($payload['scriptPubKey']);
        }

        return new self(
            value: (float) ($payload['value'] ?? 0.0),
            n: (int) ($payload['n'] ?? 0),
            scriptPubKey: $spk,
        );
    }
}
