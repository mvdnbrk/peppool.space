<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MiningBlockResource;
use App\Models\Block;
use App\Models\Pool;
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
        $type = $request->query('type', 'daily');
        if (! in_array($type, ['daily', 'weekly'], true)) {
            $type = 'daily';
        }

        $latestTimestamp = PoolStat::where('type', $type)->max('hashrate_timestamp');

        if (! $latestTimestamp) {
            return response()->json([]);
        }

        $end = Carbon::parse($latestTimestamp);
        $start = $end->copy()->subMonth();

        $cacheKey = 'mining_hashrate_history_'.md5($start->toDateTimeString().$end->toDateTimeString().$type);

        $data = Cache::remember($cacheKey, 600, function () use ($start, $end, $type) {
            return PoolStat::with('pool')
                ->where('type', $type)
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
                ->values()
                ->all();
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

    public function pool(string $slug, Request $request): JsonResponse
    {
        $pool = Pool::where('slug', $slug)->firstOrFail();

        $type = $request->query('type', 'daily');
        if (! in_array($type, ['daily', 'weekly'], true)) {
            $type = 'daily';
        }

        $latestTimestamp = PoolStat::where('pool_id', $pool->id)
            ->where('type', $type)
            ->max('hashrate_timestamp');

        $latestStat = $latestTimestamp ? PoolStat::where('pool_id', $pool->id)
            ->where('hashrate_timestamp', $latestTimestamp)
            ->where('type', $type)
            ->first() : null;

        $recentBlocks = Block::where('pool_id', $pool->id)
            ->orderByDesc('height')
            ->paginate(25);

        $end = $latestTimestamp ? Carbon::parse($latestTimestamp) : now();
        $start = $end->copy()->subMonth();

        $hashrateHistory = PoolStat::where('pool_id', $pool->id)
            ->where('type', $type)
            ->whereBetween('hashrate_timestamp', [$start, $end])
            ->orderBy('hashrate_timestamp')
            ->get()
            ->map(fn ($stat) => [
                'time' => $stat->hashrate_timestamp->timestamp,
                'value' => (float) $stat->avg_hashrate,
            ]);

        return response()->json([
            'pool' => [
                'name' => $pool->name,
                'slug' => $pool->slug,
                'link' => $pool->link,
                'addresses' => $pool->addresses,
            ],
            'latestStat' => $latestStat ? [
                'hashrate' => (float) $latestStat->avg_hashrate,
                'share' => (float) $latestStat->share,
                'timestamp' => $latestStat->hashrate_timestamp->timestamp,
            ] : null,
            'blocks' => MiningBlockResource::collection($recentBlocks)->response()->getData(true),
            'hashrateHistory' => $hashrateHistory,
        ]);
    }
}
