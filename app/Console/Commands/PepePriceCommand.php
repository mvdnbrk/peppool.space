<?php

namespace App\Console\Commands;

use App\Jobs\FetchPepePrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PepePriceCommand extends Command
{
    protected $signature = 'pepe:price';

    protected $description = 'Show the current PEPE price';

    public function handle()
    {
        FetchPepePrice::dispatchSync();

        $this->info('The current PEPE price in USD is '.Cache::get('pepecoin_price_usd', default: 'unknown'));
        $this->info('The current PEPE price in EUR is '.Cache::get('pepecoin_price_eur', default: 'unknown'));
    }
}
