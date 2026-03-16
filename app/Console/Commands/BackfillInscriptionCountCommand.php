<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\FetchBlockInscriptionCount;
use App\Models\Block;
use Illuminate\Console\Command;

class BackfillInscriptionCountCommand extends Command
{
    protected $signature = 'pepe:backfill:inscription-counts
                            {--limit=100 : Number of blocks to process per run}';

    protected $description = 'Backfill inscription counts for blocks where count is null';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $blocks = Block::whereNull('inscription_count')
            ->orderByDesc('height')
            ->limit($limit)
            ->pluck('height');

        if ($blocks->isEmpty()) {
            $this->info('No blocks to backfill.');

            return self::SUCCESS;
        }

        foreach ($blocks as $height) {
            FetchBlockInscriptionCount::dispatch($height);
        }

        $this->info("Dispatched {$blocks->count()} inscription count jobs.");

        return self::SUCCESS;
    }
}
