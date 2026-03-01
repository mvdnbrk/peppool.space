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
        $this->info("Starting rolling backfill for the last {$days} days...");

        $now = now()->startOfHour();
        $targetStart = $now->copy()->subDays($days);
        $fetchStart = $targetStart->copy()->subDays(7); // 7-day buffer for the first weekly rolling point

        $startHeight = Block::where('created_at', '>=', $fetchStart)->min('height');

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

        // Group blocks by Y-m-d H:00:00 for fast window aggregation
        $blocksByHour = $blocks->groupBy(function (Block $block) {
            return $block->created_at->format('Y-m-d H:00:00');
        });

        $totalHours = $days * 24;
        $bar = $this->output->createProgressBar($totalHours + 1);

        for ($i = $totalHours; $i >= 0; $i--) {
            $windowEnd = $targetStart->copy()->addHours($totalHours - $i);

            // 1. Calculate Daily (24h) rolling average
            $dailyBlocks = collect();
            for ($j = 0; $j < 24; $j++) {
                $hourKey = $windowEnd->copy()->subHours($j)->format('Y-m-d H:00:00');
                if ($blocksByHour->has($hourKey)) {
                    $dailyBlocks = $dailyBlocks->concat($blocksByHour->get($hourKey));
                }
            }
            $this->calculateStatsForWindow($dailyBlocks, $windowEnd, $unknownPoolId, 'daily', 86400);

            // 2. Calculate Weekly (7d) rolling average
            $weeklyBlocks = collect();
            for ($j = 0; $j < 168; $j++) {
                $hourKey = $windowEnd->copy()->subHours($j)->format('Y-m-d H:00:00');
                if ($blocksByHour->has($hourKey)) {
                    $weeklyBlocks = $weeklyBlocks->concat($blocksByHour->get($hourKey));
                }
            }
            $this->calculateStatsForWindow($weeklyBlocks, $windowEnd, $unknownPoolId, 'weekly', 604800);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Rolling backfill completed successfully.');

        return self::SUCCESS;
    }

    private function calculateStatsForWindow($blocks, Carbon $endTimestamp, int $unknownPoolId, string $type, int $seconds): void
    {
        $count = $blocks->count();
        if ($count === 0) {
            return;
        }

        $avgDifficulty = (float) $blocks->avg('difficulty');
        $networkHashrate = Block::estimateHashrate($avgDifficulty, $count, $seconds);

        $poolCounts = $blocks->groupBy('pool_id');

        foreach ($poolCounts as $poolId => $poolBlocks) {
            $effectivePoolId = $poolId ?: $unknownPoolId;
            $share = $poolBlocks->count() / $count;
            $poolHashrate = $share * $networkHashrate;

            PoolStat::updateOrCreate(
                [
                    'hashrate_timestamp' => $endTimestamp,
                    'pool_id' => $effectivePoolId,
                    'type' => $type,
                ],
                [
                    'avg_hashrate' => $poolHashrate,
                    'share' => $share,
                ]
            );
        }
    }
}
