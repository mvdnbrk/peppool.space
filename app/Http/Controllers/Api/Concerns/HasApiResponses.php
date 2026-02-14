<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait HasApiResponses
{
    protected function errorResponse(string $error, string $message, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'message' => $message,
            'code' => $status,
        ], $status);
    }

    protected function invalidAddressResponse(): JsonResponse
    {
        return $this->errorResponse('invalid_address', 'The provided address is invalid.', Response::HTTP_BAD_REQUEST);
    }

    protected function addressNotFoundResponse(): JsonResponse
    {
        return $this->errorResponse('address_not_found', 'The requested address could not be found.', Response::HTTP_NOT_FOUND);
    }

    protected function transactionNotFoundResponse(): JsonResponse
    {
        return $this->errorResponse('transaction_not_found', 'The requested transaction could not be found.', Response::HTTP_NOT_FOUND);
    }

    protected function invalidTransactionIdResponse(): JsonResponse
    {
        return $this->errorResponse('invalid_txid', 'The provided transaction ID is invalid.', Response::HTTP_BAD_REQUEST);
    }
}
