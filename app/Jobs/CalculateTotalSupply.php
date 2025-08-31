<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PepecoinRpcService;

class CalculateTotalSupply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120; // seconds

    public function handle(): void
    {
        $cacheKey = 'pepe:total_supply';

        // 1) Try RPC truth source first (aligns with CMC): gettxoutsetinfo.total_amount
        try {
            /** @var PepecoinRpcService $rpc */
            $rpc = app(PepecoinRpcService::class);
            $info = $rpc->getTxOutSetInfo();
            if (is_array($info) && array_key_exists('total_amount', $info)) {
                $total = (string) $info['total_amount'];
                Cache::put($cacheKey, $total, now()->addHours(1));
                return;
            }
        } catch (\Throwable $e) {
            Log::warning('CalculateTotalSupply RPC path failed, falling back to DB sum', [
                'error' => $e->getMessage(),
            ]);
        }

        // 2) Fallback: Sum all outputs from coinbase transactions in our DB
        $sum = DB::table('transaction_outputs as o')
            ->join('transactions as t', 't.tx_id', '=', 'o.tx_id')
            ->where('t.is_coinbase', true)
            ->sum('o.amount');

        Cache::put($cacheKey, (string) $sum, now()->addHours(1));
    }
}
