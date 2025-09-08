<?php

declare(strict_types=1);

namespace Tests\Unit\Data\Rpc;

use App\Data\Rpc\BlockchainInfoData;
use Tests\TestCase;

final class BlockchainInfoDataTest extends TestCase
{
    public function test_from_rpc_maps_fields_with_nested_arrays(): void
    {
        $dto = BlockchainInfoData::from([
            'chain' => 'main',
            'blocks' => 695976,
            'headers' => 695976,
            'bestblockhash' => 'f08e1915dd991044b1bfc718d212fe52190a89adc577f8a66bcfd12b00e1d13d',
            'difficulty' => 43406971.21384085,
            'mediantime' => 1757267419,
            'verificationprogress' => 1,
            'initialblockdownload' => false,
            'chainwork' => '000000000000000000000000000000000000000000000babcd9f518f993ee745',
            'size_on_disk' => 5636506125,
            'pruned' => false,
            'softforks' => [
                [
                    'id' => 'bip34',
                    'version' => 2,
                    'reject' => ['status' => true],
                ],
            ],
            'bip9_softforks' => [
                'csv' => [
                    'status' => 'failed',
                    'startTime' => 1462060800,
                    'timeout' => 1493596800,
                    'since' => 10080,
                ],
            ],
            'warnings' => '',
        ]);

        $this->assertSame('main', $dto->chain);
        $this->assertSame(695976, $dto->blocks);
        $this->assertSame(695976, $dto->headers);
        $this->assertSame('f08e1915dd991044b1bfc718d212fe52190a89adc577f8a66bcfd12b00e1d13d', $dto->bestBlockHash);
        $this->assertSame(43406971.21384085, $dto->difficulty);
        $this->assertSame(1757267419, $dto->medianTime);
        $this->assertSame(1.0, $dto->verificationProgress);
        $this->assertFalse($dto->initialBlockDownload);
        $this->assertSame('000000000000000000000000000000000000000000000babcd9f518f993ee745', $dto->chainwork);
        $this->assertSame(5636506125, $dto->sizeOnDisk);
        $this->assertFalse($dto->pruned);
        $this->assertIsArray($dto->softforks);
        $this->assertIsArray($dto->bip9Softforks);
        $this->assertArrayHasKey('csv', $dto->bip9Softforks);
        $this->assertSame('', $dto->warnings);
    }

    public function test_from_rpc_defaults_when_missing(): void
    {
        $dto = BlockchainInfoData::from([]);

        $this->assertSame('', $dto->chain);
        $this->assertSame(0, $dto->blocks);
        $this->assertSame(0, $dto->headers);
        $this->assertSame('', $dto->bestBlockHash);
        $this->assertSame(0.0, $dto->difficulty);
        $this->assertSame(0, $dto->medianTime);
        $this->assertSame(0.0, $dto->verificationProgress);
        $this->assertFalse($dto->initialBlockDownload);
        $this->assertSame('', $dto->chainwork);
        $this->assertSame(0, $dto->sizeOnDisk);
        $this->assertFalse($dto->pruned);
        $this->assertSame([], $dto->softforks);
        $this->assertSame([], $dto->bip9Softforks);
        $this->assertSame('', $dto->warnings);
    }
}
