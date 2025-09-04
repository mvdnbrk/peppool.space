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
        public int $version = 0,
        public string $subversion = '',
        #[MapInputName('protocolversion')]
        public int $protocolVersion = 0,
        #[MapInputName('localservices')]
        public string $localServices = '',
        #[MapInputName('localrelay')]
        public bool $localRelay = false,
        #[MapInputName('timeoffset')]
        public int $timeOffset = 0,
        #[MapInputName('networkactive')]
        public bool $networkActive = false,
        public int $connections = 0,
        #[DataCollectionOf(NetworkInfoNetworkData::class)]
        public array $networks = [],
        #[MapInputName('relayfee')]
        public float $relayFee = 0.0,
        #[MapInputName('incrementalfee')]
        public float $incrementalFee = 0.0,
        #[MapInputName('softdustlimit')]
        public float $softDustLimit = 0.0,
        #[MapInputName('harddustlimit')]
        public float $hardDustLimit = 0.0,
        #[MapInputName('localaddresses')]
        #[DataCollectionOf(NetworkInfoLocalAddressData::class)]
        public array $localAddresses = [],
        public string $warnings = '',
    ) {}
}
