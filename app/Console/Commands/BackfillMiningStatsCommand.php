<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Pool;
use App\Models\PoolStat;
use App\Services\PepecoinExplorerService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class BackfillMiningStatsCommand extends Command
{
    protected $signature = 'pepe:mining:backfill-stats
                            {--days=30 : Number of days to backfill}';

    protected $description = 'Backfill historical mining pool statistics';

    public function __construct(
        private readonly PepecoinExplorerService $explorer
    ) {
        parent::__construct();
    }

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
        $unknownPool = Pool::firstOrCreate(['slug' => 'unknown'], ['name' => 'Unknown', 'addresses' => [], 'regexes' => []]);

        $totalHours = $days * 24;
        $bar = $this->output->createProgressBar($totalHours);

        for ($i = $totalHours; $i >= 0; $i--) {
            $end = $referenceTime->copy()->subHours($i);

            // Calculate daily stats (24h window ending at this hour)
            $this->calculateForType('daily', $end->copy()->subDay(), $end, $unknownPool->id);

            // Calculate weekly stats (7d window ending at this hour) once a day to save time
            if ($i % 24 === 0) {
                $this->calculateForType('weekly', $end->copy()->subWeek(), $end, $unknownPool->id);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Backfill completed.');

        return self::SUCCESS;
    }

    private function calculateForType(string $type, Carbon $start, Carbon $end, int $unknownPoolId): void
    {
        $blocksInWindow = Block::whereBetween('created_at', [$start, $end])->get();
        $count = $blocksInWindow->count();

        if ($count === 0) {
            return;
        }

        // Formula: (Difficulty * 2^32 * BlockCount) / TimeInSeconds
        $avgDifficulty = (float) $blocksInWindow->avg('difficulty');
        $timeWindowSeconds = (float) abs($end->diffInSeconds($start));

        $totalHashrate = ($avgDifficulty * 4294967296.0 * $count) / $timeWindowSeconds;

        $poolCounts = $blocksInWindow->groupBy('pool_id');

        foreach ($poolCounts as $poolId => $blocks) {
            $poolId = $poolId ?: $unknownPoolId;
            $share = $blocks->count() / $count;
            $poolHashrate = $share * $totalHashrate;

            PoolStat::updateOrCreate(
                [
                    'hashrate_timestamp' => $end,
                    'pool_id' => $poolId,
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
