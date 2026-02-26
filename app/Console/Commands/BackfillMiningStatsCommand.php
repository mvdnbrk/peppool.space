<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Pool;
use App\Models\PoolStat;
use App\Services\PepecoinExplorerService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

        $referenceTime = $latestBlock->created_at;
        $unknownPool = Pool::firstOrCreate(['slug' => 'unknown'], ['name' => 'Unknown', 'addresses' => [], 'regexes' => []]);

        $bar = $this->output->createProgressBar($days);

        for ($i = $days; $i >= 0; $i--) {
            $end = $referenceTime->copy()->subDays($i)->endOfDay();
            
            // Daily
            $this->calculateForType('daily', $end->copy()->subDay(), $end, $unknownPool->id);
            
            // Weekly (only once a day is fine)
            $this->calculateForType('weekly', $end->copy()->subWeek(), $end, $unknownPool->id);
            
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

        // Estimate hashrate for this window
        // Formula: Difficulty * 2^32 / TimeInSeconds
        $avgDifficulty = $blocksInWindow->avg('difficulty');
        $timeWindowSeconds = $end->diffInSeconds($start);
        
        $totalHashrate = ($avgDifficulty * 4294967296 * $count) / $timeWindowSeconds;

        $poolCounts = $blocksInWindow->groupBy('pool_id');

        foreach ($poolCounts as $poolId => $blocks) {
            $poolId = $poolId ?: $unknownPoolId;
            $share = $blocks->count() / $count;
            $poolHashrate = $share * $totalHashrate;

            PoolStat::updateOrCreate(
                [
                    'hashrate_timestamp' => $end->startOfHour(),
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
