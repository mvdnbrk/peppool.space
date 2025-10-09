<?php

namespace App\Console\Commands;

use App\Jobs\CalculateTotalSupply;
use App\Services\PepecoinPriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Number;

class PepeSupplyCommand extends Command
{
    protected $signature = 'pepe:supply';

    protected $description = 'Calculate and display total PEPE supply.';

    public function __construct(private readonly PepecoinPriceService $prices)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Calculating total PEPE supply...');
        // Always calculate fresh supply data
        CalculateTotalSupply::dispatchSync();

        // Read the freshly calculated supply directly from cache
        $totalSupply = $this->prices->getTotalSupply();

        $this->line('Total PEPE supply');
        $this->line('-------------------');
        $this->line('Full:   '.Number::format($totalSupply, maxPrecision: 0));
        $this->line('Approx: '.Number::abbreviate($totalSupply, maxPrecision: 0));

        return self::SUCCESS;
    }
}
