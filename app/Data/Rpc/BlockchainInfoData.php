<?php

declare(strict_types=1);

namespace App\Data\Rpc;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class BlockchainInfoData extends Data
{
    public function __construct(
        public string $chain = '',
        public int $blocks = 0,
        public int $headers = 0,
        #[MapInputName('bestblockhash')]
        public string $bestBlockHash = '',
        public float $difficulty = 0.0,
        #[MapInputName('mediantime')]
        public int $medianTime = 0,
        #[MapInputName('verificationprogress')]
        public float $verificationProgress = 0.0,
        #[MapInputName('initialblockdownload')]
        public bool $initialBlockDownload = false,
        public string $chainwork = '',
        #[MapInputName('size_on_disk')]
        public int $sizeOnDisk = 0,
        public bool $pruned = false,
        #[DataCollectionOf(SoftforkData::class)]
        public array $softforks = [],
        #[MapInputName('bip9_softforks')]
        /** @var array<string, array<string, mixed>> */
        public array $bip9Softforks = [],
        public string $warnings = '',
    ) {}
}
