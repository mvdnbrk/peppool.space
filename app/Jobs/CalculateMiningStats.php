<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Block;
use App\Models\Pool;
use App\Models\PoolStat;
use App\Services\PepecoinExplorerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CalculateMiningStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(PepecoinExplorerService $explorer): void
    {
        $latestBlock = Block::orderBy('height', 'desc')->first();
        if (! $latestBlock) {
            return;
        }

        $referenceTime = $latestBlock->created_at;
        $unknownPool = Pool::where('slug', 'unknown')->first() ?? Pool::create(['slug' => 'unknown', 'name' => 'Unknown', 'addresses' => [], 'regexes' => []]);

        $this->calculateForType('daily', $referenceTime->copy()->subDay(), $referenceTime, $explorer, $unknownPool->id);
        $this->calculateForType('weekly', $referenceTime->copy()->subWeek(), $referenceTime, $explorer, $unknownPool->id);
    }

    private function calculateForType(string $type, Carbon $start, Carbon $end, PepecoinExplorerService $explorer, int $unknownPoolId): void
    {
        $totalBlocks = Block::whereBetween('created_at', [$start, $end])->count();
        if ($totalBlocks === 0) {
            return;
        }

        $timeWindowSeconds = (float) abs($end->diffInSeconds($start));
        if ($timeWindowSeconds === 0.0) {
            return;
        }

        $avgDifficulty = (float) Block::whereBetween('created_at', [$start, $end])->avg('difficulty');
        $networkHashrate = ($avgDifficulty * 4294967296.0 * $totalBlocks) / $timeWindowSeconds;

        $poolCounts = Block::query()
            ->select('pool_id', DB::raw('count(*) as block_count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('pool_id')
            ->get();

        foreach ($poolCounts as $poolCount) {
            $poolId = $poolCount->pool_id ?? $unknownPoolId;
            $share = $poolCount->block_count / $totalBlocks;
            $poolHashrate = $share * $networkHashrate;

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
