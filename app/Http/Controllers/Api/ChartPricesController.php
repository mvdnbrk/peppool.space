<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChartPricesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $period = (string) $request->get('period', '24h');
        $currency = (string) $request->get('currency', 'USD');

        // Basic validation
        if (! in_array($currency, ['USD', 'EUR'], true)) {
            $currency = 'USD';
        }

        $startTime = $this->getStartTimeForPeriod($period);

        $query = Price::query()
            ->where('currency', $currency)
            ->when($startTime !== null, fn ($q) => $q->where('created_at', '>=', $startTime))
            ->orderBy('created_at');

        // Sampling to keep payload small
        $maxPoints = match ($period) {
            '1h' => 60,
            '24h' => 288,
            '7d' => 336,
            '30d' => 720,
            '1y' => 365,
            'all' => 1000,
            default => 288,
        };

        $count = (clone $query)->count();

        if ($count === 0 && $startTime !== null) {
            // Fallback: no data in the requested window, return the latest N points
            $prices = Price::query()
                ->where('currency', $currency)
                ->orderByDesc('created_at')
                ->take($maxPoints)
                ->get()
                ->reverse() // chronological order
                ->values()
                ->map(function ($p) {
                    return [
                        'time' => $p->created_at?->timestamp ?? Carbon::now()->timestamp,
                        'value' => (float) $p->price,
                    ];
                })
                ->toArray();
        } else {
            $interval = max(1, (int) floor($count / max(1, $maxPoints)));
            $prices = $query->get()->filter(function ($item, $idx) use ($interval) {
                return $idx % $interval === 0;
            })->values()->map(function ($p) {
                return [
                    'time' => $p->created_at?->timestamp ?? Carbon::now()->timestamp,
                    'value' => (float) $p->price,
                ];
            })->toArray();
        }

        return response()->json([
            'success' => true,
            'series' => $prices,
            'period' => $period,
            'currency' => $currency,
            'count' => count($prices),
        ]);
    }

    private function getStartTimeForPeriod(string $period): ?Carbon
    {
        return match ($period) {
            '1h' => Carbon::now()->subHour(),
            '24h' => Carbon::now()->subDay(),
            '7d' => Carbon::now()->subWeek(),
            '30d' => Carbon::now()->subMonth(),
            '1y' => Carbon::now()->subYear(),
            'all' => null,
            default => Carbon::now()->subDay(),
        };
    }
}
