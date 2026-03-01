<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CalculateMiningStats;
use App\Models\Block;
use Illuminate\Console\Command;

class BackfillMiningStatsCommand extends Command
{
    protected $signature = 'pepe:mining:backfill-stats
                            {--days=30 : Number of days to backfill}';

    protected $description = 'Backfill historical mining pool statistics';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Backfilling mining stats for the last {$days} days...");

        $latestBlock = Block::orderBy('height', 'desc')->first();
        if (! $latestBlock) {
            $this->error('No blocks found in database.');

            return self::FAILURE;
        }

        $referenceTime = $latestBlock->created_at->startOfHour();
        $totalHours = $days * 24;
        $bar = $this->output->createProgressBar($totalHours);

        for ($i = $totalHours; $i >= 0; $i--) {
            $end = $referenceTime->copy()->subHours($i);

            CalculateMiningStats::dispatchSync($end);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Backfill completed.');

        return self::SUCCESS;
    }
}
