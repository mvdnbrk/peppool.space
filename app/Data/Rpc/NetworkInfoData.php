<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\DataCollectionOf;

final class NetworkInfoData extends Data
{
    /** @param NetworkInfoNetworkData[] $networks */
    /** @param NetworkInfoLocalAddressData[] $localAddresses */
    public function __construct(
        public int $version,
        public string $subversion,
        #[MapInputName('protocolversion')]
        public int $protocolVersion,
        #[MapInputName('localservices')]
        public string $localServices,
        #[MapInputName('localrelay')]
        public bool $localRelay,
        #[MapInputName('timeoffset')]
        public int $timeOffset,
        #[MapInputName('networkactive')]
        public bool $networkActive,
        public int $connections,
        #[DataCollectionOf(NetworkInfoNetworkData::class)]
        public array $networks,
        #[MapInputName('relayfee')]
        public float $relayFee,
        #[MapInputName('incrementalfee')]
        public float $incrementalFee,
        #[MapInputName('softdustlimit')]
        public float $softDustLimit,
        #[MapInputName('harddustlimit')]
        public float $hardDustLimit,
        #[MapInputName('localaddresses')]
        #[DataCollectionOf(NetworkInfoLocalAddressData::class)]
        public array $localAddresses,
        public string $warnings,
    ) {}

    public static function fromRpc(array $payload): self
    {
        $networks = [];
        if (isset($payload['networks']) && is_array($payload['networks'])) {
            foreach ($payload['networks'] as $n) {
                if (is_array($n)) {
                    $networks[] = NetworkInfoNetworkData::from($n);
                }
            }
        }

        $localAddresses = [];
        if (isset($payload['localaddresses']) && is_array($payload['localaddresses'])) {
            foreach ($payload['localaddresses'] as $la) {
                if (is_array($la)) {
                    $localAddresses[] = NetworkInfoLocalAddressData::from($la);
                }
            }
        }

        return new self(
            version: (int) ($payload['version'] ?? 0),
            subversion: (string) ($payload['subversion'] ?? ''),
            protocolVersion: (int) ($payload['protocolversion'] ?? 0),
            localServices: (string) ($payload['localservices'] ?? ''),
            localRelay: (bool) ($payload['localrelay'] ?? false),
            timeOffset: (int) ($payload['timeoffset'] ?? 0),
            networkActive: (bool) ($payload['networkactive'] ?? false),
            connections: (int) ($payload['connections'] ?? 0),
            networks: $networks,
            relayFee: (float) ($payload['relayfee'] ?? 0.0),
            incrementalFee: (float) ($payload['incrementalfee'] ?? 0.0),
            softDustLimit: (float) ($payload['softdustlimit'] ?? 0.0),
            hardDustLimit: (float) ($payload['harddustlimit'] ?? 0.0),
            localAddresses: $localAddresses,
            warnings: (string) ($payload['warnings'] ?? ''),
        );
    }
}
