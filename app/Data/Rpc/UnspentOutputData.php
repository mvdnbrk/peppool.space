<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class UnspentOutputData
{
    public function __construct(
        public string $txid,
        public int $vout,
        public ?string $address,
        public ?string $scriptPubKey,
        public float $amount,
        public int $confirmations,
        public bool $spendable,
    ) {}

    public static function fromRpc(array $payload): self
    {
        return new self(
            txid: (string) ($payload['txid'] ?? ''),
            vout: (int) ($payload['vout'] ?? 0),
            address: isset($payload['address']) && is_string($payload['address']) ? $payload['address'] : null,
            scriptPubKey: isset($payload['scriptPubKey']) && is_string($payload['scriptPubKey']) ? $payload['scriptPubKey'] : null,
            amount: (float) ($payload['amount'] ?? 0.0),
            confirmations: (int) ($payload['confirmations'] ?? 0),
            spendable: (bool) ($payload['spendable'] ?? false),
        );
    }
}
