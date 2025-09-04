<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;

final class TransactionData extends Data
{
    public function __construct(
        public string $txid = '',
        public int $version = 0,
        public int $size = 0,
        public int $vsize = 0,
        public int $locktime = 0,
        public ?int $time = null,
        public ?int $blocktime = null,
        public ?string $blockhash = null,
    ) {}

    public static function from(mixed ...$payloads): static
    {
        $payload = $payloads[0] ?? [];

        // Handle vsize fallback to size
        if (is_array($payload) && ! isset($payload['vsize']) && isset($payload['size'])) {
            $payload['vsize'] = $payload['size'];
        }

        return parent::from($payload);
    }
}
