<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Pool;
use App\Models\PoolStat;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class BackfillMiningStatsCommand extends Command
{
    protected $signature = 'pepe:mining:backfill-stats
                            {--days=30 : Number of days to backfill}';

    protected $description = 'Backfill historical mining pool statistics';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Starting backfill for the last {$days} days...");

        $startDate = now()->subDays($days)->startOfHour();

        $startHeight = Block::where('created_at', '>=', $startDate)->min('height');

        if ($startHeight === null) {
            $this->error('Could not find a starting block for the requested date range.');

            return self::FAILURE;
        }

        $latestHeight = Block::max('height');

        $this->info("Fetching blocks from height {$startHeight} to {$latestHeight}...");

        $blocks = Block::query()
            ->where('height', '>=', $startHeight)
            ->orderBy('height', 'asc')
            ->get();

        $this->info('Processing '.number_format($blocks->count()).' blocks in memory...');

        $unknownPoolId = Pool::where('slug', 'unknown')->first()?->id ?? 1;

        // Group blocks by hour
        $groupedByHour = $blocks->groupBy(function (Block $block) {
            return $block->created_at->startOfHour()->toDateTimeString();
        });

        $bar = $this->output->createProgressBar($groupedByHour->count());

        foreach ($groupedByHour as $hourStr => $hourBlocks) {
            $endOfHour = Carbon::parse($hourStr)->addHour();
            $this->calculateStatsForGroup($hourBlocks, $endOfHour, $unknownPoolId);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Backfill completed successfully.');

        return self::SUCCESS;
    }

    private function calculateStatsForGroup($blocks, Carbon $endOfHour, int $unknownPoolId): void
    {
        $count = $blocks->count();
        if ($count === 0) {
            return;
        }

        // Use 1 hour (3600s) as the window for these snapshots
        $timeWindow = 3600;
        $avgDifficulty = (float) $blocks->avg('difficulty');
        $networkHashrate = Block::estimateHashrate($avgDifficulty, $count, $timeWindow);

        $poolCounts = $blocks->groupBy('pool_id');

        foreach ($poolCounts as $poolId => $poolBlocks) {
            $effectivePoolId = $poolId ?: $unknownPoolId;
            $share = $poolBlocks->count() / $count;
            $poolHashrate = $share * $networkHashrate;

            PoolStat::updateOrCreate(
                [
                    'hashrate_timestamp' => $endOfHour,
                    'pool_id' => $effectivePoolId,
                    'type' => 'daily', // Chart default
                ],
                [
                    'avg_hashrate' => $poolHashrate,
                    'share' => $share,
                ]
            );
        }
    }
}
