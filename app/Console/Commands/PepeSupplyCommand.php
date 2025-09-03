<?php

namespace App\Console\Commands;

use App\Jobs\CalculateTotalSupply;
use App\Services\PepecoinExplorerService;
use Illuminate\Console\Command;
use Illuminate\Support\Number;

class PepeSupplyCommand extends Command
{
    protected $signature = 'pepe:supply {--refresh : Recalculate and refresh the cached value}';

    protected $description = 'Calculate and display total PEPE supply (sum of coinbase outputs) and cache the value.';

    public function __construct(private readonly PepecoinExplorerService $explorer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('refresh')) {
            $this->info('Refreshing cached total supply...');
            // Reuse the job's logic to compute & cache
            CalculateTotalSupply::dispatchSync();
        }

        // Read via service (will use cache, or compute and cache if missing)
        $sumStr = $this->explorer->getTotalSupply(false);
        $sumInt = (int) $sumStr;

        $human = Number::abbreviate($sumInt, maxPrecision: 0);

        $this->line('Total PEPE supply');
        $this->line('-------------------');
        $this->line('Full:   '.Number::format($sumInt, maxPrecision: 0));
        $this->line('Approx: '.$human);

        return self::SUCCESS;
    }
}
