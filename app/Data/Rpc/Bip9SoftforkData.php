<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;

final class Bip9SoftforkData extends Data
{
    public function __construct(
        public string $status = '',
        public int $startTime = 0,
        public int $timeout = 0,
        public int $since = 0,
    ) {}
}
