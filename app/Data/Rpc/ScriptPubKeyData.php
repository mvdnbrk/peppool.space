<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;

final class ScriptPubKeyData extends Data
{
    /** @param string[] $addresses */
    public function __construct(
        public string $asm = '',
        public string $hex = '',
        public ?int $reqSigs = null,
        public string $type = '',
        public array $addresses = [],
    ) {}
}
