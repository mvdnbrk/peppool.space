<?php

declare(strict_types=1);

namespace App\Data\Electrs;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class AddressData extends Data
{
    public function __construct(
        public string $address,
        #[MapInputName('chain_stats')]
        public StatsData $chainStats,
        #[MapInputName('mempool_stats')]
        public StatsData $mempoolStats,
    ) {}

    public function getConfirmedBalance(): float
    {
        return $this->chainStats->getBalance();
    }

    public function getMempoolBalance(): float
    {
        return $this->mempoolStats->getBalance();
    }

    public function getTotalBalance(): float
    {
        return $this->getConfirmedBalance() + $this->getMempoolBalance();
    }
}
