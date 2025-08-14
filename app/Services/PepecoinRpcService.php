<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PepecoinRpcService
{
    private readonly string $host;

    private readonly int $port;

    private readonly string $username;

    private readonly string $password;

    private readonly int $timeout;

    public function __construct()
    {
        $this->host = config('pepecoin.rpc.host', '127.0.0.1');
        $this->port = config('pepecoin.rpc.port', 33873);
        $this->username = config('pepecoin.rpc.username', '');
        $this->password = config('pepecoin.rpc.password', '');
        $this->timeout = config('pepecoin.rpc.timeout', 30);
    }

    public function call(string $method, array $params = []): mixed
    {
        $url = "http://{$this->host}:{$this->port}/";

        $payload = [
            'jsonrpc' => '2.0',
            'id' => uniqid(),
            'method' => $method,
            'params' => $params,
        ];

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout($this->timeout)
                ->post($url, $payload);

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
        return Cache::remember(
            'network_info',
            Carbon::now()->addMinutes(15),
            fn (): array => $this->call('getnetworkinfo')
        );
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

    public function getRawMempool(bool $verbose = false): array
    {
        return $this->call('getrawmempool', [$verbose]);
    }

    public function getRawTransaction(string $txid, bool $verbose = false): mixed
    {
        return $this->call('getrawtransaction', [$txid, $verbose]);
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
}
