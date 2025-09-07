<?php

namespace App\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

class RpcResponseException extends RuntimeException
{
    public function __construct(
        public readonly string $method,
        public readonly int $httpStatus = 0,
        public readonly ?int $rpcCode = null,
        string $message = 'RPC request failed',
        public readonly ?Response $response = null,
    ) {
        parent::__construct($message, $rpcCode ?? 0);
    }
}
