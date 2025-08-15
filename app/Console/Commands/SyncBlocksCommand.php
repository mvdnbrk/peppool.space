<?php

namespace App\Console\Commands;

use App\Models\Block;
use App\Services\PepecoinRpcService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncBlocksCommand extends Command
{
    protected $signature = 'pepe:sync:blocks
                            {--from= : Start syncing from this block height (defaults to latest in DB + 1)}
                            {--to= : Stop syncing at this block height (defaults to current chain tip)}
                            {--limit= : Limit the total number of blocks to process}
                            {--batch=250 : Number of blocks to process in each batch}
                            {--delay=0 : Delay in milliseconds between RPC calls}
                            {--batch-delay=500 : Delay in milliseconds between batches}
                            {--force : Force sync even if blocks already exist}
                            {--genesis : Start syncing from genesis block (height 0)}';

    protected $description = 'Sync blocks from Pepecoin blockchain to database (defaults to syncing new blocks only)';

    private PepecoinRpcService $rpcService;

    private bool $shouldStop = false;

    public function __construct(PepecoinRpcService $rpcService)
    {
        parent::__construct();
        $this->rpcService = $rpcService;
    }

    public function handle(): int
    {
        // Set up graceful shutdown handling
        $this->trap([SIGINT, SIGTERM], function (int $signal) {
            $this->shouldStop = true;
            $this->newLine();
            $this->warn('🛑 Received shutdown signal. Finishing current batch and stopping gracefully...');
            $this->line('💡 Press Ctrl+C again to force quit (may cause data corruption).');
        });

        $batchSize = (int) $this->option('batch');
        $force = $this->option('force');
        $delay = (int) $this->option('delay');
        $batchDelay = (int) $this->option('batch-delay');

        try {
            $chainHeight = $this->rpcService->getBlockCount();
            $endHeight = $this->option('to') ? (int) $this->option('to') : $chainHeight;
            $startHeight = $this->determineStartHeight();

            // Apply limit if specified
            if ($this->option('limit')) {
                $limit = (int) $this->option('limit');
                $endHeight = min($endHeight, $startHeight + $limit - 1);
            }

            $this->info("Chain height: {$chainHeight}");
            $this->info("Syncing blocks from {$startHeight} to {$endHeight}");
            $this->info("Batch size: {$batchSize}");

            if ($this->option('limit')) {
                $this->info("Limit: {$this->option('limit')} blocks");
            }

            if ($delay > 0) {
                $this->info("RPC delay: {$delay}ms between calls");
            }

            if ($batchDelay > 0) {
                $this->info("Batch delay: {$batchDelay}ms between batches");
            }

            $this->newLine();
            $this->comment('Press Ctrl+C to stop gracefully (will finish current batch)');
            $this->newLine();

            if ($startHeight > $endHeight) {
                $this->info('No new blocks to sync.');

                return self::SUCCESS;
            }

            $totalBlocks = $endHeight - $startHeight + 1;
            $progressBar = $this->output->createProgressBar($totalBlocks);
            $progressBar->setFormat('Syncing: Blocks %message% [%bar%] %percent:3s%% (%current%/%max%)');
            $progressBar->setMessage("{$startHeight}-{$endHeight}");
            $progressBar->start();

            for ($height = $startHeight; $height <= $endHeight; $height += $batchSize) {
                // Check for graceful shutdown signal
                if ($this->shouldStop) {
                    $this->warn('🛑 Graceful shutdown requested. Stopping after current batch.');
                    break;
                }

                $batchEnd = min($height + $batchSize - 1, $endHeight);

                $this->syncBatch($height, $batchEnd, $progressBar, $force, $delay);

                // Add delay between batches (except for the last batch)
                if ($batchDelay > 0 && $batchEnd < $endHeight && ! $this->shouldStop) {
                    usleep($batchDelay * 1000); // Convert ms to microseconds
                }
            }

            $progressBar->finish();
            $this->newLine();
            $this->info('Block sync completed successfully!');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error syncing blocks: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function syncBatch(int $startHeight, int $endHeight, $progressBar, bool $force, int $delay = 0): void
    {
        $blocksToInsert = [];
        $heightsToProcess = [];

        // Collect heights that need processing
        for ($height = $startHeight; $height <= $endHeight; $height++) {
            if (! $force && Block::where('height', $height)->exists()) {
                $progressBar->advance();

                continue;
            }
            $heightsToProcess[] = $height;
        }

        // Process blocks in the batch
        foreach ($heightsToProcess as $index => $height) {
            // Check for graceful shutdown signal during batch processing
            if ($this->shouldStop) {
                $this->warn("🛑 Graceful shutdown requested during batch processing at block #{$height}.");
                break;
            }

            try {
                $blockHash = $this->rpcService->getBlockHash($height);

                // Add delay between RPC calls if specified
                if ($delay > 0 && $index > 0 && ! $this->shouldStop) {
                    usleep($delay * 1000); // Convert ms to microseconds
                }

                $blockData = $this->rpcService->getBlock($blockHash, 1);

                $blocksToInsert[] = [
                    'height' => $height,
                    'hash' => $blockHash,
                    'tx_count' => count($blockData['tx'] ?? []),
                    'size' => $blockData['size'] ?? 0,
                    'difficulty' => $blockData['difficulty'] ?? 0,
                    'nonce' => $blockData['nonce'] ?? 0,
                    'version' => $blockData['version'] ?? 0,
                    'merkleroot' => $blockData['merkleroot'] ?? '',
                    'chainwork' => $blockData['chainwork'] ?? '',
                    'auxpow' => isset($blockData['auxpow']) ? json_encode($blockData['auxpow']) : null,
                    'created_at' => Carbon::createFromTimestamp($blockData['time']),
                ];

                $progressBar->advance();

            } catch (\Exception $e) {
                $this->warn("Failed to sync block {$height}: ".$e->getMessage());
                $progressBar->advance();

                continue;
            }
        }

        // Batch insert all blocks at once
        if (! empty($blocksToInsert)) {
            try {
                DB::table('blocks')->upsert(
                    $blocksToInsert,
                    ['height'], // Unique columns
                    ['hash', 'tx_count', 'size', 'difficulty', 'nonce', 'version', 'merkleroot', 'chainwork', 'auxpow', 'created_at'] // Columns to update
                );
            } catch (\Exception $e) {
                $this->error('Failed to insert batch: '.$e->getMessage());
            }
        }
    }

    private function determineStartHeight(): int
    {
        // Explicit --from option takes precedence
        if ($this->option('from') !== null) {
            return (int) $this->option('from');
        }

        // --genesis flag starts from block 0
        if ($this->option('genesis')) {
            return 0;
        }

        // Default: start from latest block in DB + 1
        $lastSyncedHeight = Block::max('height') ?? -1;

        return $lastSyncedHeight + 1;
    }
}
