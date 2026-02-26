<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PoolStat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        // For history, we usually show daily points even if the toggle is weekly? 
        // Actually, let's just return all daily points for the last month.
        
        $stats = PoolStat::with('pool')
            ->where('type', 'daily')
            ->where('hashrate_timestamp', '>=', now()->subMonth())
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

        return response()->json($stats);
    }
}
