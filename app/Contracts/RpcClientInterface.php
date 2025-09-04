<?php

declare(strict_types=1);

namespace App\Contracts;

interface RpcClientInterface
{
    public function call(string $method, array $params = []): mixed;

    public function batchCall(array $calls): array;
}
