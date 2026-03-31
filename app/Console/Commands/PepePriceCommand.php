<?php

namespace App\Console\Commands;

use App\Jobs\FetchPepePrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PepePriceCommand extends Command
{
    protected $signature = 'pepe:price';

    protected $description = 'Fetch and store the current PEPE price';

    public function handle(): void
    {
        FetchPepePrice::dispatchSync();

        $this->info('USD: '.Cache::get('pepecoin_price_usd', 'unknown'));
        $this->info('EUR: '.Cache::get('pepecoin_price_eur', 'unknown'));
    }
}
