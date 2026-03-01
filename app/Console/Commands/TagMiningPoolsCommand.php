<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Pool;
use App\Services\MiningPoolService;
use App\Services\PepecoinRpcService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class TagMiningPoolsCommand extends Command
{
    protected $signature = 'pepe:mining:tag-history
                            {--from= : Start from this height}
                            {--limit= : Limit the number of blocks to process}
                            {--all : Process all blocks, even those already tagged}
                            {--unknown : Process only blocks currently marked as Unknown (Fast, uses local data only)}';

    protected $description = 'Identify mining pools for blocks in the database and transition NULL blocks to Unknown';

    public function __construct(
        private readonly MiningPoolService $miningPoolService,
        private readonly PepecoinRpcService $rpcService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $unknownPool = Pool::where('slug', 'unknown')->first();
        if (! $unknownPool) {
            $this->error('Unknown pool not found. Please run the MiningPoolSeeder.');

            return self::FAILURE;
        }

        $query = $this->buildBlockQuery($unknownPool);

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('No blocks to process.');

            return self::SUCCESS;
        }

        $isUnknownOnly = $this->option('unknown');
        $this->info("Processing {$total} blocks".($isUnknownOnly ? ' (Local data only)...' : '...'));
        $bar = $this->output->createProgressBar($total);
        $tagged = 0;
        $markedAsUnknown = 0;

        $query->chunkById(500, function ($blocks) use ($bar, &$tagged, &$markedAsUnknown, $unknownPool, $isUnknownOnly) {
            foreach ($blocks as $block) {
                $pool = null;
                $blockData = null;

                // 1. Try with stored auxpow (Fast, local)
                if (! empty($block->auxpow)) {
                    $pool = $this->miningPoolService->identifyFromBlock(['auxpow' => $block->auxpow]);
                }

                // 2. Fallback to RPC for full block (Slow, network)
                // We SKIP this if --unknown is passed to ensure the re-scan is instantaneous
                if (! $pool && ! $isUnknownOnly) {
                    try {
                        $blockData = $this->rpcService->getBlock($block->hash, 2);
                        $pool = $this->miningPoolService->identifyFromBlock($blockData);
                    } catch (\Exception $e) {
                        // Skip if RPC fails
                    }
                }

                if ($pool) {
                    // Update if it's a different pool (e.g. was Unknown, now Luxor)
                    if ($block->pool_id !== $pool->id) {
                        $block->pool_id = $pool->id;
                        $block->save();
                        $tagged++;
                    }

                    // Record payout address
                    $pepeAddress = null;
                    if (! empty($block->auxpow)) {
                        $pepeAddress = $block->auxpow['tx']['vout'][0]['scriptPubKey']['addresses'][0] ?? null;
                    } elseif ($blockData) {
                        $pepeAddress = $blockData['tx'][0]['vout'][0]['scriptPubKey']['addresses'][0] ?? null;
                    }

                    if ($pepeAddress) {
                        $this->miningPoolService->recordPayoutAddress($pool, $pepeAddress);
                    }
                } else {
                    // If still not found, mark as Unknown pool ID instead of leaving NULL
                    // This ensures future standard runs will skip this block.
                    if ($block->pool_id !== $unknownPool->id) {
                        $block->pool_id = $unknownPool->id;
                        $block->save();
                        $markedAsUnknown++;
                    }
                }

                $bar->advance();
            }
        }, 'height');

        $bar->finish();
        $this->newLine();
        $this->info('Tagging completed.');
        $this->info("- {$tagged} blocks identified and tagged.");
        $this->info("- {$markedAsUnknown} blocks transitioned to Unknown.");

        return self::SUCCESS;
    }

    private function buildBlockQuery(Pool $unknownPool): Builder
    {
        $query = Block::query();

        if ($this->option('from')) {
            $query->where('height', '>=', (int) $this->option('from'));
        }

        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        if ($this->option('all')) {
            return $query;
        }

        if ($this->option('unknown')) {
            return $query->where('pool_id', $unknownPool->id);
        }

        return $query->whereNull('pool_id');
    }
}
