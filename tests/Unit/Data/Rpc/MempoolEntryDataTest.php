<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\MempoolEntryData;
use Tests\TestCase;

final class MempoolEntryDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = MempoolEntryData::from([
            'size' => 123,
            'fee' => 0.42,
            'modifiedfee' => 0.43,
            'time' => 111,
            'height' => 222,
            'startingpriority' => 1.5,
            'currentpriority' => 1.4,
            'descendantcount' => 3,
            'descendantsize' => 456,
            'descendantfees' => 789,
            'ancestorcount' => 2,
            'ancestorsize' => 321,
            'ancestorfees' => 654,
            'depends' => ['tx1', 'tx2'],
        ]);

        $this->assertSame(123, $dto->size);
        $this->assertSame(0.42, $dto->fee);
        $this->assertSame(0.43, $dto->modifiedFee);
        $this->assertSame(111, $dto->time);
        $this->assertSame(222, $dto->height);
        $this->assertSame(1.5, $dto->startingPriority);
        $this->assertSame(1.4, $dto->currentPriority);
        $this->assertSame(3, $dto->descendantCount);
        $this->assertSame(456, $dto->descendantSize);
        $this->assertSame(789, $dto->descendantFees);
        $this->assertSame(2, $dto->ancestorCount);
        $this->assertSame(321, $dto->ancestorSize);
        $this->assertSame(654, $dto->ancestorFees);
        $this->assertSame(['tx1', 'tx2'], $dto->depends);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = MempoolEntryData::from([]);

        $this->assertSame(0, $dto->size);
        $this->assertSame(0.0, $dto->fee);
        $this->assertSame(0.0, $dto->modifiedFee);
        $this->assertSame(0, $dto->time);
        $this->assertSame(0, $dto->height);
        $this->assertSame(0.0, $dto->startingPriority);
        $this->assertSame(0.0, $dto->currentPriority);
        $this->assertSame(0, $dto->descendantCount);
        $this->assertSame(0, $dto->descendantSize);
        $this->assertSame(0, $dto->descendantFees);
        $this->assertSame(0, $dto->ancestorCount);
        $this->assertSame(0, $dto->ancestorSize);
        $this->assertSame(0, $dto->ancestorFees);
        $this->assertSame([], $dto->depends);
    }
}
