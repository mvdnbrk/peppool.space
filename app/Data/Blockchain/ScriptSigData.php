<?php

declare(strict_types=1);

namespace App\Data\Blockchain;

use Spatie\LaravelData\Data;

final class ScriptSigData extends Data
{
    public function __construct(
        public string $asm = '',
        public string $hex = '',
    ) {}
}
