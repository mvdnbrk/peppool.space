<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Block;
use App\Services\MiningPoolService;
use Illuminate\Console\Command;

class TagMiningPoolsCommand extends Command
{
    protected $signature = 'pepe:mining:tag-history
                            {--from= : Start from this height}
                            {--limit= : Limit the number of blocks to process}
                            {--all : Process all blocks, even those already tagged}';

    protected $description = 'Identify mining pools for blocks already in the database';

    public function __construct(
        private readonly MiningPoolService $miningPoolService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $query = Block::query();

        if (! $this->option('all')) {
            $query->whereNull('pool_id');
        }

        if ($this->option('from')) {
            $query->where('height', '>=', (int) $this->option('from'));
        }

        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No blocks to process.');

            return self::SUCCESS;
        }

        $this->info("Processing {$total} blocks...");
        $bar = $this->output->createProgressBar($total);
        $tagged = 0;

        // Note: chunkById ignores any previous order by and uses the ID column.
        $query->chunkById(500, function ($blocks) use ($bar, &$tagged) {
            foreach ($blocks as $block) {
                if (! empty($block->auxpow)) {
                    $pool = $this->miningPoolService->identifyFromBlock(['auxpow' => $block->auxpow]);
                    if ($pool) {
                        $block->pool_id = $pool->id;
                        $block->save();
                        $tagged++;

                        // Auto-discover historical addresses too
                        $pepeAddress = $block->auxpow['tx']['vout'][0]['scriptPubKey']['addresses'][0] ?? null;
                        $this->miningPoolService->recordPayoutAddress($pool, $pepeAddress);
                    }
                }
                $bar->advance();
            }
        }, 'height');

        $bar->finish();
        $this->newLine();
        $this->info("Tagging completed. {$tagged} blocks tagged.");

        return self::SUCCESS;
    }
}
