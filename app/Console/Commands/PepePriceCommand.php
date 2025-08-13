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

        $this->info('The current PEPE price is '.Cache::get('pepe_price', default: 'unknown'));
    }
}
