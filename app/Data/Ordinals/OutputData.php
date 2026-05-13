<?php

declare(strict_types=1);

namespace App\Data\Ordinals;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class OutputData extends Data
{
    public function __construct(
        public ?string $address,
        public int $confirmations,
        public bool $indexed,
        public array $inscriptions,
        public string $outpoint,
        public string $script_pubkey,
        public bool $spent,
        public string $transaction,
        public int $value,
    ) {}

    public function hasInscriptions(): bool
    {
        return ! empty($this->inscriptions);
    }

    public function getInscriptions(): Collection
    {
        return collect($this->inscriptions);
    }

    public function isSpent(): bool
    {
        return $this->spent;
    }

    public function isIndexed(): bool
    {
        return $this->indexed;
    }
}
