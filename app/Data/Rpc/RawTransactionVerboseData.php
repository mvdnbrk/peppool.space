<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\DataCollectionOf;

final class RawTransactionVerboseData extends Data
{
    public function __construct(
        public string $hex = '',
        public string $txid = '',
        public string $hash = '',
        public int $size = 0,
        public int $vsize = 0,
        public int $version = 0,
        public int $locktime = 0,
        #[DataCollectionOf(RawVinData::class)]
        public array $vin = [],
        #[DataCollectionOf(RawVoutData::class)]
        public array $vout = [],
        public ?string $blockhash = null,
        public ?int $confirmations = null,
        public ?int $time = null,
        public ?int $blocktime = null,
    ) {}

    public static function from(mixed ...$payloads): static
    {
        $payload = $payloads[0] ?? [];
        
        // Handle vsize fallback to size
        if (is_array($payload) && !isset($payload['vsize']) && isset($payload['size'])) {
            $payload['vsize'] = $payload['size'];
        }
        
        return parent::from($payload);
    }
}
