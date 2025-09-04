<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

final class ValidateAddressData extends Data
{
    public function __construct(
        #[MapInputName('isvalid')]
        public bool $isValid = false,
        #[MapInputName('ismine')]
        public bool $isMine = false,
        public ?string $address = null,
        public ?string $scriptPubKey = null,
    ) {}
}
