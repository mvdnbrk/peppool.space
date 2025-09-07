<?php

namespace App\Services;

use App\Contracts\RpcClientInterface;
use App\Exceptions\RpcResponseException;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PepecoinRpcService implements RpcClientInterface
{
    private readonly string $url;

    private readonly string $host;

    private readonly string $port;

    private readonly string $username;

    private readonly string $password;

    private readonly int $timeout;

    public function __construct(
        ?string $host = null,
        ?string $port = null,
        ?string $username = null,
        ?string $password = null,
        ?int $timeout = null,
    ) {
        $this->host = $host ?? config('pepecoin.rpc.host', '127.0.0.1');
        $this->port = $port ?? config('pepecoin.rpc.port', '3873');
        $this->username = $username ?? config('pepecoin.rpc.username', '');
        $this->password = $password ?? config('pepecoin.rpc.password', '');
        $this->timeout = $timeout ?? config('pepecoin.rpc.timeout', 30);

        $this->url = Str::of('http://')
            ->append($this->host)
            ->append(':')
            ->append($this->port)
            ->append('/')
            ->toString();
    }

    public function call(string $method, array $params = []): mixed
    {
        $response = $this->makeRequest($this->buildPayload($method, $params));

        return $this->handleResponse($response, $method);
    }

    public function batchCall(array $calls): array
    {
        $payload = collect($calls)->map(fn ($call) => $this->buildPayload(
            $call['method'],
            $call['params'] ?? []
        ))->toArray();

        $response = $this->makeRequest($payload);
        $data = $response->collect();

        $this->validateBatchResponse($data, $calls, $response);

        return $data
            ->map(fn ($item) => $this->extractResult($item, $response))
            ->toArray();
    }

    private function buildPayload(string $method, array $params = []): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => uniqid(),
            'method' => $method,
            'params' => $params,
        ];
    }

    private function makeRequest(array $payload)
    {
        return rescue(
            fn () => Http::withBasicAuth($this->username, $this->password)
                ->timeout($this->timeout)
                ->throw()
                ->post($this->url, $payload),
            fn ($e) => $this->handleHttpException($e, $payload),
            false
        );
    }

    private function handleResponse($response, string $method): mixed
    {
        $data = $response->json();

        if (isset($data['error']) && $data['error'] !== null) {
            $this->throwRpcError($data['error'], $method, $response);
        }

        return $data['result'] ?? [];
    }

    private function validateBatchResponse(Collection $data, array $calls, $response): void
    {
        if ($data->count() !== count($calls)) {
            throw new RpcResponseException(
                method: 'batch',
                httpStatus: $response->status(),
                rpcCode: null,
                message: 'Invalid batch response format',
                response: $response,
            );
        }
    }

    private function extractResult(array $item, $response): mixed
    {
        if (isset($item['error']) && $item['error'] !== null) {
            $this->throwRpcError($item['error'], 'batch-item', $response);
        }

        return $item['result'] ?? [];
    }

    private function throwRpcError(array $error, string $method, $response): never
    {
        $rpcCode = $error['code'] ?? null;
        $rpcMessage = $error['message'] ?? 'Unknown RPC error';

        throw new RpcResponseException(
            method: $method,
            httpStatus: $response->status(),
            rpcCode: $rpcCode,
            message: "RPC error: {$rpcMessage} (code: {$rpcCode})",
            response: $response,
        );
    }

    private function handleHttpException($exception, array $payload): never
    {
        $method = is_array($payload) && isset($payload[0]) ? 'batch' : ($payload['method'] ?? 'unknown');

        Log::error('Pepecoin RPC call failed', [
            'method' => $method,
            'payload' => $payload,
            'error' => $exception->getMessage(),
        ]);

        throw $exception;
    }

    public function testConnection(): bool
    {
        try {
            $this->getBlockchainInfo();

            return true;
        } catch (Exception $e) {
            Log::warning('RPC connection test failed: '.$e->getMessage());

            return false;
        }
    }

    public function getBlockchainInfo(): array
    {
        return $this->call('getblockchaininfo');
    }

    public function getMempoolInfo(): array
    {
        return $this->call('getmempoolinfo');
    }

    public function getNetworkInfo(): array
    {
        return $this->call('getnetworkinfo');
    }

    public function getBlockCount(): int
    {
        return $this->call('getblockcount');
    }

    public function getBlockHash(int $height): string
    {
        return $this->call('getblockhash', [$height]);
    }

    public function getBlock(string $hash, int $verbosity = 1): array
    {
        return $this->call('getblock', [$hash, $verbosity]);
    }

    public function getRawTransaction(string $txid, bool $verbose = true): array
    {
        return $this->call('getrawtransaction', [$txid, $verbose]);
    }

    public function getRawMempool(bool $verbose = false): array
    {
        return $this->call('getrawmempool', [$verbose]);
    }

    public function getMempoolEntry(string $txid): array
    {
        return $this->call('getmempoolentry', [$txid]);
    }

    public function getTxOutSetInfo(): array
    {
        return $this->call('gettxoutsetinfo');
    }
}
