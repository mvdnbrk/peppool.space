<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ElectrsPepeService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function __construct(
        private readonly ElectrsPepeService $electrs
    ) {}

    public function show(string $txid): mixed
    {
        return $this->handleRequest($txid, fn ($id) => $this->electrs->getTransaction($id));
    }

    public function status(string $txid): mixed
    {
        return $this->handleRequest($txid, fn ($id) => $this->electrs->getTransaction($id)->status);
    }

    public function hex(string $txid): mixed
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
            return response('Invalid hex string', Response::HTTP_BAD_REQUEST)
                ->header('Content-Type', 'text/plain');
        }

        try {
            return $callback($txid);
        } catch (RequestException $e) {
            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                return response('Transaction not found', Response::HTTP_NOT_FOUND)
                    ->header('Content-Type', 'text/plain');
            }

            throw $e;
        }
    }
}
