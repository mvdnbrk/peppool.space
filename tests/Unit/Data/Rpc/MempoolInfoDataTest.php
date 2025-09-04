<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\MempoolInfoData;
use Tests\TestCase;

final class MempoolInfoDataTest extends TestCase
{
    public function test_from_rpc_maps_fields(): void
    {
        $dto = MempoolInfoData::from([
            'size' => 10,
            'bytes' => 1000,
            'usage' => 2000,
            'maxmempool' => 3000,
            'mempoolminfee' => 0.0001,
        ]);

        $this->assertSame(10, $dto->size);
        $this->assertSame(1000, $dto->bytes);
        $this->assertSame(2000, $dto->usage);
        $this->assertSame(3000, $dto->maxMempool);
        $this->assertSame(0.0001, $dto->mempoolMinFee);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = MempoolInfoData::from([]);

        $this->assertSame(0, $dto->size);
        $this->assertSame(0, $dto->bytes);
        $this->assertSame(0, $dto->usage);
        $this->assertSame(0, $dto->maxMempool);
        $this->assertSame(0.0, $dto->mempoolMinFee);
    }
}
