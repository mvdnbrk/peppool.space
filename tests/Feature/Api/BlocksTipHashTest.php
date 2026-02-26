<?php

namespace Tests\Feature\Api;

use App\Contracts\BlockchainServiceInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlocksTipHashTest extends TestCase
{
    #[Test]
    public function tip_hash_returns_plain_text_hash(): void
    {
        $mock = Mockery::mock(BlockchainServiceInterface::class);
        $mock->shouldReceive('getBlockTipHash')->once()
            ->andReturn(str_repeat('a', 64));
        $this->app->instance(BlockchainServiceInterface::class, $mock);

        $this->get(route('api.blocks.tip.hash'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee(str_repeat('a', 64));
    }
}
