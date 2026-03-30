<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Block;
use App\Services\PepecoinRpcService;
use Illuminate\Console\Command;

class BackfillPreviousHashCommand extends Command
{
    protected $signature = 'pepe:backfill:previous-hash
                            {--limit=5000 : Number of blocks to process per run}
                            {--all : Process all blocks (ignores --limit)}';

    protected $description = 'Backfill previous_hash for blocks where it is null';

    private bool $shouldStop = false;

    public function handle(PepecoinRpcService $rpc): int
    {
        $this->trap([SIGINT, SIGTERM], function () {
            $this->shouldStop = true;
            $this->newLine();
            $this->warn('Received shutdown signal. Finishing current batch...');
        });

        $remaining = Block::whereNull('previous_hash')
            ->where('height', '>', 0)
            ->count();

        if ($remaining === 0) {
            $this->info('No blocks to backfill.');

            return self::SUCCESS;
        }

        $limit = $this->option('all') ? $remaining : (int) $this->option('limit');

        $this->info("Backfilling previous_hash for {$remaining} blocks".($this->option('all') ? '' : " (limit: {$limit})").'...');

        $progressBar = $this->output->createProgressBar(min($limit, $remaining));
        $progressBar->setFormat('Backfilling: %current%/%max% [%bar%] %percent:3s%%');
        $progressBar->start();

        $processed = 0;

        while ($processed < $limit && ! $this->shouldStop) {
            $batchSize = min(250, $limit - $processed);

            $blocks = Block::whereNull('previous_hash')
                ->where('height', '>', 0)
                ->orderByDesc('height')
                ->limit($batchSize)
                ->pluck('height');

            if ($blocks->isEmpty()) {
                break;
            }

            foreach ($blocks as $height) {
                if ($this->shouldStop) {
                    break;
                }

                try {
                    $hash = $rpc->getBlockHash($height);
                    $blockData = $rpc->getBlock($hash);
                    $previousHash = $blockData['previousblockhash'] ?? null;

                    if ($previousHash) {
                        Block::where('height', $height)->update(['previous_hash' => $previousHash]);
                    }
                } catch (\Throwable $e) {
                    $this->warn("Failed to backfill block {$height}: {$e->getMessage()}");
                }

                $progressBar->advance();
                $processed++;
            }
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Processed {$processed} blocks.");

        return self::SUCCESS;
    }
}
