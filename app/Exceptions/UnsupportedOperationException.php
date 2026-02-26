<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class UnsupportedOperationException extends RuntimeException
{
    public static function electrsRequired(string $method): self
    {
        return new self(
            "The '{$method}' operation requires electrs-pepe. "
            . 'Install it from https://github.com/mvdnbrk/electrs-pepe'
        );
    }
}
