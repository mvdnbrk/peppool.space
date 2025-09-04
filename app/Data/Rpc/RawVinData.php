<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class RawVinData
{
    public function __construct(
        public ?string $txid,
        public ?int $vout,
        public ?ScriptSigData $scriptSig,
        public ?int $sequence,
        public ?string $coinbase,
    ) {}

    public static function fromRpc(array $payload): self
    {
        $scriptSig = null;
        if (isset($payload['scriptSig']) && is_array($payload['scriptSig'])) {
            $scriptSig = ScriptSigData::fromRpc($payload['scriptSig']);
        }

        return new self(
            txid: isset($payload['txid']) ? (string) $payload['txid'] : null,
            vout: isset($payload['vout']) ? (int) $payload['vout'] : null,
            scriptSig: $scriptSig,
            sequence: isset($payload['sequence']) ? (int) $payload['sequence'] : null,
            coinbase: isset($payload['coinbase']) ? (string) $payload['coinbase'] : null,
        );
    }
}
