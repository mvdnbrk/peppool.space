<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\BlockchainServiceInterface;
use App\Exceptions\RpcResponseException;
use App\Http\Controllers\Api\Concerns\HasApiResponses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class TransactionController extends Controller
{
    use HasApiResponses;

    public function __construct(
        private readonly BlockchainServiceInterface $blockchain,
    ) {}

    public function broadcast(Request $request): Response|JsonResponse
    {
        $hex = $request->getContent();

        if (empty($hex) || ! preg_match('/^[0-9a-fA-F]+$/', $hex)) {
            return $this->errorResponse('invalid_hex', 'The provided transaction hex is invalid.', Response::HTTP_BAD_REQUEST);
        }

        try {
            return response($this->blockchain->broadcastTransaction($hex), Response::HTTP_OK)
                ->header('Content-Type', 'text/plain');
        } catch (Throwable $e) {
            return $this->errorResponse(
                'broadcast_failed',
                $e->getMessage() ?: 'Failed to broadcast transaction.',
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function show(string $txid): JsonResponse
    {
        return $this->handleRequest($txid, fn ($id) => response()->json($this->blockchain->getTransaction($id)));
    }

    public function status(string $txid): JsonResponse
    {
        return $this->handleRequest($txid, fn ($id) => response()->json($this->blockchain->getTransactionStatus($id)));
    }

    public function hex(string $txid): Response
    {
        return $this->handleRequest($txid, function ($id) {
            return response($this->blockchain->getRawTransaction($id), Response::HTTP_OK)
                ->header('Content-Type', 'text/plain');
        });
    }

    public function raw(string $txid): mixed
    {
        return $this->handleRequest($txid, function ($id) {
            $hex = $this->blockchain->getRawTransaction($id);

            return response(hex2bin($hex), Response::HTTP_OK)
                ->header('Content-Type', 'application/octet-stream');
        });
    }

    private function handleRequest(string $txid, callable $callback): mixed
    {
        if (! preg_match('/^[0-9a-fA-F]{64}$/', $txid)) {
            return $this->invalidTransactionIdResponse();
        }

        try {
            return $callback($txid);
        } catch (Throwable $e) {
            $status = 0;

            if ($e instanceof RequestException) {
                $status = $e->getCode();
            } elseif ($e instanceof RpcResponseException) {
                $status = $e->httpStatus;
            } else {
                $status = (int) $e->getCode();
            }

            if ($status === Response::HTTP_NOT_FOUND || str_contains(strtolower($e->getMessage()), 'not found')) {
                return $this->transactionNotFoundResponse();
            }

            throw $e;
        }
    }
}
