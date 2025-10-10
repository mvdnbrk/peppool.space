<?php

namespace App\Console\Commands;

use App\Services\PepecoinRpcService;
use Exception;
use Illuminate\Console\Command;

class PepeRpcHealthCommand extends Command
{
    protected $signature = 'pepe:rpc:health {--json : Output results in JSON format}';

    protected $description = 'Check Pepecoin RPC connection health and display node information';

    public function handle(PepecoinRpcService $rpcService): int
    {
        $this->info('🔍 Testing Pepecoin RPC connection...');
        $this->newLine();

        try {
            if (! $rpcService->testConnection()) {
                $this->error('❌ RPC connection failed');

                return self::FAILURE;
            }

            $this->info('✅ RPC connection successful');
            $this->newLine();

            $this->info('📊 Fetching blockchain information...');
            $blockchainInfo = $rpcService->getBlockchainInfo();

            $this->info('🔄 Fetching mempool information...');
            $mempoolInfo = $rpcService->getMempoolInfo();

            $this->info('🌐 Fetching network information...');
            $networkInfo = $rpcService->getNetworkInfo();

            $results = [
                'connection' => 'healthy',
                'blockchain' => $blockchainInfo,
                'mempool' => $mempoolInfo,
                'network' => $networkInfo,
                'timestamp' => now()->toISOString(),
            ];

            if ($this->option('json')) {
                $this->line(json_encode($results, JSON_PRETTY_PRINT));

                return self::SUCCESS;
            }

            // Display formatted results
            $this->displayResults($results);

            return self::SUCCESS;

        } catch (Exception $e) {
            $error = [
                'connection' => 'failed',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];

            if ($this->option('json')) {
                $this->line(json_encode($error, JSON_PRETTY_PRINT));

                return self::FAILURE;
            }

            $this->error('❌ Health check failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('📋 Health Check Results');
        $this->line('═══════════════════════');

        // Blockchain info
        $blockchain = $results['blockchain'];
        $this->info('🔗 Blockchain Information:');
        $this->line("  Chain: {$blockchain->chain}");
        $this->line('  Blocks: '.number_format($blockchain->blocks));
        $this->line("  Best Block Hash: {$blockchain->bestBlockHash}");
        $this->line('  Difficulty: '.number_format($blockchain->difficulty, 8));
        $this->line('  Verification Progress: '.round($blockchain->verificationProgress * 100, 2).'%');

        if ($blockchain->sizeOnDisk > 0) {
            $this->line('  Chain Size: '.$this->formatBytes($blockchain->sizeOnDisk));
        }

        $this->newLine();

        // Mempool info
        $mempool = $results['mempool'];
        $this->info('🔄 Mempool Information:');
        $this->line('  Transactions: '.number_format($mempool->size));
        $this->line('  Memory Usage: '.$this->formatBytes($mempool->bytes));
        $this->line('  Min Fee Rate: '.($mempool->mempoolMinFee > 0 ? number_format($mempool->mempoolMinFee, 8) : 'N/A').' PEPE/kB');

        if ($mempool->maxMempool > 0) {
            $this->line('  Max Mempool: '.$this->formatBytes($mempool->maxMempool));
        }

        $this->newLine();

        // Network info
        $network = $results['network'];
        $this->info('🌐 Network Information:');
        $this->line("  Version: {$network->version}");
        $this->line("  Subversion: {$network->subversion}");
        $this->line("  Protocol Version: {$network->protocolVersion}");
        $this->line("  Connections: {$network->connections}");
        $this->line('  Network Active: '.($network->networkActive ? 'Yes' : 'No'));

        $this->newLine();
        $this->info('✅ All systems operational!');
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }
}
