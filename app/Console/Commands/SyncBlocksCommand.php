<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\FetchBlockInscriptionCount;
use App\Models\Block;
use App\Services\MiningPoolService;
use App\Services\PepecoinRpcService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncBlocksCommand extends Command
{
    protected $signature = 'pepe:sync:blocks
                            {--from= : Start syncing from this block height (defaults to latest in DB + 1)}
                            {--to= : Stop syncing at this block height (defaults to current chain tip)}
                            {--limit=500 : Maximum number of blocks to process per run}';

    protected $description = 'Sync blocks from Pepecoin blockchain to database';

    private bool $shouldStop = false;

    public function __construct(
        private readonly PepecoinRpcService $rpcService,
        private readonly MiningPoolService $miningPoolService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->trap([SIGINT, SIGTERM], function () {
            $this->shouldStop = true;
            $this->newLine();
            $this->warn('Received shutdown signal. Finishing current batch...');
        });

        try {
            $chainHeight = $this->rpcService->getBlockCount();
            $startHeight = $this->option('from') !== null
                ? (int) $this->option('from')
                : (Block::max('height') ?? -1) + 1;
            $limit = (int) $this->option('limit');
            $endHeight = $this->option('to') !== null
                ? (int) $this->option('to')
                : $chainHeight;
            $endHeight = min($endHeight, $startHeight + $limit - 1);

            if ($startHeight > $endHeight) {
                $this->info('No new blocks to sync.');

                return self::SUCCESS;
            }

            $totalBlocks = $endHeight - $startHeight + 1;
            $progressBar = $this->output->createProgressBar($totalBlocks);
            $progressBar->setFormat('Syncing: %current%/%max% [%bar%] %percent:3s%%');
            $progressBar->start();

            for ($height = $startHeight; $height <= $endHeight && ! $this->shouldStop; $height += 250) {
                $batchEnd = min($height + 249, $endHeight);
                $this->syncBatch($height, $batchEnd, $progressBar);
            }

            $progressBar->finish();
            $this->newLine();

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error syncing blocks: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function syncBatch(int $startHeight, int $endHeight, $progressBar): void
    {
        $blocksToInsert = [];

        for ($height = $startHeight; $height <= $endHeight && ! $this->shouldStop; $height++) {
            try {
                $blockHash = $this->rpcService->getBlockHash($height);
                $blockData = $this->rpcService->getBlock($blockHash, 2);

                $pool = $this->miningPoolService->identifyFromBlock($blockData);

                if ($pool) {
                    $pepeAddress = $blockData['tx'][0]['vout'][0]['scriptPubKey']['addresses'][0] ?? null;
                    $this->miningPoolService->recordPayoutAddress($pool, $pepeAddress);
                }

                $blocksToInsert[] = [
                    'height' => $height,
                    'pool_id' => $pool?->id,
                    'hash' => $blockHash,
                    'previous_hash' => $blockData['previousblockhash'] ?? null,
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
            }
        }

        if (! empty($blocksToInsert)) {
            DB::table('blocks')->upsert(
                $blocksToInsert,
                ['height'],
                ['pool_id', 'hash', 'previous_hash', 'tx_count', 'size', 'difficulty', 'nonce', 'version', 'merkleroot', 'chainwork', 'auxpow', 'created_at']
            );

            foreach ($blocksToInsert as $block) {
                FetchBlockInscriptionCount::dispatch($block['height']);
            }
        }
    }
}
