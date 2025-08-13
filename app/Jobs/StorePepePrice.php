<?php

namespace App\Jobs;

use App\Models\Price;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class StorePepePrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private array $currencies = [
        'usd',
        'eur',
    ];

    public function handle(): void
    {
        $timestamp = $this->getTimestamp();

        foreach ($this->currencies as $currency) {
            $price = Cache::get("pepecoin_price_{$currency}");

            if ($price === null) {
                continue;
            }

            Price::updateOrCreate([
                'currency' => $currency,
                'created_at' => $timestamp,
            ], [
                'price' => $price,
            ]);
        }
    }

    private function getTimestamp(): string
    {
        return Carbon::now()
            ->startOfHour()
            ->addMinutes(
                15 * floor(Carbon::now()->minute / 15)
            );
    }
}
