<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;

final class NetworkInfoLocalAddressData extends Data
{
    public function __construct(
        public string $address = '',
        public int $port = 0,
        public int $score = 0,
    ) {}
}
