<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class ScriptSigData
{
    public function __construct(
        public string $asm,
        public string $hex,
    ) {}

    public static function fromRpc(array $payload): self
    {
        return new self(
            asm: (string) ($payload['asm'] ?? ''),
            hex: (string) ($payload['hex'] ?? ''),
        );
    }
}
