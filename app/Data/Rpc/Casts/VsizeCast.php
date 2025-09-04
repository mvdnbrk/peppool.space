<?php

declare(strict_types=1);

namespace App\Data\Rpc\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Creation\CreationContext;

final class VsizeCast implements Cast
{
    /**
     * @param array<string,mixed> $properties
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if ($value !== null) {
            return (int) $value;
        }

        if (array_key_exists('size', $properties)) {
            return (int) $properties['size'];
        }

        return 0;
    }
}
