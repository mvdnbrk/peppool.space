<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class SoftforkData extends Data
{
    public function __construct(
        public string $id = '',
        public int $version = 0,
        #[MapInputName('reject')]
        public array $reject = [],
    ) {}
}
