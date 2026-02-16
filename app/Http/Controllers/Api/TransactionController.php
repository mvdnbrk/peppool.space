<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\HasApiResponses;
use App\Http\Controllers\Controller;
use App\Services\ElectrsPepeService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    use HasApiResponses;

    public function __construct(
        private readonly ElectrsPepeService $electrs
    ) {}

    public function broadcast(Request $request): Response|JsonResponse
    {
        $hex = $request->getContent();

        if (empty($hex) || ! preg_match('/^[0-9a-fA-F]+$/', $hex)) {
            return $this->errorResponse('invalid_hex', 'The provided transaction hex is invalid.', Response::HTTP_BAD_REQUEST);
        }

        try {
            return response($this->electrs->broadcastTransaction($hex), Response::HTTP_OK)
                ->header('Content-Type', 'text/plain');
        } catch (RequestException $e) {
            return $this->errorResponse(
                'broadcast_failed',
                $e->response->body() ?: 'Failed to broadcast transaction.',
                $e->getCode() ?: Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function show(string $txid): JsonResponse
    {
        return $this->handleRequest($txid, fn ($id) => response()->json($this->electrs->getTransaction($id)));
    }

    public function status(string $txid): JsonResponse
    {
        return $this->handleRequest($txid, fn ($id) => response()->json($this->electrs->getTransaction($id)->status));
    }

    public function hex(string $txid): Response
    {
        return $this->handleRequest($txid, function ($id) {
            return response($this->electrs->getRawTransaction($id), Response::HTTP_OK)
                ->header('Content-Type', 'text/plain');
        });
    }

    public function raw(string $txid): mixed
    {
        return $this->handleRequest($txid, function ($id) {
            $hex = $this->electrs->getRawTransaction($id);

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
        } catch (RequestException $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                return $this->transactionNotFoundResponse();
            }

            throw $e;
        }
    }
}
