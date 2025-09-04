<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

final class NetworkInfoNetworkData extends Data
{
    public function __construct(
        public string $name = '',
        public bool $limited = false,
        public bool $reachable = false,
        public string $proxy = '',
        #[MapInputName('proxy_randomize_credentials')]
        public bool $proxyRandomizeCredentials = false,
    ) {}
}
