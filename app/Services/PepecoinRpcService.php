<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PepecoinRpcService
{
    private readonly string $host;

    private readonly string $port;

    private readonly string $username;

    private readonly string $password;

    private readonly int $timeout;

    private readonly string $url;

    public function __construct()
    {
        $this->host = config('pepecoin.rpc.host', '127.0.0.1');
        $this->port = config('pepecoin.rpc.port', '3873');
        $this->username = config('pepecoin.rpc.username', '');
        $this->password = config('pepecoin.rpc.password', '');
        $this->timeout = config('pepecoin.rpc.timeout', 30);
        $this->url = "http://{$this->host}:{$this->port}/";
    }

    public function call(string $method, array $params = []): mixed
    {
        $payload = [
            'jsonrpc' => '2.0',
            'id' => uniqid(),
            'method' => $method,
            'params' => $params,
        ];

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout($this->timeout)
                ->post($this->url, $payload);

            if (! $response->successful()) {
                throw new Exception("RPC request failed with status: {$response->status()}");
            }

            $data = $response->json();

            if (isset($data['error']) && $data['error'] !== null) {
                throw new Exception("RPC error: {$data['error']['message']} (code: {$data['error']['code']})");
            }

            return $data['result'] ?? [];

        } catch (Exception $e) {
            Log::error('Pepecoin RPC call failed', [
                'method' => $method,
                'params' => $params,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function batchCall(array $calls): array
    {
        $payload = [];
        foreach ($calls as $call) {
            $payload[] = [
                'jsonrpc' => '2.0',
                'id' => uniqid(),
                'method' => $call['method'],
                'params' => $call['params'] ?? [],
            ];
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout($this->timeout)
                ->post($this->url, $payload);

            if (! $response->successful()) {
                throw new Exception("Batch RPC request failed with status: {$response->status()}");
            }

            $data = $response->json();

            if (! is_array($data) || count($data) !== count($calls)) {
                throw new Exception('Invalid batch response format');
            }

            $results = [];
            foreach ($data as $item) {
                if (isset($item['error']) && $item['error'] !== null) {
                    throw new Exception("Batch RPC error: {$item['error']['message']} (code: {$item['error']['code']})");
                }
                $results[] = $item['result'] ?? [];
            }

            return $results;

        } catch (Exception $e) {
            Log::error('Pepecoin batch RPC call failed', [
                'calls' => $calls,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
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
