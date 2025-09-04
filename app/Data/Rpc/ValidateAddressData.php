<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

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
