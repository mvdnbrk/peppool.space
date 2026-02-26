<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MiningBlockResource;
use App\Models\Block;
use App\Models\PoolStat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class MiningController extends Controller
{
    public function pools(Request $request): JsonResponse
    {
        $type = $request->query('type', 'daily');
        if (! in_array($type, ['daily', 'weekly'], true)) {
            $type = 'daily';
        }

        // Get the most recent stats for each pool
        $latestTimestamp = PoolStat::where('type', $type)->max('hashrate_timestamp');

        if (! $latestTimestamp) {
            return response()->json([]);
        }

        $stats = PoolStat::with('pool')
            ->where('hashrate_timestamp', $latestTimestamp)
            ->where('type', $type)
            ->orderByDesc('share')
            ->get()
            ->map(fn ($stat) => [
                'poolId' => $stat->pool_id,
                'name' => $stat->pool->name,
                'slug' => $stat->pool->slug,
                'share' => $stat->share,
                'hashrate' => $stat->avg_hashrate,
            ]);

        return response()->json($stats);
    }

    public function hashrate(Request $request): JsonResponse
    {
        $latestTimestamp = PoolStat::where('type', 'daily')->max('hashrate_timestamp');

        if (! $latestTimestamp) {
            return response()->json([]);
        }

        $end = Carbon::parse($latestTimestamp);
        $start = $end->copy()->subMonth();

        $cacheKey = 'mining_hashrate_history_'.md5($start->toDateTimeString().$end->toDateTimeString());

        $data = Cache::remember($cacheKey, 600, function () use ($start, $end) {
            return PoolStat::with('pool')
                ->where('type', 'daily')
                ->whereBetween('hashrate_timestamp', [$start, $end])
                ->orderBy('hashrate_timestamp')
                ->get()
                ->groupBy(fn ($stat) => $stat->hashrate_timestamp->toIso8601String())
                ->map(fn ($group, $timestamp) => [
                    'timestamp' => $timestamp,
                    'pools' => $group->map(fn ($stat) => [
                        'name' => $stat->pool->name,
                        'hashrate' => $stat->avg_hashrate,
                    ]),
                    'totalHashrate' => $group->sum('avg_hashrate'),
                ])
                ->values();
        });

        return response()->json($data);
    }

    public function blocks(): JsonResponse
    {
        return MiningBlockResource::collection(
            Block::query()
                ->with('pool')
                ->orderByDesc('height')
                ->limit(10)
                ->get()
        )->response();
    }
}
