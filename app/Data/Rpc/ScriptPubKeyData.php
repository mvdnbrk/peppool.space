<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;

final class ScriptPubKeyData extends Data
{
    /** @param string[] $addresses */
    public function __construct(
        public string $asm,
        public string $hex,
        public ?int $reqSigs,
        public string $type,
        public array $addresses,
    ) {}

    public static function fromRpc(array $payload): self
    {
        $addresses = [];
        if (isset($payload['addresses']) && is_array($payload['addresses'])) {
            foreach ($payload['addresses'] as $addr) {
                if (is_string($addr)) {
                    $addresses[] = $addr;
                }
            }
        }

        return new self(
            asm: (string) ($payload['asm'] ?? ''),
            hex: (string) ($payload['hex'] ?? ''),
            reqSigs: isset($payload['reqSigs']) ? (int) $payload['reqSigs'] : null,
            type: (string) ($payload['type'] ?? ''),
            addresses: $addresses,
        );
    }
}
